<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PleskService;
use Illuminate\Http\JsonResponse;
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
            $projectRoot = $this->plesk->getProjectRoot($domain);
        } catch (\Exception $e) {
            return redirect()->route('plesk.index')->with('error', 'Error loading domain: ' . $e->getMessage());
        }

        return view('admin.plesk.show', compact(
            'domain', 'domainInfo', 'detail', 'databases', 'projectRoot'
        ));
    }

    public function sshCommand(Request $request, string $domain): JsonResponse
    {
        $request->validate([
            'command' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $projectRoot = $this->plesk->getProjectRoot($domain);
            $output = $this->plesk->runCliCommand($request->input('command'), $projectRoot);

            return response()->json(['output' => $output ?? '']);
        } catch (\Exception $e) {
            return response()->json(['output' => 'Error: ' . $e->getMessage()], 500);
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
