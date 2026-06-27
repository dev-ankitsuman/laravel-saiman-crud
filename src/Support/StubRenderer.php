<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Support;

use RuntimeException;
use Saiman\SaimanCrud\Exceptions\StubNotFoundException;

final class StubRenderer
{
    private readonly string $packageStubPath;

    private readonly string $publishedStubPath;

    /**
     * @var array<string, string>
     */
    private array $cache = [];

    public function __construct()
    {
        $this->packageStubPath   = dirname(__DIR__, 2).'/stubs';
        $this->publishedStubPath = base_path('stubs/saiman-crud');
    }

    /**
     * @param  array<string, string>  $replacements
     *
     * @throws StubNotFoundException
     */
    public function render(string $stubName, array $replacements = []): string
    {
        $raw = $this->getRaw($stubName);

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $raw
        );
    }

    /**
     * @throws StubNotFoundException
     * @throws RuntimeException
     */
    private function getRaw(string $stubName): string
    {
        if (isset($this->cache[$stubName])) {
            return $this->cache[$stubName];
        }

        $path    = $this->resolveStubPath($stubName);
        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException("Unable to read stub file: [{$path}]");
        }

        $this->cache[$stubName] = $content;

        return $content;
    }

    /**
     * @throws StubNotFoundException
     */
    private function resolveStubPath(string $stubName): string
    {
        $searched = [];

        $publishedPath = $this->publishedStubPath.'/'.$stubName.'.stub';
        $searched[]    = $publishedPath;

        if (file_exists($publishedPath)) {
            return $publishedPath;
        }

        $packagePath = $this->packageStubPath.'/'.$stubName.'.stub';
        $searched[]  = $packagePath;

        if (file_exists($packagePath)) {
            return $packagePath;
        }

        throw StubNotFoundException::forStub($stubName, $searched);
    }

    public function clearCache(): void
    {
        $this->cache = [];
    }
}
