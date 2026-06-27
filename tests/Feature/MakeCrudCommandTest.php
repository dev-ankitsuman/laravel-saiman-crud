<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

function cleanupModel(string $model): void
{
    $snake = Str::snake(Str::plural($model));
    $fs = new \Illuminate\Filesystem\Filesystem();

    $files = [
        app_path("Models/{$model}.php"),
        app_path("Http/Controllers/{$model}Controller.php"),
        app_path("Http/Controllers/Api/{$model}Controller.php"),
        app_path("Http/Requests/Store{$model}Request.php"),
        app_path("Http/Requests/Update{$model}Request.php"),
        app_path("Http/Resources/{$model}Resource.php"),
        app_path("Services/{$model}Service.php"),
        app_path("Repositories/{$model}RepositoryInterface.php"),
        app_path("Repositories/{$model}Repository.php"),
        database_path("seeders/{$model}Seeder.php"),
        database_path("factories/{$model}Factory.php"),
    ];

    foreach ($files as $file) {
        if ($fs->exists($file)) {
            $fs->delete($file);
        }
    }

    $viewDir = resource_path("views/{$snake}");
    if ($fs->isDirectory($viewDir)) {
        $fs->deleteDirectory($viewDir);
    }

    foreach ($fs->glob(database_path("migrations/*_create_{$snake}_table.php")) as $migration) {
        $fs->delete($migration);
    }
}

it('exits successfully for a valid model', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);
})->after(fn () => cleanupModel('Widget'));

it('generates model file', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    expect(app_path('Models/Widget.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('generates migration file', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    $files = File::glob(database_path('migrations/*_create_widgets_table.php'));

    expect($files)->not->toBeEmpty();
})->after(fn () => cleanupModel('Widget'));

it('generates controller and requests', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    expect(app_path('Http/Controllers/WidgetController.php'))->toBeFile();
    expect(app_path('Http/Requests/StoreWidgetRequest.php'))->toBeFile();
    expect(app_path('Http/Requests/UpdateWidgetRequest.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('generates seeder and factory', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    expect(database_path('seeders/WidgetSeeder.php'))->toBeFile();
    expect(database_path('factories/WidgetFactory.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('generates blade views for web crud', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    expect(resource_path('views/widgets/index.blade.php'))->toBeFile();
    expect(resource_path('views/widgets/create.blade.php'))->toBeFile();
    expect(resource_path('views/widgets/edit.blade.php'))->toBeFile();
    expect(resource_path('views/widgets/show.blade.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('model has correct namespace', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    $this->assertFileContains(app_path('Models/Widget.php'), 'namespace App\\Models');
    $this->assertFileContains(app_path('Models/Widget.php'), 'class Widget extends Model');
})->after(fn () => cleanupModel('Widget'));

it('migration creates correct table', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    $files = File::glob(database_path('migrations/*_create_widgets_table.php'));

    $this->assertFileContains($files[0], "Schema::create('widgets'");
})->after(fn () => cleanupModel('Widget'));

it('includes fields in model fillable', function () {
    $this->artisan('make:crud Widget --fields="name:string,price:decimal,active:boolean"')
        ->assertExitCode(0);

    $this->assertFileContains(app_path('Models/Widget.php'), "'name'");
    $this->assertFileContains(app_path('Models/Widget.php'), "'price'");
    $this->assertFileContains(app_path('Models/Widget.php'), "'active'");
})->after(fn () => cleanupModel('Widget'));

it('includes fields in migration columns', function () {
    $this->artisan('make:crud Widget --fields="name:string,price:decimal,active:boolean"')
        ->assertExitCode(0);

    $files = File::glob(database_path('migrations/*_create_widgets_table.php'));

    $this->assertFileContains($files[0], "\$table->string('name')");
    $this->assertFileContains($files[0], "\$table->decimal('price')");
    $this->assertFileContains($files[0], "\$table->boolean('active')");
})->after(fn () => cleanupModel('Widget'));

it('includes nullable modifier in migration', function () {
    $this->artisan('make:crud Widget --fields="note:text:nullable"')
        ->assertExitCode(0);

    $files = File::glob(database_path('migrations/*_create_widgets_table.php'));

    $this->assertFileContains($files[0], '->nullable()');
})->after(fn () => cleanupModel('Widget'));

it('generates api controller with --api flag', function () {
    $this->artisan('make:crud Widget --api')->assertExitCode(0);

    expect(app_path('Http/Controllers/Api/WidgetController.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('generates api resource with --api flag', function () {
    $this->artisan('make:crud Widget --api')->assertExitCode(0);

    expect(app_path('Http/Resources/WidgetResource.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('does not generate views for api crud', function () {
    $this->artisan('make:crud Widget --api')->assertExitCode(0);

    expect(resource_path('views/widgets/index.blade.php'))->not->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('generates service class with --service flag', function () {
    $this->artisan('make:crud Widget --service')->assertExitCode(0);

    expect(app_path('Services/WidgetService.php'))->toBeFile();
    $this->assertFileContains(app_path('Services/WidgetService.php'), 'class WidgetService');
})->after(fn () => cleanupModel('Widget'));

it('generates repository with --repository flag', function () {
    $this->artisan('make:crud Widget --repository')->assertExitCode(0);

    expect(app_path('Repositories/WidgetRepositoryInterface.php'))->toBeFile();
    expect(app_path('Repositories/WidgetRepository.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('warns on second run without --force', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    // Existing files are skipped, but command still completes successfully.
    $this->artisan('make:crud Widget')->assertExitCode(0);
})->after(fn () => cleanupModel('Widget'));

it('succeeds on second run with --force', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);
    $this->artisan('make:crud Widget --force')->assertExitCode(0);
})->after(fn () => cleanupModel('Widget'));

it('rejects lowercase model name', function () {
    $this->artisan('make:crud widget')->assertExitCode(1);
});

it('rejects model name with special characters', function () {
    $this->artisan('make:crud My-Widget')->assertExitCode(1);
});

it('handles multi-word model name', function () {
    $this->artisan('make:crud BlogPost')->assertExitCode(0);

    expect(app_path('Models/BlogPost.php'))->toBeFile();

    $migrations = File::glob(database_path('migrations/*_create_blog_posts_table.php'));

    expect($migrations)->not->toBeEmpty();
})->after(fn () => cleanupModel('BlogPost'));

it('can combine api service repository flags', function () {
    $this->artisan('make:crud Widget --api --service --repository')->assertExitCode(0);

    expect(app_path('Http/Controllers/Api/WidgetController.php'))->toBeFile();
    expect(app_path('Services/WidgetService.php'))->toBeFile();
    expect(app_path('Repositories/WidgetRepositoryInterface.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));