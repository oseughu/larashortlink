# URL Shortener Service

This is a simple URL shortening service built with Laravel. It provides two endpoints:

- **`/api/encode`** (POST): Accepts a JSON body with an `original_url` and returns a shortened URL.
- **`/api/decode`** (POST): Accepts a JSON body with a `short_url` and returns the original URL.

## Requirements

- PHP 8.2+
- Composer
- A database (e.g., MySQL, SQLite). Note: If you want to use SQLite for simplicity, you can set it in the `.env` file. (This is what I used for this project.)
- It is configurable and customizable as your short link prefix can be set in the `.env` file.

## Tests

To run the tests, you can use the following command:

```bash
php artisan test
```

## Deployment

The service is deployed on Laravel Forge. You can access it at the following URL:

[https://larashortlink.oseughu.com](https://larashortlink.oseughu.com)
