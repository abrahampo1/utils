<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tracking_link_id', 'ip_address', 'user_agent', 'referer',
        'browser', 'browser_version', 'platform', 'device_type',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
        'country', 'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
        ];
    }

    public function trackingLink(): BelongsTo
    {
        return $this->belongsTo(TrackingLink::class);
    }
}
