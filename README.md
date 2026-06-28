<h1 align="center">Laravel Saiman CRUD Generator</h1>

<p align="center">
  <b>⚡ One Command. Complete CRUD. Zero Compromise.</b>
</p>

<p align="center">
  <a href="https://packagist.org/packages/saiman/laravel-saiman-crud">
    <img src="https://img.shields.io/packagist/v/saiman/laravel-saiman-crud?style=flat-square&color=FF2D20" alt="Latest Version on Packagist">
  </a>
  <a href="https://github.com/dev-ankitsuman/laravel-saiman-crud/actions/workflows/tests.yml">
    <img src="https://img.shields.io/github/actions/workflow/status/dev-ankitsuman/laravel-saiman-crud/tests.yml?branch=main&label=tests&style=flat-square" alt="Tests">
  </a>
  <a href="https://github.com/dev-ankitsuman/laravel-saiman-crud/actions/workflows/code-style.yml">
    <img src="https://img.shields.io/github/actions/workflow/status/dev-ankitsuman/laravel-saiman-crud/code-style.yml?branch=main&label=code%20style&style=flat-square" alt="Code Style">
  </a>
  <a href="https://github.com/dev-ankitsuman/laravel-saiman-crud/actions/workflows/static-analysis.yml">
    <img src="https://img.shields.io/github/actions/workflow/status/dev-ankitsuman/laravel-saiman-crud/static-analysis.yml?branch=main&label=phpstan&style=flat-square" alt="PHPStan">
  </a>
  <a href="https://packagist.org/packages/saiman/laravel-saiman-crud">
    <img src="https://img.shields.io/packagist/dt/saiman/laravel-saiman-crud?style=flat-square&color=4F46E5" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/saiman/laravel-saiman-crud">
    <img src="https://img.shields.io/packagist/php-v/saiman/laravel-saiman-crud?style=flat-square" alt="PHP Version">
  </a>
  <a href="https://github.com/dev-ankitsuman/laravel-saiman-crud/blob/main/LICENSE">
    <img src="https://img.shields.io/packagist/l/saiman/laravel-saiman-crud?style=flat-square" alt="License">
  </a>
  <a href="https://github.com/dev-ankitsuman/laravel-saiman-crud/stargazers">
    <img src="https://img.shields.io/github/stars/dev-ankitsuman/laravel-saiman-crud?style=flat-square&color=FFD700" alt="GitHub Stars">
  </a>
</p>

<p align="center">
  <a href="#-why-this-package">Why?</a> •
  <a href="#-features">Features</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-quick-start">Quick Start</a> •
  <a href="#-usage">Usage</a> •
  <a href="#-field-types">Field Types</a> •
  <a href="#-architecture-patterns">Architecture</a> •
  <a href="#-the-revert-command">Revert</a> •
  <a href="#-configuration">Config</a> •
  <a href="#-faq">FAQ</a> •
  <a href="#-roadmap">Roadmap</a>
</p>

---

## 🤔 Why This Package?

Every Laravel developer faces the same painful ritual when starting a new feature:

```
1. Create Model
2. Write Migration manually
3. Create Controller with 7 resource methods
4. Write StoreRequest with all validation rules
5. Write UpdateRequest with slightly different rules
6. Create index.blade.php, create.blade.php, edit.blade.php, show.blade.php
7. Register routes in web.php without breaking existing ones
8. Create Seeder
9. Create Factory with Faker definitions
```

**For 10 models, that is 400–600 lines of identical boilerplate. Every. Single. Project.**

Laravel Saiman CRUD Generator eliminates all of this with **one command:**

```bash
php artisan make:crud Product --fields="name:string,price:decimal,active:boolean"
```

Generates **10+ production-ready files instantly** — and it understands your fields.

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 🚀 **One-Command Scaffolding** | Model, Migration, Controller, Requests, Views, Routes, Seeder, Factory — all at once |
| 🧠 **Field-Aware Generation** | 26 field types with automatic migration column, validation rule, HTML input, and Faker mapping |
| 🛡️ **Smart Route Append** | Routes are safely appended with CRUD markers — your existing routes are never touched |
| 🎨 **Auto Layout Detection** | Uses your existing layout or generates a clean standalone fallback with inline CSS |
| ⏪ **Full Revert System** | `crud:revert` drops the table, removes migration records, and deletes all generated files |
| 🔌 **Multiple Architectures** | API Resources, Service Layer, Repository Pattern, Livewire, Filament — all supported |
| 🎯 **Modern Laravel Only** | Built exclusively for Laravel 12 and 13 with PHP 8.2+ |
| 📝 **PSR-12 Compliant Output** | All generated code passes Laravel Pint automatically |
| 🔧 **Customizable Stubs** | Publish and modify every stub to match your team's standards |
| 🔍 **PHPStan Compatible** | Generated code has proper types, docblocks, and strict typing |

