<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedirectClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'redirect_link_id', 'ip_address', 'user_agent', 'referer',
        'browser', 'browser_version', 'platform', 'device_type',
        'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
        ];
    }

    public function redirectLink(): BelongsTo
    {
        return $this->belongsTo(RedirectLink::class);
    }
}
