<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

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

    public function getDomainDetailRaw(string $name): ?array
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

    /**
     * Parse the raw XML-RPC detail into clean structured data.
     */
    public function getDomainDetail(string $name): array
    {
        $raw = $this->getDomainDetailRaw($name);

        $info = [
            'php_version' => null,
            'disk_usage' => null,
            'ssl' => false,
            'doc_root' => null,
            'ip' => null,
            'hosting_properties' => [],
        ];

        if (!$raw) {
            return $info;
        }

        // Gen info
        $genInfo = $raw['data']['gen_info'] ?? [];
        $info['ip'] = $genInfo['dns_ip_address'] ?? $genInfo['ip_address'] ?? null;

        // Hosting properties — can be single assoc array or list of assoc arrays
        $properties = $raw['data']['hosting']['vrt_hst']['property'] ?? [];
        if (isset($properties['name'])) {
            $properties = [$properties]; // single property → wrap in array
        }

        foreach ($properties as $prop) {
            $propName = $prop['name'] ?? null;
            $propValue = $prop['value'] ?? null;

            if (!$propName || !is_string($propValue)) {
                continue;
            }

            $info['hosting_properties'][$propName] = $propValue;

            if (in_array($propName, ['php_handler_id', 'php_version'])) {
                $info['php_version'] = $propValue;
            }
            if ($propName === 'www_root') {
                $info['doc_root'] = $propValue;
            }
            if ($propName === 'ssl' && $propValue === 'true') {
                $info['ssl'] = true;
            }
        }

        // Disk usage — Plesk XML-RPC returns this as a flat map after simplexml:
        // {httpdocs: "123456", logs: "789", dbases: "456", ...}
        // or sometimes nested under 'usage', or as [{name, value}] pairs
        $diskData = $raw['data']['disk_usage'] ?? [];

        if (isset($diskData['usage'])) {
            $diskData = $diskData['usage'];
        }

        $totalBytes = 0;
        if (is_array($diskData)) {
            foreach ($diskData as $key => $value) {
                if (is_numeric($value)) {
                    // Flat map: {httpdocs: "123456", logs: "789"}
                    $totalBytes += (int) $value;
                } elseif (is_array($value) && isset($value['value'])) {
                    // Structured: [{name: "httpdocs", value: "123456"}]
                    $totalBytes += (int) $value['value'];
                }
            }
        }

        Log::debug('Plesk disk_usage raw', ['domain' => $name, 'raw' => $diskData, 'total' => $totalBytes]);

        if ($totalBytes > 0) {
            if ($totalBytes > 1073741824) {
                $info['disk_usage'] = number_format($totalBytes / 1073741824, 2) . ' GB';
            } else {
                $info['disk_usage'] = number_format($totalBytes / 1048576, 1) . ' MB';
            }
        }

        // Stat (traffic etc)
        $stat = $raw['data']['stat'] ?? [];
        if (isset($stat['traffic'])) {
            $info['traffic'] = $stat['traffic'];
        }

        return $info;
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

    // ── SSH Command Execution ──

    protected function sshConnect(): SSH2
    {
        $host = config('plesk.host');
        $user = config('plesk.ssh_user', 'root');
        $keyPath = config('plesk.ssh_key_path');
        $password = config('plesk.ssh_password');

        if (empty($host)) {
            throw new \RuntimeException('PLESK_HOST not configured.');
        }

        $ssh = new SSH2($host);

        if ($keyPath && file_exists($keyPath)) {
            $key = PublicKeyLoader::load(file_get_contents($keyPath));
            if (!$ssh->login($user, $key)) {
                throw new \RuntimeException("SSH key auth failed for {$user}@{$host}");
            }
        } elseif ($password) {
            if (!$ssh->login($user, $password)) {
                throw new \RuntimeException("SSH password auth failed for {$user}@{$host}");
            }
        } else {
            throw new \RuntimeException('No SSH credentials configured. Set PLESK_SSH_KEY_PATH or PLESK_SSH_PASSWORD in .env');
        }

        return $ssh;
    }

    protected function runCliCommand(string $command, string $workingDir): ?string
    {
        try {
            $ssh = $this->sshConnect();
            $output = $ssh->exec("cd {$workingDir} && {$command}");

            return $output !== false ? $output : null;
        } catch (\Exception $e) {
            Log::error('Plesk SSH command failed', [
                'command' => $command,
                'dir' => $workingDir,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function getVhostRoot(string $name): string
    {
        return "/var/www/vhosts/{$name}";
    }

    protected function getDocRoot(string $name): string
    {
        // Try REST domain info first (returns absolute path)
        $domain = $this->getDomain($name);
        if (isset($domain['www_root']) && str_starts_with($domain['www_root'], '/')) {
            return $domain['www_root'];
        }

        // XML-RPC www_root is relative to vhost — build absolute path
        $detail = $this->getDomainDetail($name);
        $relative = $detail['doc_root'] ?? null;

        if ($relative) {
            return $this->getVhostRoot($name) . '/' . $relative;
        }

        return $this->getVhostRoot($name) . '/httpdocs';
    }

    protected function getProjectRoot(string $name): string
    {
        // Git repo and artisan live in the vhost root, not in httpdocs
        return $this->getVhostRoot($name);
    }

    public function getGitCommits(string $name, int $limit = 10): array
    {
        $projectRoot = $this->getProjectRoot($name);

        $format = '%H|||%h|||%s|||%an|||%ar';
        $output = $this->runCliCommand(
            "git log --format='{$format}' -n {$limit} 2>/dev/null",
            $projectRoot
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
        $this->clearCache($name);

        return $this->runCliCommand('git pull 2>&1', $this->getProjectRoot($name));
    }

    public function runArtisan(string $name, string $command): ?string
    {
        if (!in_array($command, self::ARTISAN_WHITELIST)) {
            return "Command not allowed: {$command}";
        }

        $this->clearCache($name);

        return $this->runCliCommand("php artisan {$command} 2>&1", $this->getProjectRoot($name));
    }

    public function isLaravelSite(string $name): bool
    {
        return Cache::remember("plesk:is_laravel:{$name}", $this->cacheTtl, function () use ($name) {
            $projectRoot = $this->getProjectRoot($name);
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
