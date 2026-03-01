<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PleskService
{
    protected string $restUrl;
    protected string $xmlrpcUrl;
    protected string $apiKey;
    protected bool $verifySsl;
    protected int $cacheTtl;

    protected const ARTISAN_WHITELIST = [
        'cache:clear',
        'config:clear',
        'route:clear',
        'view:clear',
        'optimize:clear',
        'migrate --force',
        'down',
        'up',
        'storage:link',
    ];

    public function __construct()
    {
        $this->restUrl = config('plesk.rest_url', '');
        $this->xmlrpcUrl = config('plesk.xmlrpc_url', '');
        $this->apiKey = config('plesk.api_key', '');
        $this->verifySsl = config('plesk.verify_ssl', false);
        $this->cacheTtl = config('plesk.cache_ttl', 300);
    }

    protected function restClient()
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->withOptions([
            'verify' => $this->verifySsl,
        ])->baseUrl($this->restUrl);
    }

    protected function xmlrpcRequest(string $xml): ?array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'text/xml',
            'KEY' => $this->apiKey,
        ])->withOptions([
            'verify' => $this->verifySsl,
        ])->withBody($xml, 'text/xml')->post($this->xmlrpcUrl);

        if (!$response->successful()) {
            Log::error('Plesk XML-RPC request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        return $this->parseXmlResponse($response->body());
    }

    protected function parseXmlResponse(string $xml): ?array
    {
        try {
            $parsed = simplexml_load_string($xml);
            return json_decode(json_encode($parsed), true);
        } catch (\Exception $e) {
            Log::error('Plesk XML parse error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ── REST API Methods ──

    public function getDomains(): array
    {
        $cached = Cache::get('plesk:domains');
        if ($cached !== null) {
            return $cached;
        }

        if (empty($this->restUrl) || empty($this->apiKey)) {
            throw new \RuntimeException('Plesk API not configured. Set PLESK_HOST and PLESK_API_KEY in .env');
        }

        $response = $this->restClient()->get('/domains');

        if (!$response->successful()) {
            throw new \RuntimeException('Plesk API error: HTTP ' . $response->status() . ' — ' . $response->body());
        }

        $domains = $response->json() ?? [];
        Cache::put('plesk:domains', $domains, $this->cacheTtl);

        return $domains;
    }

    public function getDomain(string $name): ?array
    {
        $domains = $this->getDomains();

        foreach ($domains as $domain) {
            if (($domain['name'] ?? '') === $name) {
                return $domain;
            }
        }

        return null;
    }

    public function getServerInfo(): ?array
    {
        $cached = Cache::get('plesk:server');
        if ($cached !== null) {
            return $cached;
        }

        $response = $this->restClient()->get('/server');

        if (!$response->successful()) {
            return null;
        }

        $server = $response->json();
        Cache::put('plesk:server', $server, $this->cacheTtl);

        return $server;
    }

    // ── XML-RPC Methods ──

    public function getDomainDetail(string $name): ?array
    {
        return Cache::remember("plesk:domain_detail:{$name}", $this->cacheTtl, function () use ($name) {
            $xml = <<<XML
            <packet>
                <webspace>
                    <get>
                        <filter>
                            <name>{$name}</name>
                        </filter>
                        <dataset>
                            <gen_info/>
                            <hosting/>
                            <stat/>
                            <disk_usage/>
                        </dataset>
                    </get>
                </webspace>
            </packet>
            XML;

            $result = $this->xmlrpcRequest($xml);

            if (!$result) {
                return null;
            }

            return $result['webspace']['get']['result'] ?? null;
        });
    }

    public function getDatabases(string $name): array
    {
        return Cache::remember("plesk:databases:{$name}", $this->cacheTtl, function () use ($name) {
            $domain = $this->getDomain($name);
            $webspaceId = $domain['id'] ?? null;

            if (!$webspaceId) {
                return [];
            }

            $xml = <<<XML
            <packet>
                <database>
                    <get-db>
                        <filter>
                            <webspace-id>{$webspaceId}</webspace-id>
                        </filter>
                    </get-db>
                </database>
            </packet>
            XML;

            $result = $this->xmlrpcRequest($xml);

            if (!$result) {
                return [];
            }

            $dbResult = $result['database']['get-db']['result'] ?? null;

            if (!$dbResult) {
                return [];
            }

            // Normalize: single result vs multiple
            if (isset($dbResult['id'])) {
                return [$dbResult];
            }

            return array_values($dbResult);
        });
    }

    // ── CLI Methods (REST first, XML-RPC fallback) ──

    protected function runCliCommand(string $command, string $workingDir): ?string
    {
        // Try REST CLI endpoint first
        try {
            $response = $this->restClient()->post('/cli/commands', [
                'command' => 'bash',
                'args' => ['-c', "cd {$workingDir} && {$command}"],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['stdout'] ?? $data['output'] ?? '';
            }
        } catch (\Exception $e) {
            Log::info('Plesk REST CLI not available, trying XML-RPC', ['error' => $e->getMessage()]);
        }

        // Fallback to XML-RPC server extension
        try {
            $fullCommand = "cd {$workingDir} && {$command}";
            $escapedCommand = htmlspecialchars($fullCommand, ENT_XML1, 'UTF-8');

            $xml = <<<XML
            <packet>
                <server>
                    <shell_exec>
                        <cmd>{$escapedCommand}</cmd>
                    </shell_exec>
                </server>
            </packet>
            XML;

            $result = $this->xmlrpcRequest($xml);

            if ($result) {
                return $result['server']['shell_exec']['result']['stdout']
                    ?? $result['server']['shell_exec']['result']['output']
                    ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Plesk XML-RPC CLI failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    protected function getDocRoot(string $name): ?string
    {
        $detail = $this->getDomainDetail($name);

        $docRoot = $detail['data']['hosting']['vrt_hst']['property'] ?? null;

        if (is_array($docRoot)) {
            foreach ((isset($docRoot['name']) ? [$docRoot] : $docRoot) as $prop) {
                if (($prop['name'] ?? '') === 'www_root') {
                    return $prop['value'];
                }
            }
        }

        // Fallback: try REST domain info
        $domain = $this->getDomain($name);
        return $domain['www_root'] ?? $domain['hosting']['www_root'] ?? "/var/www/vhosts/{$name}/httpdocs";
    }

    public function getGitCommits(string $name, int $limit = 10): array
    {
        $docRoot = $this->getDocRoot($name);

        if (!$docRoot) {
            return [];
        }

        $format = '%H|||%h|||%s|||%an|||%ar';
        $output = $this->runCliCommand(
            "git log --oneline --format='{$format}' -n {$limit} 2>/dev/null",
            $docRoot
        );

        if (!$output || trim($output) === '') {
            return [];
        }

        $commits = [];
        foreach (explode("\n", trim($output)) as $line) {
            $parts = explode('|||', $line);
            if (count($parts) >= 5) {
                $commits[] = [
                    'hash' => trim($parts[0]),
                    'short_hash' => trim($parts[1]),
                    'message' => trim($parts[2]),
                    'author' => trim($parts[3]),
                    'date' => trim($parts[4]),
                ];
            }
        }

        return $commits;
    }

    public function gitPull(string $name): ?string
    {
        $docRoot = $this->getDocRoot($name);

        if (!$docRoot) {
            return null;
        }

        $this->clearCache($name);

        return $this->runCliCommand('git pull 2>&1', $docRoot);
    }

    public function runArtisan(string $name, string $command): ?string
    {
        if (!in_array($command, self::ARTISAN_WHITELIST)) {
            return "Command not allowed: {$command}";
        }

        $docRoot = $this->getDocRoot($name);

        if (!$docRoot) {
            return null;
        }

        $this->clearCache($name);

        return $this->runCliCommand("php artisan {$command} 2>&1", $docRoot);
    }

    public function isLaravelSite(string $name): bool
    {
        return Cache::remember("plesk:is_laravel:{$name}", $this->cacheTtl, function () use ($name) {
            $docRoot = $this->getDocRoot($name);

            if (!$docRoot) {
                return false;
            }

            // Check parent of docroot (Laravel's public/ is usually the docroot)
            $projectRoot = dirname($docRoot);
            $output = $this->runCliCommand("test -f {$projectRoot}/artisan && echo 'yes' || echo 'no'", '/tmp');

            return trim($output ?? '') === 'yes';
        });
    }

    public function clearCache(?string $name = null): void
    {
        if ($name) {
            Cache::forget("plesk:domain_detail:{$name}");
            Cache::forget("plesk:databases:{$name}");
            Cache::forget("plesk:is_laravel:{$name}");
        }

        Cache::forget('plesk:domains');
        Cache::forget('plesk:server');
    }

    public function getArtisanWhitelist(): array
    {
        return self::ARTISAN_WHITELIST;
    }
}
