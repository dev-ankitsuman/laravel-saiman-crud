# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2027-06-27

### Added

- `make:crud {Model}` Artisan command for full CRUD scaffolding
- Model generation with fillable fields and optional soft deletes
- Migration generation with typed columns and nullable support
- Web controller (resource) and API controller generation
- Store and Update Form Request generation with validation rules
- Blade views (index, create, edit, show) with Tailwind CSS
- Route registration (appends to `routes/web.php` or `routes/api.php`)
- Database Seeder generation
- Model Factory generation with appropriate Faker methods
- API Resource generation (`--api`)
- Service layer generation (`--service`)
- Repository pattern generation (`--repository`) with interface and implementation
- Livewire CRUD component generation (`--livewire`)
- Filament Resource generation (`--filament`)
- `--fields` option supporting 24 field types with nullable and unique modifiers
- `--force` flag to overwrite existing files
- Customisable stub system (publish with `vendor:publish`)
- Configuration file with namespace/path overrides
- PHPStan Level 8 compliance
- Comprehensive test suite (60+ test cases)
- GitHub Actions CI for PHP 8.2/8.3/8.4 × Laravel 10/11/12

[Unreleased]: https://github.com/dev-ankitsuman/laravel-saiman-crud/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/dev-ankitsuman/laravel-saiman-crud/releases/tag/v1.0.0