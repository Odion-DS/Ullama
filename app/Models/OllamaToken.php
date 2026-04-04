<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['name', 'expires_at'])]
#[Hidden(['token'])]
class OllamaToken extends Model
{
    protected $casts = [
        'expires_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (OllamaToken $token) {
            if (empty($token->token)) {
                $token->token = 'ollama_' . Str::random(64);
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
}
