<?php

namespace App\Models;

use App\Enums\OllamaPermission;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Fillable(['name', 'expires_at', 'capabilities'])]
#[Hidden(['token'])]
class OllamaToken extends Authenticatable
{
    public $plainToken = null;
    protected $casts = [
        'expires_at' => 'date',
        'capabilities' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (OllamaToken $token) {
            if (empty($token->token)) {
                $plainToken = 'ollama_' . Str::random(64);
                $token->plainToken = $plainToken;
                $token->token = Hash::make($plainToken);
            }
        });
    }

    /**
     * Check if the token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Scope to get only valid (non-expired) tokens
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>=', now());
    }

    /**
     * Scope to get only expired tokens
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }


    public function canApiCall(OllamaPermission|string $permission): bool
    {
        if (!is_string($permission)) {
            $permission = $permission->value;
        }

        return in_array($permission, $this->capabilities, true);
    }
}
