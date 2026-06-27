<?php

declare(strict_types=1);

use Saiman\SaimanCrud\Support\NameResolver;

it('normalises to StudlyCase', function () {
    expect((new NameResolver('blogPost'))->getModelName())->toBe('BlogPost');
});

it('returns plural model name', function () {
    expect((new NameResolver('Product'))->getModelNamePlural())->toBe('Products');
    expect((new NameResolver('Category'))->getModelNamePlural())->toBe('Categories');
});

it('returns camelCase variable', function () {
    expect((new NameResolver('Product'))->getModelVariable())->toBe('product');
    expect((new NameResolver('BlogPost'))->getModelVariable())->toBe('blogPost');
});

it('returns snake_case plural', function () {
    expect((new NameResolver('BlogPost'))->getModelSnakePlural())->toBe('blog_posts');
    expect((new NameResolver('ProductCategory'))->getModelSnakePlural())->toBe('product_categories');
});

it('returns correct table name', function () {
    expect((new NameResolver('Product'))->getTableName())->toBe('products');
    expect((new NameResolver('BlogPost'))->getTableName())->toBe('blog_posts');
});

it('returns correct controller name', function () {
    expect((new NameResolver('Product'))->getControllerName())->toBe('ProductController');
});

it('returns correct request names', function () {
    $r = new NameResolver('Product');
    expect($r->getStoreRequestName())->toBe('StoreProductRequest');
    expect($r->getUpdateRequestName())->toBe('UpdateProductRequest');
});

it('returns correct seeder and factory names', function () {
    $r = new NameResolver('Product');
    expect($r->getSeederName())->toBe('ProductSeeder');
    expect($r->getFactoryName())->toBe('ProductFactory');
});

it('replacement array has all required keys', function () {
    $array = (new NameResolver('Product'))->toReplacementArray();
    expect($array)
        ->toHaveKey('{{ModelName}}')
        ->toHaveKey('{{ModelNamePlural}}')
        ->toHaveKey('{{modelVariable}}')
        ->toHaveKey('{{tableName}}')
        ->toHaveKey('{{ControllerName}}');
});

it('replacement values are correct', function () {
    $array = (new NameResolver('Product'))->toReplacementArray();
    expect($array['{{ModelName}}'])->toBe('Product')
        ->and($array['{{tableName}}'])->toBe('products')
        ->and($array['{{ControllerName}}'])->toBe('ProductController');
});