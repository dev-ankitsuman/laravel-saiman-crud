<?php

declare(strict_types=1);

use Saiman\SaimanCrud\Exceptions\StubNotFoundException;
use Saiman\SaimanCrud\Support\StubRenderer;

function stubReplacements(): array
{
    return [
        '{{ModelName}}'           => 'Product',
        '{{ModelNamePlural}}'     => 'Products',
        '{{modelVariable}}'       => 'product',
        '{{modelVariablePlural}}' => 'products',
        '{{model_snake}}'         => 'product',
        '{{model_snake_plural}}'  => 'products',
        '{{model-kebab}}'         => 'product',
        '{{model-kebab-plural}}'  => 'products',
        '{{tableName}}'           => 'products',
        '{{ControllerName}}'      => 'ProductController',
        '{{StoreRequestName}}'    => 'StoreProductRequest',
        '{{UpdateRequestName}}'   => 'UpdateProductRequest',
        '{{ResourceName}}'        => 'ProductResource',
        '{{ServiceName}}'         => 'ProductService',
        '{{RepositoryInterface}}' => 'ProductRepositoryInterface',
        '{{RepositoryName}}'      => 'ProductRepository',
        '{{SeederName}}'          => 'ProductSeeder',
        '{{FactoryName}}'         => 'ProductFactory',
        '{{viewFolder}}'          => 'products',
        '{{LivewireComponent}}'   => 'ProductManager',
        '{{paginationCount}}'     => '15',
        '{{Namespace}}'           => 'App\\Models',
        '{{ModelNamespace}}'      => 'App\\Models',
        '{{RequestNamespace}}'    => 'App\\Http\\Requests',
        '{{ResourceNamespace}}'   => 'App\\Http\\Resources',
        '{{fillable}}'            => "        'name',",
        '{{softDeletesUse}}'      => '',
        '{{softDeletesTrait}}'    => '',
        '{{timestamps}}'          => '',
        '{{columns}}'             => "            \$table->string('name');",
        '{{softDeletes}}'         => '',
        '{{validationRules}}'     => "            'name' => 'required|string|max:255',",
        '{{factoryDefinition}}'   => "            'name' => \$this->faker->words(3, true),",
        '{{resourceFields}}'      => "            'id' => \$this->id,",
        '{{layout}}'              => 'layouts.app',
        '{{tableHeaders}}'        => '',
        '{{tableCells}}'          => '',
        '{{createInputs}}'        => '',
        '{{editInputs}}'          => '',
        '{{showFields}}'          => '',
    ];
}

beforeEach(function () {
    $this->renderer = new StubRenderer();
    $this->renderer->clearCache();
});

it('renders model stub without throwing', function () {
    $content = $this->renderer->render('model', stubReplacements());
    expect($content)
        ->toContain('class Product extends Model')
        ->toContain('namespace App\\Models');
});

it('renders migration stub without throwing', function () {
    $content = $this->renderer->render('migration', stubReplacements());
    expect($content)->toContain("Schema::create('products'");
});

it('renders controller stub without throwing', function () {
    $content = $this->renderer->render('controller', stubReplacements());
    expect($content)->toContain('class ProductController');
});

it('throws StubNotFoundException for missing stub', function () {
    expect(fn () => $this->renderer->render('this-does-not-exist'))
        ->toThrow(StubNotFoundException::class);
});

it('caches stub content on repeated calls', function () {
    $r = stubReplacements();
    $first = $this->renderer->render('model', $r);
    $second = $this->renderer->render('model', $r);
    expect($first)->toBe($second);
});

it('clears cache correctly', function () {
    $r = stubReplacements();
    $this->renderer->render('model', $r);
    $this->renderer->clearCache();
    $content = $this->renderer->render('model', $r);
    expect($content)->toContain('class Product extends Model');
});