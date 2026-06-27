<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Tests;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as Orchestra;
use Saiman\SaimanCrud\SaimanCrudServiceProvider;

abstract class TestCase extends Orchestra
{
    protected Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
    }

    protected function getPackageProviders($app): array
    {
        return [
            SaimanCrudServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('app.key', 'base64:2fl+Ktvkfl+Fuz4Qp/A75G2RTiWVA/ZoKZvp6fiiM10=');
    }

    protected function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($this->files->exists($path)) {
                $this->files->delete($path);
            }
        }
    }

    protected function deleteDirectory(string $path): void
    {
        if ($this->files->isDirectory($path)) {
            $this->files->deleteDirectory($path);
        }
    }

    protected function assertFileContains(string $path, string $needle): void
    {
        $this->assertFileExists($path);
        $this->assertStringContainsString($needle, (string) $this->files->get($path));
    }

    protected function assertFileNotContains(string $path, string $needle): void
    {
        $this->assertFileExists($path);
        $this->assertStringNotContainsString($needle, (string) $this->files->get($path));
    }
}