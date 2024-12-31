<?php

use App\Http\Controllers\UrlShortenerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to my Laravel Link Shortener!']);
});

Route::get('/{shortCode}', [UrlShortenerController::class, 'redirectToOriginalUrl']);
