<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

use Illuminate\Filesystem\Filesystem;
use Saiman\SaimanCrud\Exceptions\GeneratorException;
use Saiman\SaimanCrud\Generators\Contracts\GeneratorInterface;
use Saiman\SaimanCrud\Support\FieldParser;
use Saiman\SaimanCrud\Support\NameResolver;
use Saiman\SaimanCrud\Support\StubRenderer;

abstract class BaseGenerator implements GeneratorInterface
{
    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $fields = [];

    public function __construct(
        protected readonly Filesystem $files,
        protected readonly StubRenderer $stubRenderer,
        protected readonly NameResolver $nameResolver,
        protected readonly FieldParser $fieldParser,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    final public function withFields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function shouldRun(array $options = []): bool
    {
        return true;
    }

    /**
     * @param  array<string, string>  $extra
     */
    protected function renderStub(string $stubName, array $extra = []): string
    {
        $replacements = array_merge(
            $this->nameResolver->toReplacementArray(),
            $extra
        );

        return $this->stubRenderer->render($stubName, $replacements);
    }

    /**
     * @throws GeneratorException
     */
    protected function writeFile(string $path, string $content, bool $force = false): string
    {
        if ($this->files->exists($path) && ! $force) {
            throw GeneratorException::fileAlreadyExists($path);
        }

        $this->ensureDirectory(dirname($path));

        if ($this->files->put($path, $content) === false) {
            throw GeneratorException::fileWriteFailed($path);
        }

        return $path;
    }

    /**
     * @throws GeneratorException
     */
    protected function ensureDirectory(string $directory): void
    {
        if (! $this->files->isDirectory($directory)) {
            if (! $this->files->makeDirectory($directory, 0755, true, true)) {
                throw GeneratorException::directoryCreationFailed($directory);
            }
        }
    }

    protected function resolve(string $relativePath): string
    {
        return base_path($relativePath);
    }

    protected function configString(string $key, string $default): string
    {
        $value = config('saiman-crud.'.$key);

        if (! is_string($value) || $value === '') {
            return $default;
        }

        return $value;
    }

    protected function configBool(string $key, bool $default = false): bool
    {
        $value = config('saiman-crud.'.$key);

        if (! is_bool($value)) {
            return $default;
        }

        return $value;
    }

    protected function configInt(string $key, int $default): int
    {
        $value = config('saiman-crud.'.$key);

        if (! is_int($value)) {
            return $default;
        }

        return $value;
    }
}
