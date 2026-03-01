<?php

namespace App\Http\Controllers;

use App\Models\RedirectClick;
use App\Models\RedirectLink;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class RedirectController extends Controller
{
    public function handle(Request $request, string $code)
    {
        $link = RedirectLink::active()->where('code', $code)->firstOrFail();

        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        RedirectClick::create([
            'redirect_link_id' => $link->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'browser' => $agent->browser() ?: null,
            'browser_version' => $agent->version($agent->browser()) ?: null,
            'platform' => $agent->platform() ?: null,
            'device_type' => $this->getDeviceType($agent),
            'clicked_at' => now(),
        ]);

        return redirect()->away($link->destination_url, 302);
    }

    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isTablet()) {
            return 'Tablet';
        }

        if ($agent->isMobile()) {
            return 'Mobile';
        }

        if ($agent->isDesktop()) {
            return 'Desktop';
        }

        return 'Other';
    }
}