---

## 📋 Requirements

| Requirement | Version |
|-------------|---------|
| **PHP** | `^8.2` |
| **Laravel** | `^12.0 \|\| ^13.0` |

---

## 📦 Installation

Install as a **dev dependency** via Composer:

```bash
composer require saiman/laravel-saiman-crud --dev
```

> The package uses Laravel's auto-discovery. **No manual registration needed.**

### Publish Configuration and Stubs (Optional)

```bash
# Publish config only
php artisan vendor:publish --tag=saiman-crud-config

# Publish stubs only (for customization)
php artisan vendor:publish --tag=saiman-crud-stubs

# Publish everything at once
php artisan vendor:publish --tag=saiman-crud
```

---

## ⚡ Quick Start

### Step 1 — Generate CRUD

```bash
php artisan make:crud Product \
  --fields="name:string,price:decimal,stock:integer,active:boolean,description:text:nullable"
```

**What gets generated:**

```
✓ Model        → app/Models/Product.php
✓ Migration    → database/migrations/xxxx_create_products_table.php
✓ Controller   → app/Http/Controllers/ProductController.php
✓ Requests     → app/Http/Requests/StoreProductRequest.php
✓ Requests     → app/Http/Requests/UpdateProductRequest.php
✓ Views        → resources/views/products/index.blade.php
✓ Views        → resources/views/products/create.blade.php
✓ Views        → resources/views/products/edit.blade.php
✓ Views        → resources/views/products/show.blade.php
✓ Seeder       → database/seeders/ProductSeeder.php
✓ Factory      → database/factories/ProductFactory.php
✓ Routes       → Appended to routes/web.php
```

### Step 2 — Run Migration

```bash
php artisan migrate
```

### Step 3 — Visit Your App

```
http://127.0.0.1:8000/products
```

**Done. Your fully functional CRUD is live.**

---

## 📚 Usage

### Basic Web CRUD

```bash
php artisan make:crud Post --fields="title:string,body:text,published_at:datetime:nullable"
```

### API CRUD (No Blade Views)

```bash
php artisan make:crud Product --api --fields="sku:string:unique,name:string,price:decimal"
```

### With Service Layer

```bash
php artisan make:crud Order --service --fields="total:decimal,status:string"
```

### With Repository Pattern

```bash
php artisan make:crud Customer \
  --repository \
  --fields="email:email:unique,name:string,phone:string:nullable"
```

### With Livewire Component

```bash
php artisan make:crud Task --livewire --fields="title:string,completed:boolean"
```

### With Filament Resource

```bash
php artisan make:crud Category \
  --filament \
  --fields="name:string,slug:string:unique,parent_id:foreignId:nullable"
```

### Full-Stack API with Clean Architecture

```bash
php artisan make:crud Product \
  --api \
  --service \
  --repository \
  --fields="name:string,price:decimal,sku:string:unique,active:boolean"
```

### Force Overwrite Existing Files

```bash
php artisan make:crud Product --force
```

### All Available Options

```bash
php artisan make:crud {Model}
    {--fields=}       # Field definitions
    {--api}           # Generate API controller and JSON resource
    {--service}       # Generate Service class
    {--repository}    # Generate Repository interface and implementation
    {--livewire}      # Generate Livewire component
    {--filament}      # Generate Filament resource
    {--force}         # Overwrite existing files
```

---

## 🧬 Field Types

Define fields using this format:

```
name:type
name:type:nullable
name:type:unique
name:type:nullable:unique
```

Multiple fields are comma-separated:

```bash
--fields="name:string,price:decimal:nullable,email:email:unique,active:boolean"
```

### Supported Types

