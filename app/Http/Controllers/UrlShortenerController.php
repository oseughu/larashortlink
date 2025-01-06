<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $existingUrl = ShortLink::findByOriginalUrl($data['original_url']);

        if ($existingUrl) {
            return response()->json([
                'original_url' => $existingUrl->original_url,
                'short_url' => $existingUrl->short_url,
            ]);
        }

        // ideally, we'd use a service to generate the short code
        $shortCode = $this->generateUniqueShortCode();
        $shortLinkPrefix = rtrim(env('APP_SHORT_URL_PREFIX', 'https://l.nk'), '/');
        $shortenedUrl = "$shortLinkPrefix/$shortCode";

        $shortLink = ShortLink::create([
            'original_url' => $data['original_url'],
            'short_code' => $shortCode,
            'short_url' => $shortenedUrl,
        ]);

        return response()->json([
            'original_url' => $shortLink->original_url,
            'short_url' => $shortLink->short_url,
        ], 201);
    }

    /**
     * Decodes a shortened URL to its original URL.
     * Endpoint: POST /api/decode
     * Body (JSON or Query Params):  "short_code": "abc123" }
     */
    public function decode(Request $request)
    {
        $data = $request->validate([
            'short_url' => 'required|url',
        ]);

        $shortLink = ShortLink::findByShortUrl($data['short_url']);

        if (!$shortLink) {
            return response()->json([
                'error' => 'URL not found',
            ], 404);
        }

        return response()->json([
            'original_url' => $shortLink->original_url,
        ]);
    }

    /**
     * Redirects a shortcode to its original URL.
     * Endpoint: GET /{short_code}
     */
    public function redirectToOriginalUrl($shortCode)
    {
        $shortLink = ShortLink::findByShortCode($shortCode);

        if (!$shortLink) {
            abort(404);
        }

        return redirect()->away($shortLink->original_url);
    }

    /**
     * Generate a unique short code of a given length, retrying until it's unique.
     */
    private function generateUniqueShortCode(): string
    {
        $shortCodeLength = (int) env('APP_SHORT_CODE_LENGTH', 4);
        $shortCode = Str::random($shortCodeLength);

        while (ShortLink::where('short_code', $shortCode)->exists()) {
            $shortCode = Str::random($shortCodeLength);
        }

        return strtolower($shortCode);
    }
}
