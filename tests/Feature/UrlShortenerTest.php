<?php

use App\Models\ShortUrl;

/**
 * Test the encode endpoint with a valid URL.
 */
test('it encodes a valid URL', function () {
    $response = $this->postJson('/api/encode', [
        'original_url' => 'https://www.example.com/my/long/url'
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'original_url',
            'short_url',
        ]);
});

/**
 * Test the encode endpoint fails when the same URL is submitted twice
 * because the validation uses 'unique:short_urls' on original_url.
 */
test('it fails to encode a duplicate URL', function () {
    // First creation
    ShortUrl::create([
        'original_url' => 'https://www.example.com/my/long/url',
        'short_url'    => 'http://short.est/AbCdEf',
        'short_code'    => 'AbCdEf',
    ]);

    // Second creation attempt with the same original_url
    $response = $this->postJson('/api/encode', [
        'original_url' => 'https://www.example.com/my/long/url'
    ]);

    // Should fail with 422 because original_url must be unique
    $response->assertStatus(422)
        ->assertJsonValidationErrors('original_url');
});

test('it decodes a valid short URL (given code mismatch in snippet)', function () {
    ShortUrl::create([
        'original_url' => 'https://www.example.com/long/url',
        'short_url'    => 'http://short.est/abc123',
        'short_code'    => 'abc123',
    ]);

    $response = $this->postJson('/api/decode', [
        'short_url' => 'http://short.est/abc123'
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

    // 'short_url' => 'required|url|exists:short_urls' will fail
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

    // Fails the 'exists:short_urls' validation
    $response->assertStatus(422)
        ->assertJsonValidationErrors('short_url');
});
