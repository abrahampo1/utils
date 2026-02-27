<?php

namespace App\Http\Controllers;

use App\Models\LinkClick;
use App\Models\TrackingLink;
use Jenssegers\Agent\Agent;

class TrackingLinkRedirectController extends Controller
{
    public function __invoke(string $code)
    {
        $link = TrackingLink::active()->where('code', $code)->firstOrFail();

        $agent = new Agent();
        $agent->setUserAgent(request()->userAgent());

        LinkClick::create([
            'tracking_link_id' => $link->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
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
