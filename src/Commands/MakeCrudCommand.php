<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Saiman\SaimanCrud\Exceptions\GeneratorException;
use Saiman\SaimanCrud\Exceptions\StubNotFoundException;
use Saiman\SaimanCrud\Generators\ApiResourceGenerator;
use Saiman\SaimanCrud\Generators\BaseGenerator;
use Saiman\SaimanCrud\Generators\ControllerGenerator;
use Saiman\SaimanCrud\Generators\FactoryGenerator;
use Saiman\SaimanCrud\Generators\FilamentGenerator;
use Saiman\SaimanCrud\Generators\LivewireGenerator;
use Saiman\SaimanCrud\Generators\MigrationGenerator;
use Saiman\SaimanCrud\Generators\ModelGenerator;
use Saiman\SaimanCrud\Generators\RepositoryGenerator;
use Saiman\SaimanCrud\Generators\RequestGenerator;
use Saiman\SaimanCrud\Generators\RouteGenerator;
use Saiman\SaimanCrud\Generators\SeederGenerator;
use Saiman\SaimanCrud\Generators\ServiceGenerator;
use Saiman\SaimanCrud\Generators\ViewGenerator;
use Saiman\SaimanCrud\Support\FieldParser;
use Saiman\SaimanCrud\Support\NameResolver;
use Saiman\SaimanCrud\Support\StubRenderer;
use Throwable;

