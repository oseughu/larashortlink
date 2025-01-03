<?php

use App\Models\ShortLink;

/**
 * Test encoding a brand-new URL returns 201 and the correct JSON structure.
 */
test('it encodes a new URL and returns 201', function () {
    $response = $this->postJson('/api/encode', [
        'original_url' => 'https://www.example.com/new/url',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'original_url',
            'short_url',
        ]);

    // Ensure the database actually contains the new record
    $this->assertDatabaseHas('short_links', [
        'original_url' => 'https://www.example.com/new/url',
    ]);
});

/**
 * Test encoding the same URL again returns 200,
 * and returns the same short_url record as before.
 */
test('it returns the existing short URL with a 200 if the original URL has been shortened before', function () {
    // First call: 201
    $firstResponse = $this->postJson('/api/encode', [
        'original_url' => 'https://www.example.com/duplicate/url',
    ]);
    $firstResponse->assertStatus(201);
    $firstShortUrl = $firstResponse->json('short_url');

    // Second call for the same URL: 200, same short_url
    $secondResponse = $this->postJson('/api/encode', [
        'original_url' => 'https://www.example.com/duplicate/url',
    ]);
    $secondResponse->assertStatus(200);
    $secondShortUrl = $secondResponse->json('short_url');

    expect($secondShortUrl)->toBe($firstShortUrl); // The same short link
});

/**
 * Test validation: sending an invalid URL should return 422.
 */
test('it returns 422 when the original_url is invalid', function () {
    $response = $this->postJson('/api/encode', [
        'original_url' => 'not-a-real-url',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('original_url');
});

test('it decodes a valid short URL (given code mismatch in snippet)', function () {
    ShortLink::create([
        'original_url' => 'https://www.example.com/long/url',
        'short_url' => 'http://short.est/abc123',
        'short_code' => 'abc123',
    ]);

    $response = $this->postJson('/api/decode', [
        'short_url' => 'http://short.est/abc123',
    ]);

    // Expect a 200 based on the snippet’s final return if everything passes
    $response->assertStatus(200)
        ->assertJson([
            'original_url' => 'https://www.example.com/long/url',
        ]);
});

/**
 * Test the decode endpoint fails validation if no short_url is provided.
 */
test('it returns validation errors if the short_url is missing in decode', function () {
    $response = $this->postJson('/api/decode', []);

    // 'short_url' => 'required|url|exists:short_links' will fail
    $response->assertStatus(422)
        ->assertJsonValidationErrors('short_url');
});

/**
 * Test the decode endpoint fails validation for an invalid URL format.
 */
test('it returns validation error if the short_url is not a valid URL', function () {
    $response = $this->postJson('/api/decode', [
        'short_url' => 'not-a-valid-url',
    ]);

    // Fails the 'url' validation
    $response->assertStatus(422)
        ->assertJsonValidationErrors('short_url');
});

/**
 * Test the decode endpoint fails validation if the short_url does not exist in the DB.
 */
test('it returns validation error if the short_url does not exist in the db', function () {
    $response = $this->postJson('/api/decode', [
        'short_url' => 'http://short.est/fake',
    ]);

    // Returns 404 because the URL does not exist in the database
    $response->assertStatus(404)
        ->assertJson(['error' => 'URL not found']);
});

/**
 * Test that encoding a URL with different cases returns the same short URL.
 */
test('it encodes URLs case-insensitively', function () {
    $urlLower = 'https://www.example.com/case-test';
    $urlUpper = 'https://www.EXAMPLE.com/CASE-TEST';

    // Encode the lowercase URL
    $responseLower = $this->postJson('/api/encode', [
        'original_url' => $urlLower,
    ]);
    $responseLower->assertStatus(201);
    $shortUrlLower = $responseLower->json('short_url');

    // Encode the uppercase URL
    $responseUpper = $this->postJson('/api/encode', [
        'original_url' => $urlUpper,
    ]);
    $responseUpper->assertStatus(200);
    $shortUrlUpper = $responseUpper->json('short_url');

    // Assert both short URLs are the same
    expect($shortUrlUpper)->toBe($shortUrlLower);
});

/**
 * Test that decoding a short URL with different cases returns the same original URL.
 */
test('it decodes short URLs case-insensitively', function () {
    $originalUrl = 'https://www.example.com/long/url';
    $shortCode = 'AbC123';
    $shortUrl = "http://short.est/{$shortCode}";

    // Create a short link with mixed case short_code
    ShortLink::create([
        'original_url' => $originalUrl,
        'short_url' => $shortUrl,
        'short_code' => $shortCode,
    ]);

    // Decode using different cases of the short URL
    $response = $this->postJson('/api/decode', [
        'short_url' => Str::lower($shortUrl),
    ]);
    $response->assertStatus(200)
        ->assertJson(['original_url' => $originalUrl]);

    $response = $this->postJson('/api/decode', [
        'short_url' => Str::upper($shortUrl),
    ]);
    $response->assertStatus(200)
        ->assertJson(['original_url' => $originalUrl]);
});
