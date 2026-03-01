<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PleskService;
use Illuminate\Http\Request;

class PleskController extends Controller
{
    protected PleskService $plesk;

    public function __construct(PleskService $plesk)
    {
        $this->plesk = $plesk;
    }

    public function index()
    {
        try {
            $domains = $this->plesk->getDomains();
        } catch (\Exception $e) {
            $domains = [];
            session()->flash('error', 'Plesk API: ' . $e->getMessage());
        }

        return view('admin.plesk.index', compact('domains'));
    }

    public function server()
    {
        try {
            $server = $this->plesk->getServerInfo();
            $domains = $this->plesk->getDomains();
        } catch (\Exception $e) {
            return back()->with('error', 'Could not fetch server info: ' . $e->getMessage());
        }

        return view('admin.plesk.server', compact('server', 'domains'));
    }

    public function show(string $domain)
    {
        try {
            $domainInfo = $this->plesk->getDomain($domain);

            if (!$domainInfo) {
                return redirect()->route('plesk.index')->with('error', "Domain '{$domain}' not found.");
            }

            $detail = $this->plesk->getDomainDetail($domain);
            $databases = $this->plesk->getDatabases($domain);
            $commits = $this->plesk->getGitCommits($domain);
            $isLaravel = $this->plesk->isLaravelSite($domain);
            $artisanCommands = $this->plesk->getArtisanWhitelist();
        } catch (\Exception $e) {
            return redirect()->route('plesk.index')->with('error', 'Error loading domain: ' . $e->getMessage());
        }

        return view('admin.plesk.show', compact(
            'domain', 'domainInfo', 'detail', 'databases', 'commits', 'isLaravel', 'artisanCommands'
        ));
    }

    public function gitPull(string $domain)
    {
        try {
            $output = $this->plesk->gitPull($domain);

            if ($output === null) {
                return back()->with('error', 'Could not execute git pull. Check server logs.');
            }

            return back()->with('success', "Git pull executed: {$output}");
        } catch (\Exception $e) {
            return back()->with('error', 'Git pull failed: ' . $e->getMessage());
        }
    }

    public function artisan(Request $request, string $domain)
    {
        $request->validate([
            'command' => ['required', 'string'],
        ]);

        try {
            $output = $this->plesk->runArtisan($domain, $request->input('command'));

            if ($output === null) {
                return back()->with('error', 'Could not execute artisan command. Check server logs.');
            }

            return back()->with('success', "Artisan output: {$output}");
        } catch (\Exception $e) {
            return back()->with('error', 'Artisan command failed: ' . $e->getMessage());
        }
    }

    public function refresh(Request $request, ?string $domain = null)
    {
        $this->plesk->clearCache($domain);

        $message = $domain ? "Cache cleared for {$domain}." : 'All Plesk cache cleared.';

        if ($domain) {
            return redirect()->route('plesk.show', $domain)->with('success', $message);
        }

        return redirect()->route('plesk.index')->with('success', $message);
    }
}
