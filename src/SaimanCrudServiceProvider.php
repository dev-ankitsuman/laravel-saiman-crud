<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud;

use Illuminate\Support\ServiceProvider;
use Saiman\SaimanCrud\Commands\MakeCrudCommand;
use Saiman\SaimanCrud\Commands\RevertCrudCommand;

final class SaimanCrudServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/saiman-crud.php',
            'saiman-crud'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerPublishing();
        }
    }

    private function registerCommands(): void
    {
        $this->commands([
            MakeCrudCommand::class,
            RevertCrudCommand::class,
        ]);
    }

    private function registerPublishing(): void
    {
        $basePath = dirname(__DIR__);

        $this->publishes([
            $basePath.'/config/saiman-crud.php' => config_path('saiman-crud.php'),
        ], ['saiman-crud-config', 'saiman-crud']);

        $this->publishes([
            $basePath.'/stubs' => base_path('stubs/saiman-crud'),
        ], ['saiman-crud-stubs', 'saiman-crud']);
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            MakeCrudCommand::class,
            RevertCrudCommand::class,
        ];
    }
}
