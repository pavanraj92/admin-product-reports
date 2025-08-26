# Product Report

Generates insights and analytics for product performance and sales.

## Features

- Generate sales and transaction reports

## Requirements

- PHP >=8.2
- Laravel Framework >= 12.x

## Installation

### 1. Add Git Repository to `composer.json`

```json
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/pavanraj92/admin-product-reports.git"
        }
]
```

### 2. Require the package via Composer
    ```bash
    composer require admin/product_reports:@dev
    ```

### 3. Publish assets
    ```bash
    php artisan reports:publish --force
    ```
---


## Usage

 **Reporting**: Generate and view sales and transaction reports.

## Admin Panel Routes

| Method | Endpoint                                 | Description                              |
| ------ | ---------------------------------------- | ---------------------------------------- |
| GET    | /reports                                 | View sales and transaction reports       |

---

## Protecting Admin Routes

Protect your routes using the provided middleware:

```php
Route::middleware(['web','admin.auth'])->group(function () {
    // products routes here
});
```

## License

This package is open-sourced software licensed under the MIT license.