| Type | Migration Column | Validation Rule | HTML Input | Faker Method |
|------|-----------------|----------------|------------|--------------|
| `string` | `string` | `string\|max:255` | `text` | `words(3, true)` |
| `text` | `text` | `string` | `textarea` | `paragraph()` |
| `longtext` | `longText` | `string` | `textarea` | `text()` |
| `integer` / `int` | `integer` | `integer` | `number` | `randomNumber()` |
| `bigint` | `bigInteger` | `integer` | `number` | `randomNumber()` |
| `smallint` | `smallInteger` | `integer` | `number` | `numberBetween(0, 32767)` |
| `tinyint` | `tinyInteger` | `integer\|min:0\|max:127` | `number` | `numberBetween(0, 127)` |
| `decimal` | `decimal` | `numeric` | `number` | `randomFloat(2, 0, 1000)` |
| `float` | `float` | `numeric` | `number` | `randomFloat(2)` |
| `double` | `double` | `numeric` | `number` | `randomFloat(4)` |
| `boolean` / `bool` | `boolean` | `boolean` | `checkbox` | `boolean()` |
| `date` | `date` | `date` | `date` | `date()` |
| `datetime` | `dateTime` | `date_format:Y-m-d H:i:s` | `datetime-local` | `dateTime()` |
| `timestamp` | `timestamp` | `date_format:Y-m-d H:i:s` | `datetime-local` | `dateTime()` |
| `time` | `time` | `date_format:H:i:s` | `time` | `time()` |
| `email` | `string` | `email:rfc,dns` | `email` | `safeEmail()` |
| `url` | `string` | `url` | `url` | `url()` |
| `password` | `string` | `string\|min:8` | `password` | `bcrypt('password')` |
| `ip` | `ipAddress` | `ip` | `text` | `ipv4()` |
| `uuid` | `uuid` | `uuid` | `text` | `uuid()` |
| `json` | `json` | `array` | `textarea` | `[]` |
| `enum` | `enum` | `string` | `select` | `word()` |
| `file` | `string` | `file` | `file` | `word().'.pdf'` |
| `image` | `string` | `image\|mimes:jpg,jpeg,png` | `file` | `imageUrl()` |
| `foreignId` | `foreignId` | `integer` | `number` | `1` |

### Field Modifiers

| Modifier | Migration Effect | Validation Effect |
|----------|-----------------|-------------------|
| `:nullable` | `->nullable()` | `nullable` replaces `required` |
| `:unique` | `->unique()` | `unique:table` added |

---

## 🏗️ Architecture Patterns

### API Mode (`--api`)

Generates an API controller under `app/Http/Controllers/Api/` and a JSON Resource:

```php
// Generated API Controller
public function index(): AnonymousResourceCollection
{
    $products = Product::latest()->paginate(15);
    return ProductResource::collection($products);
}

public function store(StoreProductRequest $request): ProductResource
{
    $product = Product::create($request->validated());
    return new ProductResource($product);
}
```

Routes are appended to `routes/api.php`:

```php
// CRUD-GENERATED:START Product
Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class);
// CRUD-GENERATED:END Product
```

---

### Service Layer (`--service`)

Generates `app/Services/ProductService.php` with clean CRUD methods:

```php
class ProductService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Product::latest()->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->refresh();
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}
```

---

### Repository Pattern (`--repository`)

Generates both an interface and an Eloquent implementation:

```php
// ProductRepositoryInterface.php
interface ProductRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Product;
    public function create(array $data): Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): bool;
}

// ProductRepository.php
class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private readonly Product $model) {}

    // Full implementation included...
}
```

---

### Livewire (`--livewire`)

Generates a full Livewire CRUD component with pagination and modal:

```php
class ProductManager extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $isEditing = false;

    public function openCreate(): void { ... }
    public function openEdit(int $id): void { ... }
    public function save(): void { ... }
    public function delete(int $id): void { ... }
}
```

> **Requires:** `livewire/livewire ^3.0`

---

### Filament (`--filament`)

Generates a complete Filament Resource with form schema and table columns auto-mapped from your field types:

```php
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('price')->numeric()->required(),
            Forms\Components\Toggle::make('active'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('price')->sortable(),
            Tables\Columns\IconColumn::make('active')->boolean(),
        ]);
    }
}
```

> **Requires:** `filament/filament ^3.0`

---

## ⏪ The Revert Command

The `crud:revert` command is unique to this package. It gives you a **complete undo** of everything `make:crud` created.

### Basic Revert

```bash
php artisan crud:revert Product
```

What happens:

```
1. ✅ Asks for confirmation (twice)
2. ✅ Drops the database table (products)
3. ✅ Removes the migration record from migrations table
4. ✅ Deletes app/Models/Product.php
5. ✅ Deletes app/Http/Controllers/ProductController.php
6. ✅ Deletes app/Http/Requests/StoreProductRequest.php
7. ✅ Deletes app/Http/Requests/UpdateProductRequest.php
8. ✅ Deletes resources/views/products/ (entire directory)
9. ✅ Deletes database/migrations/xxxx_create_products_table.php
10. ✅ Deletes database/seeders/ProductSeeder.php
11. ✅ Deletes database/factories/ProductFactory.php
12. ✅ Removes generated route block from routes/web.php
```