final class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud
                            {model              : The model name in PascalCase (e.g. Product, BlogPost)}
                            {--fields=          : Field definitions e.g. "name:string,price:decimal,active:boolean"}
                            {--api              : Generate API controller and resource (no Blade views)}
                            {--service          : Generate a Service class}
                            {--repository       : Generate Repository interface and implementation}
                            {--livewire         : Generate a Livewire CRUD component}
                            {--filament         : Generate a Filament resource}
                            {--force            : Overwrite existing files}';

    protected $description = 'Generate a complete CRUD module — Model, Migration, Controller, Requests, Views, Routes, Seeder, Factory and more';

    /**
     * @var array<string, array<int, string>>
     */
    private array $generated = [];

    /**
     * @var array<string, string>
     */
    private array $skipped = [];

    /**
     * @var array<string, string>
     */
    private array $failed = [];

    public function handle(): int
    {
        $rawModel = (string) $this->argument('model');

        if (! $this->isValidModelName($rawModel)) {
            $this->components->error(
                "Invalid model name [{$rawModel}]. Must start with uppercase and contain only letters/numbers (e.g. Product, BlogPost)."
            );

            return self::FAILURE;
        }

        $nameResolver = new NameResolver($rawModel);
        $fieldParser  = new FieldParser;
        $stubRenderer = new StubRenderer;
        $filesystem   = new Filesystem;

        $options = $this->resolveOptions();
        $fields  = $this->parseFields($fieldParser, $options['fields']);

        $this->printHeader($nameResolver, $fields, $options);

        foreach ($this->buildGenerators($filesystem, $stubRenderer, $nameResolver, $fieldParser) as $label => $generator) {
            if (! $generator->shouldRun($options)) {
                continue;
            }

            $this->runGenerator($label, $generator, $fields, $options);
        }

        $this->printSummary($nameResolver);

        return empty($this->failed) ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @return array<string, BaseGenerator>
     */
    private function buildGenerators(
        Filesystem $fs,
        StubRenderer $renderer,
        NameResolver $resolver,
        FieldParser $parser,
    ): array {
        $args = [$fs, $renderer, $resolver, $parser];

        return [
            'Model'        => new ModelGenerator(...$args),
            'Migration'    => new MigrationGenerator(...$args),
            'Controller'   => new ControllerGenerator(...$args),
            'Requests'     => new RequestGenerator(...$args),
            'Views'        => new ViewGenerator(...$args),
            'Routes'       => new RouteGenerator(...$args),
            'Seeder'       => new SeederGenerator(...$args),
            'Factory'      => new FactoryGenerator(...$args),
            'API Resource' => new ApiResourceGenerator(...$args),
            'Service'      => new ServiceGenerator(...$args),
            'Repository'   => new RepositoryGenerator(...$args),
            'Livewire'     => new LivewireGenerator(...$args),
            'Filament'     => new FilamentGenerator(...$args),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $options
     */
    private function runGenerator(
        string $label,
        BaseGenerator $generator,
        array $fields,
        array $options,
    ): void {
        try {
            $paths                   = $generator->withFields($fields)->generate($options);
            $this->generated[$label] = $paths;

            foreach ($paths as $path) {
                $display = str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
                $this->components->twoColumnDetail("  <fg=green>✓</> {$label}", "<fg=gray>{$display}</>");
            }
        } catch (GeneratorException $e) {
            $this->skipped[$label] = $e->getMessage();
            $this->components->twoColumnDetail("  <fg=yellow>⊘</> {$label}", "<fg=yellow>{$e->getMessage()}</>");
        } catch (StubNotFoundException $e) {
            $this->failed[$label] = $e->getMessage();
            $this->components->error("  Stub missing for [{$label}]: {$e->getMessage()}");
        } catch (Throwable $e) {
            $this->failed[$label] = $e->getMessage();
            $this->components->error("  [{$label}] failed: {$e->getMessage()}");

            if ($this->getOutput()->isVerbose()) {
                $this->line($e->getTraceAsString());
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parseFields(FieldParser $parser, string $fieldsString): array
    {
        if ($fieldsString === '') {
            return [];
        }

        try {
            return $parser->parse($fieldsString);
        } catch (GeneratorException $e) {
            $this->components->warn("Field parse error: {$e->getMessage()}");
            $this->components->warn('Continuing without field definitions.');

            return [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveOptions(): array
    {
        return [
            'fields'     => (string) ($this->option('fields') ?? ''),
            'api'        => $this->option('api')        === true,
            'service'    => $this->option('service')    === true,
            'repository' => $this->option('repository') === true,
            'livewire'   => $this->option('livewire')   === true,
            'filament'   => $this->option('filament')   === true,
            'force'      => $this->option('force')      === true,
        ];
    }

    private function isValidModelName(string $name): bool
    {
        return $name !== '' && (bool) preg_match('/^[A-Z][A-Za-z0-9]+$/', $name);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $options
     */
    private function printHeader(NameResolver $resolver, array $fields, array $options): void
    {
        $this->newLine();
        $this->line('  <fg=cyan;options=bold>Laravel Saiman CRUD Generator</>');
        $this->line('  ──────────────────────────────────────────');
        $this->components->twoColumnDetail('  Model', $resolver->getModelName());
        $this->components->twoColumnDetail('  Table', $resolver->getTableName());

        if (! empty($fields)) {
            $this->components->twoColumnDetail('  Fields', implode(', ', array_column($fields, 'name')));
        }

        $active = array_keys(array_filter([
            'api'        => (bool) ($options['api'] ?? false),
            'service'    => (bool) ($options['service'] ?? false),
            'repository' => (bool) ($options['repository'] ?? false),
            'livewire'   => (bool) ($options['livewire'] ?? false),
            'filament'   => (bool) ($options['filament'] ?? false),
            'force'      => (bool) ($options['force'] ?? false),
        ]));

        if (! empty($active)) {
            $this->components->twoColumnDetail('  Options', implode(', ', $active));
        }

        $this->line('  ──────────────────────────────────────────');
        $this->newLine();
    }

    private function printSummary(NameResolver $resolver): void
    {
        $fileCount    = array_sum(array_map('count', $this->generated));
        $skippedCount = count($this->skipped);
        $failedCount  = count($this->failed);

        $this->newLine();
        $this->line('  ──────────────────────────────────────────');

        if ($failedCount === 0) {
            $this->components->info("Done! {$fileCount} file(s) created, {$skippedCount} skipped.");
        } else {
            $this->components->warn("Completed with {$failedCount} failure(s). Review output above.");
        }

        $this->newLine();
        $this->line('  <fg=cyan>Next steps:</>');
        $this->line('    <fg=yellow>php artisan migrate</>');
        $this->line("    <fg=yellow>php artisan db:seed --class={$resolver->getSeederName()}</>");
        $this->newLine();
    }
}
