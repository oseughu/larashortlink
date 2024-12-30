<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ShortUrl;

class UrlShortenerController extends Controller
{
    /**
     * Encodes a URL to a shortened URL.
     * Endpoint: POST /api/encode
     * Body (JSON or Query Params): { "original_url": "https://www.example.com/long/url" }
     */
    public function encode(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url|unique:short_urls',
        ]);

        $shortLinkPrefix = env('APP_SHORT_URL_PREFIX');
        $shortCode = Str::random(6);
        while (ShortUrl::where('short_code', $shortCode)->exists()) {
            $shortCode = Str::random(6);
        }
        $shortenedUrl = $shortLinkPrefix . '/' . $shortCode;

        $shortUrl = ShortUrl::create([
            'original_url' => $request->original_url,
            'short_code' => $shortCode,
            'short_url' => $shortenedUrl,
        ]);

        return response()->json([
            'original_url' => $shortUrl->original_url,
            'short_url'    => $shortenedUrl,
        ], 201);
    }

    /**
     * Decodes a shortened URL to its original URL.
     * Endpoint: POST /api/decode
     * Body (JSON or Query Params): { "short_code": "abc123" }
     */
    public function decode(Request $request)
    {
        $request->validate([
            'short_url' => 'required|url|exists:short_urls',
        ]);

        $shortUrl = ShortUrl::firstWhere('short_url', $request->short_url);

        return response()->json([
            'original_url' => $shortUrl->original_url,
        ], 200);
    }
}
