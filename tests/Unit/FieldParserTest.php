<?php

declare(strict_types=1);

use Saiman\SaimanCrud\Exceptions\GeneratorException;
use Saiman\SaimanCrud\Support\FieldParser;

beforeEach(function () {
    $this->parser = new FieldParser();
});

it('returns empty array for empty string', function () {
    expect($this->parser->parse(''))->toBeEmpty();
});

it('parses single string field', function () {
    $fields = $this->parser->parse('name:string');
    expect($fields)->toHaveCount(1)
        ->and($fields[0]['name'])->toBe('name')
        ->and($fields[0]['type'])->toBe('string')
        ->and($fields[0]['migration_type'])->toBe('string')
        ->and($fields[0]['input_type'])->toBe('text')
        ->and($fields[0]['nullable'])->toBeFalse()
        ->and($fields[0]['label'])->toBe('Name');
});

it('parses multiple fields', function () {
    $fields = $this->parser->parse('name:string,price:decimal,active:boolean');
    expect($fields)->toHaveCount(3);
});

it('parses nullable modifier', function () {
    $fields = $this->parser->parse('description:text:nullable');
    expect($fields[0]['nullable'])->toBeTrue();
});

it('parses unique modifier', function () {
    $fields = $this->parser->parse('email:email:unique');
    expect($fields[0]['unique'])->toBeTrue();
});

it('maps bool to boolean migration type', function () {
    expect($this->parser->parse('active:bool')[0]['migration_type'])->toBe('boolean');
});

it('maps int to integer migration type', function () {
    expect($this->parser->parse('count:int')[0]['migration_type'])->toBe('integer');
});

it('generates required store validation rule', function () {
    $fields = $this->parser->parse('name:string');
    $rules = $this->parser->toStoreValidationRules($fields);
    expect($rules)->toContain("'name' => 'required|string|max:255'");
});

it('generates nullable store validation rule', function () {
    $fields = $this->parser->parse('bio:text:nullable');
    $rules = $this->parser->toStoreValidationRules($fields);
    expect($rules)->toContain("'bio' => 'nullable|string'");
});

it('adds sometimes prefix in update rules', function () {
    $fields = $this->parser->parse('name:string');
    $rules = $this->parser->toUpdateValidationRules($fields);
    expect($rules)->toContain('sometimes');
});

it('generates string migration column', function () {
    $fields = $this->parser->parse('name:string');
    $columns = $this->parser->toMigrationColumns($fields);
    expect($columns)->toContain("\$table->string('name')");
});

it('appends nullable to migration column', function () {
    $fields = $this->parser->parse('note:text:nullable');
    $columns = $this->parser->toMigrationColumns($fields);
    expect($columns)->toContain('->nullable()');
});

it('returns empty string for no fields', function () {
    expect($this->parser->toMigrationColumns([]))->toBe('');
});

it('generates text input for string field', function () {
    $inputs = $this->parser->toCreateFormInputs($this->parser->parse('name:string'));
    expect($inputs)->toContain('type="text"');
});

it('generates textarea for text field', function () {
    $inputs = $this->parser->toCreateFormInputs($this->parser->parse('description:text'));
    expect($inputs)->toContain('<textarea');
});

it('generates checkbox for boolean field', function () {
    $inputs = $this->parser->toCreateFormInputs($this->parser->parse('active:boolean'));
    expect($inputs)->toContain('type="checkbox"');
});

it('throws for missing field type', function () {
    expect(fn () => $this->parser->parse('name'))
        ->toThrow(GeneratorException::class);
});

it('throws for unsupported field type', function () {
    expect(fn () => $this->parser->parse('name:xml'))
        ->toThrow(GeneratorException::class);
});

it('returns list of supported types', function () {
    $types = $this->parser->supportedTypes();
    expect($types)->toContain('string')
        ->toContain('integer')
        ->toContain('boolean')
        ->toContain('email')
        ->toContain('uuid');
});