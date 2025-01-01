<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_url',
        'short_url',
        'short_code',
    ];

    protected function originalUrl(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }

    protected function shortUrl(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }

    protected function shortCode(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower($value),
        );
    }

    public static function findByShortCode($value)
    {
        return static::firstWhere('short_code', strtolower($value));
    }

    public static function findByOriginalUrl($value)
    {
        return static::firstWhere('original_url', strtolower($value));
    }

    public static function findByShortUrl($value)
    {
        return static::firstWhere('short_url', strtolower($value));
    }
}
