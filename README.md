# URL Shortener Service

This is a simple URL shortening service built with Laravel. It provides two endpoints:

- **`/api/encode`** (POST): Accepts a JSON body with an `original_url` and returns a shortened URL.
- **`/api/decode`** (POST): Accepts a JSON body with a `short_code` and returns the original URL.

## Requirements

- PHP 8.1+
- Composer
- A database (e.g., MySQL, SQLite). Note: If you want to use SQLite for simplicity, you can set it in the `.env` file. (This is what I used for this project.)

## Installation and Setup

1. **Clone the repository** (or download the project).
   ```bash
   git clone https://github.com/oseughu/larashortlink.git
   ```