### Revert Options

```bash
# Preview only — nothing is deleted
php artisan crud:revert Product --dry-run

# Also revert API files (controller, resource, api.php routes)
php artisan crud:revert Product --api

# Delete files but keep the database table
php artisan crud:revert Product --keep-table

# Also delete the auto-generated layout file
php artisan crud:revert Product --with-layout
```

### Safe Full Cycle

You can now safely do this without any errors:

```bash
# Generate
php artisan make:crud Product --fields="name:string,price:decimal"
php artisan migrate

# Undo everything
php artisan crud:revert Product

# Generate again (no table already exists error)
php artisan make:crud Product --fields="name:string,price:decimal,sku:string:unique"
php artisan migrate
```

---

## ⚙️ Configuration

After publishing the config file (`config/saiman-crud.php`), you can customize:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Namespace Configuration
    |--------------------------------------------------------------------------
    */
    'namespaces' => [
        'model'      => 'App\\Models',
        'controller' => 'App\\Http\\Controllers',
        'request'    => 'App\\Http\\Requests',
        'resource'   => 'App\\Http\\Resources',
        'service'    => 'App\\Services',
        'repository' => 'App\\Repositories',
        'factory'    => 'Database\\Factories',
        'seeder'     => 'Database\\Seeders',
        'livewire'   => 'App\\Livewire',
        'filament'   => 'App\\Filament\\Resources',
    ],

    /*
    |--------------------------------------------------------------------------
    | Path Configuration
    |--------------------------------------------------------------------------
    */
    'paths' => [
        'model'      => 'app/Models',
        'controller' => 'app/Http/Controllers',
        'request'    => 'app/Http/Requests',
        'resource'   => 'app/Http/Resources',
        'service'    => 'app/Services',
        'repository' => 'app/Repositories',
        'factory'    => 'database/factories',
        'seeder'     => 'database/seeders',
        'migration'  => 'database/migrations',
        'views'      => 'resources/views',
        'routes'     => 'routes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Generation Defaults
    |--------------------------------------------------------------------------
    */
    'soft_deletes' => false,   // Add SoftDeletes to all generated models
    'timestamps'   => true,    // Include timestamps in migrations
    'pagination'   => 15,      // Default pagination count in controllers

];
```

---

## 🔧 Customizing Stubs

Publish all stubs to your project:

```bash
php artisan vendor:publish --tag=saiman-crud-stubs
```

This creates `stubs/saiman-crud/` in your project root:

```
stubs/saiman-crud/
├── model.stub
├── migration.stub
├── controller.stub
├── controller.api.stub
├── request.store.stub
├── request.update.stub
├── resource.stub
├── seeder.stub
├── factory.stub
├── service.stub
├── routes.web.stub
├── routes.api.stub
├── layout.stub
├── views/
│   ├── index.stub
│   ├── create.stub
│   ├── edit.stub
│   └── show.stub
├── repository/
│   ├── interface.stub
│   └── repository.stub
├── livewire/
│   ├── component.stub
│   └── view.stub
└── filament/
    └── resource.stub
```

The generator **always checks your published stubs first** before falling back to the package defaults.

**Common customizations:**

```php
// Add copyright header to model.stub
<?php

/**
 * Copyright (c) {{ date('Y') }} Your Company. All rights reserved.
 */

declare(strict_types=1);

