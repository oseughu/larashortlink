# Larashortlink - A URL Shortening Service

This is a simple URL shortening service built with Laravel. It provides two endpoints:

Base URL: [https://larashortlink.oseughu.com/api](https://larashortlink.oseughu.com/api)

- **`/encode`** (POST): Accepts a JSON body or Query Param with an `original_url` and returns the created short URL.
- **`/decode`** (POST): Accepts a JSON body or Query Param with a `short_url` and returns the original URL.

- Additionally, the URL **`https://larashortlink.oseughu.com/{shortCode}`** redirects to the original url using the shortCode from the `short_url`.

You can use Postman or any other API testing tool to test the endpoints.

## Requirements

- PHP 8.2+
- Composer
- A database (e.g., MySQL, SQLite). Note: If you want to use SQLite for simplicity, you can set it in the `.env` file. (This is what I used for this project.)
- It is configurable and customizable as your short link prefix can be set in the `.env` file.

## Installation

You need to clone the repository and run the following command to install the dependencies:

```bash
git clone https://github.com/oseughu/larashortlink.git
composer install
php artisan serve
```

This will start the server on `http://localhost:8000`.

## Tests

To run the tests, you can use the following command:

```bash
php artisan test
```
