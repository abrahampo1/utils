<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TrackingLink extends Model
{
    protected $fillable = [
        'name', 'code', 'destination_url', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TrackingLink $link) {
            if (empty($link->code)) {
                $link->code = Str::slug($link->name);
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(LinkClick::class);
    }
}
