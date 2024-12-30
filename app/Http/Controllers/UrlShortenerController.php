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
        $data = $request->validate([
            'original_url' => 'required|url',
        ]);

        $existingUrl = ShortUrl::firstWhere('original_url', $data['original_url']);
        if ($existingUrl) {
            return response()->json([
                'original_url' => $existingUrl->original_url,
                'short_url'    => $existingUrl->short_url,
            ]);
        }

        $shortCode = $this->generateUniqueShortCode();
        $shortLinkPrefix = rtrim(env('APP_SHORT_URL_PREFIX', 'http://short.est'), '/');
        $shortenedUrl = "{$shortLinkPrefix}/{$shortCode}";

        $shortUrl = ShortUrl::create([
            'original_url' => $data['original_url'],
            'short_code'   => $shortCode,
            'short_url'    => $shortenedUrl,
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
        $data = $request->validate([
            'short_url' => 'required|url|exists:short_urls',
        ]);

        $shortUrl = ShortUrl::firstWhere('short_url', $data['short_url']);

        return response()->json([
            'original_url' => $shortUrl->original_url,
        ], 200);
    }

    /**
     * Generate a unique short code of a given length, retrying until it's unique.
     */
    private function generateUniqueShortCode(int $length = 4): string
    {
        do {
            $shortCode = Str::random($length);
        } while (ShortUrl::where('short_code', $shortCode)->exists());

        return $shortCode;
    }
}
