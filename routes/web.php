<?php

use App\Http\Controllers\UrlShortenerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to Ose\'s Laravel Link Shortener!']);
});

Route::get('/{shortCode}', [UrlShortenerController::class, 'redirectToOriginalUrl']);