namespace {{Namespace}};
// ...
```

---

## ❓ FAQ

<details>
<summary><b>Does this work with Laravel 11 or 10?</b></summary>

No. This package is built **exclusively for Laravel 12 and 13** to use modern PHP 8.2+ features without legacy baggage. For older versions, use `appzcoder/crud-generator`.
</details>

<details>
<summary><b>Does it overwrite my existing files?</b></summary>

Never without your permission. If a file already exists, the generator skips it with a warning. Use `--force` to overwrite.
</details>

<details>
<summary><b>Does it break my existing routes?</b></summary>

Never. Routes are **appended** to your route file using clearly marked blocks:

```php
// CRUD-GENERATED:START Product
Route::resource('products', \App\Http\Controllers\ProductController::class);
// CRUD-GENERATED:END Product
```

Your existing routes are completely untouched.
</details>

<details>
<summary><b>What if I don't have a layout file?</b></summary>

The generator checks 5 common layout locations. If none exists, it generates `resources/views/layouts/app.blade.php` with a clean standalone layout using inline CSS — no Tailwind or Bootstrap required.
</details>

<details>
<summary><b>Can I use this in production?</b></summary>

This is a **dev dependency** and should not be installed in production (`composer require --dev`). The **generated code** is fully production-ready.
</details>

<details>
<summary><b>How does the revert command work with the database?</b></summary>

`crud:revert` uses Laravel's `Schema::dropIfExists()` to drop the table and removes the migration record from the `migrations` table. This means you can re-run `make:crud` and `php artisan migrate` without any "table already exists" errors.
</details>

<details>
<summary><b>Can I contribute new field types?</b></summary>

Absolutely! Field types are defined in `FieldParser.php` using simple const arrays. Add your type to `TYPE_MAP`, `VALIDATION_MAP`, `INPUT_MAP`, and `FAKER_MAP`. Submit a PR and we will review it.
</details>

<details>
<summary><b>Is there a GUI or web interface?</b></summary>

Not yet. A web-based generator interface is on the roadmap. For now everything is Artisan-command based.
</details>

---

## 🗺️ Roadmap

### ✅ Released (v1.0.0)

- [x] Laravel 12 & 13 support
- [x] 26 field types with full type intelligence
- [x] Smart route append with CRUD markers
- [x] Auto layout detection with fallback generation
- [x] `crud:revert` with database table drop
- [x] API Resource generation (`--api`)
- [x] Service Layer generation (`--service`)
- [x] Repository Pattern generation (`--repository`)
- [x] Livewire component generation (`--livewire`)
- [x] Filament resource generation (`--filament`)
- [x] Customizable stub system
- [x] PSR-12 / Laravel Pint compliance
- [x] PHPStan Level 5 static analysis
- [x] Full Pest test suite

### 🔜 Planned

- [ ] Inertia.js + Vue.js view generation (`--inertia`)
- [ ] Inertia.js + React view generation (`--react`)
- [ ] Policy generation (`--policy`)
- [ ] Observers generation (`--observer`)
- [ ] Events & Listeners (`--events`)
- [ ] Nova resource generation (`--nova`)
- [ ] Multi-language / i18n support
- [ ] Web-based GUI generator
- [ ] GitHub Actions workflow generation
- [ ] OpenAPI / Swagger documentation generation

---

## 🤝 Contributing

Contributions are welcome and appreciated!

### Setup

```bash
git clone https://github.com/dev-ankitsuman/laravel-saiman-crud.git
cd laravel-saiman-crud
composer install
```

### Quality Checks

```bash
# Format code
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse --memory-limit=512M

# Run tests
vendor/bin/pest
```

### Contribution Guidelines

- Follow **PSR-12** coding standards
- Use **Conventional Commits**: `feat:`, `fix:`, `docs:`, `test:`, `chore:`
- Write tests for every new feature or bug fix
- Update documentation for any changed behavior
- Open an issue **before** submitting large changes

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for the full contribution guide.

---

## 🔒 Security

If you discover a security vulnerability, **do not open a public issue**.

Please email **saankit1998@gmail.com** directly. All vulnerabilities are addressed within 48 hours.

See [SECURITY.md](SECURITY.md) for the complete security policy.

---

## 👤 Author

**Ankit Suman**

- GitHub: [@dev-ankitsuman](https://github.com/dev-ankitsuman)
- Packagist: [saiman/laravel-saiman-crud](https://packagist.org/packages/saiman/laravel-saiman-crud)

---

## 🙏 Credits

- **[Ankit Suman](https://github.com/dev-ankitsuman)** — Creator & Maintainer
- **[Laravel](https://laravel.com)** — The framework that makes this possible
- **[Orchestra Testbench](https://github.com/orchestral/testbench)** — Package testing
- **[Pest PHP](https://pestphp.com)** — Elegant testing framework
- **[All Contributors](https://github.com/dev-ankitsuman/laravel-saiman-crud/graphs/contributors)** — Thank you!

---

## 📄 License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

---

<p align="center">
  <a href="https://github.com/sponsors/dev-ankitsuman">
    <img src="https://img.shields.io/badge/Sponsor%20this%20project-%E2%9D%A4-FF2D20?style=for-the-badge&logo=github" alt="Sponsor">
  </a>
</p>

<p align="center">
  If this package saved you time, consider giving it a ⭐ on GitHub!
</p>

<p align="center">
  <sub>Built with ❤️ for the Laravel community by <a href="https://github.com/dev-ankitsuman">Ankit Suman</a></sub>
</p>