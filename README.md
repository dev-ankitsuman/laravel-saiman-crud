# Laravel Saiman CRUD Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/saiman/laravel-saiman-crud.svg?style=flat-square)](https://packagist.org/packages/saiman/laravel-saiman-crud)
[![Tests](https://img.shields.io/github/actions/workflow/status/saiman/laravel-saiman-crud/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/dev-ankitsuman/laravel-saiman-crud/actions/workflows/tests.yml)
[![Code Style](https://img.shields.io/github/actions/workflow/status/saiman/laravel-saiman-crud/code-style.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/dev-ankitsuman/laravel-saiman-crud/actions/workflows/code-style.yml)
[![Static Analysis](https://img.shields.io/github/actions/workflow/status/saiman/laravel-saiman-crud/static-analysis.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/dev-ankitsuman/laravel-saiman-crud/actions/workflows/static-analysis.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/saiman/laravel-saiman-crud.svg?style=flat-square)](https://packagist.org/packages/saiman/laravel-saiman-crud)
[![PHP Version](https://img.shields.io/packagist/php-v/saiman/laravel-saiman-crud.svg?style=flat-square)](https://packagist.org/packages/saiman/laravel-saiman-crud)
[![License](https://img.shields.io/packagist/l/saiman/laravel-saiman-crud.svg?style=flat-square)](LICENSE)

Generate complete, production-ready CRUD modules for your Laravel application with a single Artisan command. Stop writing boilerplate — start building features.

---

## ✨ Features

| Feature | Flag |
|---------|------|
| Model + Migration + Controller + Requests + Views + Routes + Seeder + Factory | *(default)* |
| API Controller + JSON Resource | `--api` |
| Service Class | `--service` |
| Repository Interface + Implementation | `--repository` |
| Livewire CRUD Component | `--livewire` |
| Filament Resource | `--filament` |
| Typed field definitions | `--fields` |
| Force overwrite | `--force` |
| Customisable stubs | `vendor:publish` |

---

## Requirements

- **PHP** 8.2, 8.3, or 8.4
- **Laravel** 10, 11, or 12

---

## Installation

```bash
composer require saiman/laravel-saiman-crud --dev
```

Laravel auto-discovers the service provider. No manual registration needed.

---

## Quick Start

```bash
php artisan make:crud Product
```

Generates 10+ files instantly:

```
✓ Model       → app/Models/Product.php
✓ Migration   → database/migrations/xxxx_create_products_table.php
✓ Controller  → app/Http/Controllers/ProductController.php
✓ Requests    → app/Http/Requests/StoreProductRequest.php
✓ Requests    → app/Http/Requests/UpdateProductRequest.php
✓ Views       → resources/views/products/{index,create,edit,show}.blade.php
✓ Routes      → Appended to routes/web.php
✓ Seeder      → database/seeders/ProductSeeder.php
✓ Factory     → database/factories/ProductFactory.php
```

Then:

```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder
```

Visit `/products` — your CRUD is live.

---

## Configuration

Publish the config to customise namespaces, paths, layout, and more:

```bash
php artisan vendor:publish --tag=crud-generator-config
```

Key options in `config/crud-generator.php`:

```php
'namespaces' => [
    'model'      => 'App\\Models',
    'controller' => 'App\\Http\\Controllers',
    // ...
],
'views' => [
    'layout' => 'layouts.app',  // ← your Blade layout
],
'soft_deletes' => false,
'pagination'   => 15,
```

---

## Field Definitions

```bash
php artisan make:crud Product \
  --fields="name:string,price:decimal,stock:integer,active:boolean,description:text:nullable"
```

### Supported Types

| Type | Migration | Input | Validation |
|------|-----------|-------|------------|
| `string` | `string` | `text` | `string\|max:255` |
| `text` | `text` | `textarea` | `string` |
| `longtext` | `longText` | `textarea` | `string` |
| `integer` / `int` | `integer` | `number` | `integer` |
| `bigint` | `bigInteger` | `number` | `integer` |
| `smallint` | `smallInteger` | `number` | `integer` |
| `tinyint` | `tinyInteger` | `number` | `integer\|min:0\|max:127` |
| `float` | `float` | `number` | `numeric` |
| `double` | `double` | `number` | `numeric` |
| `decimal` | `decimal` | `number` | `numeric` |
| `boolean` / `bool` | `boolean` | `checkbox` | `boolean` |
| `date` | `date` | `date` | `date` |
| `datetime` | `dateTime` | `datetime-local` | `date_format:Y-m-d H:i:s` |
| `timestamp` | `timestamp` | `datetime-local` | `date_format:Y-m-d H:i:s` |
| `time` | `time` | `time` | `date_format:H:i:s` |
| `email` | `string` | `email` | `email:rfc,dns` |
| `url` | `string` | `url` | `url` |
| `ip` | `ipAddress` | `text` | `ip` |
| `uuid` | `uuid` | `text` | `uuid` |
| `json` | `json` | `textarea` | `array` |
| `file` | `string` | `file` | `file` |
| `image` | `string` | `file` | `image\|mimes:...` |
| `password` | `string` | `password` | `string\|min:8` |
| `foreignId` | `foreignId` | `number` | `integer` |

### Modifiers

Append after type:

```bash
--fields="email:email:unique,bio:text:nullable"
```

| Modifier | Effect |
|----------|--------|
| `:nullable` | Adds `->nullable()` to migration and `nullable` to validation |
| `:unique` | Adds `->unique()` to migration and `unique:table` to validation |

---

## Available Commands & Flags

```bash
php artisan make:crud {Model} [options]
```

| Option | Description |
|--------|-------------|
| `--fields="..."` | Field definitions (see above) |
| `--api` | API controller + JSON Resource. No Blade views generated. |
| `--service` | Generate a Service class in `app/Services/` |
| `--repository` | Generate Repository Interface + Eloquent implementation |
| `--livewire` | Generate Livewire CRUD component (requires `livewire/livewire`) |
| `--filament` | Generate Filament resource (requires `filament/filament`) |
| `--force` | Overwrite existing files |

### Examples

```bash
# Web CRUD with fields
php artisan make:crud Product \
  --fields="name:string,price:decimal,active:boolean"

# Full API stack
php artisan make:crud Product --api --service --repository \
  --fields="name:string,price:decimal,sku:string:unique"

# Livewire CRUD
php artisan make:crud Product --livewire \
  --fields="name:string,price:decimal"

# Filament Resource
php artisan make:crud Product --filament \
  --fields="name:string,price:decimal,featured:boolean"

# Overwrite everything
php artisan make:crud Product --force
```

---

## Publishing Stubs

Customise any generated file by publishing stubs:

```bash
php artisan vendor:publish --tag=crud-generator-stubs
```

Stubs are placed in `stubs/crud-generator/`. The package will use your custom stubs automatically.

---

## API Reference

### `make:crud {Model}`

Generates a complete CRUD module. `{Model}` must be PascalCase (e.g. `Product`, `BlogPost`).

### Field Format

```
name:type[:modifier[:modifier...]]
```

Multiple fields are comma-separated:

```
name:string,price:decimal:nullable,active:boolean
```

---

## FAQ

**Q: Does it work with custom namespaces?**
A: Yes. Publish the config and set `namespaces.*` to your values.

**Q: Can I use it in a DDD / modular structure?**
A: Yes. Override `paths.*` and `namespaces.*` in the config.

**Q: Will it add routes to existing route files?**
A: Yes, it appends to `routes/web.php` or `routes/api.php`. It checks for duplicates to prevent double-registration.

**Q: Does `--livewire` require Livewire to be installed?**
A: Yes. Install `livewire/livewire ^3.0` before using this flag.

**Q: Does `--filament` require Filament to be installed?**
A: Yes. Install `filament/filament ^3.0` before using this flag.

---

## Troubleshooting

**"Stub not found" error**
Run `php artisan vendor:publish --tag=crud-generator-stubs` and check if the stubs exist in `stubs/crud-generator/`.

**"File already exists" warning**
Add `--force` to overwrite: `php artisan make:crud Product --force`.

**Views don't extend my layout**
Publish the config and change `views.layout` to your layout name.

**Fields not appearing in views**
Pass `--fields` when generating. Fields are only injected at generation time.

---

## Upgrade Guide

### From 1.x to 2.x

*(No breaking changes in 1.x series.)*

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security

See [SECURITY.md](SECURITY.md).

## Credits

- [Ankit Suman](https://github.com/dev-ankitsuman) — creator
- [All Contributors](https://github.com/dev-ankitsuman/laravel-saiman-crud/contributors)

## License

MIT — see [LICENSE](LICENSE).