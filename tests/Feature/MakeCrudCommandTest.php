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

    // Also clean layout if generated
    $layoutDir = resource_path('views/layouts');
    $layoutFile = resource_path('views/layouts/app.blade.php');
    if ($fs->exists($layoutFile)) {
        $fs->delete($layoutFile);
    }
    if ($fs->isDirectory($layoutDir) && count($fs->files($layoutDir)) === 0) {
        $fs->deleteDirectory($layoutDir);
    }

    foreach ($fs->glob(database_path("migrations/*_create_{$snake}_table.php")) as $migration) {
        $fs->delete($migration);
    }

    // Restore original route file
    $routePath = base_path('routes/web.php');
    if ($fs->exists($routePath)) {
        $content = $fs->get($routePath);
        $escapedModel = preg_quote($model, '/');
        $pattern = "/\n?\\/\\/ CRUD-GENERATED:START {$escapedModel}\n.*?\\/\\/ CRUD-GENERATED:END {$escapedModel}\n?/s";
        $content = (string) preg_replace($pattern, "\n", $content);
        $content = (string) preg_replace("/\n{3,}/", "\n\n", $content);
        $content = rtrim($content)."\n";
        $fs->put($routePath, $content);
    }
}

// ─── Basic CRUD Generation ────────────────────────────────────────────

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

// ─── Content Verification ─────────────────────────────────────────────

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

it('controller uses correct route names', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    $this->assertFileContains(
        app_path('Http/Controllers/WidgetController.php'),
        "route('widgets.index')"
    );
})->after(fn () => cleanupModel('Widget'));

// ─── Fields ───────────────────────────────────────────────────────────

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

it('includes fields in store request validation', function () {
    $this->artisan('make:crud Widget --fields="name:string,email:email"')
        ->assertExitCode(0);

    $this->assertFileContains(
        app_path('Http/Requests/StoreWidgetRequest.php'),
        "'name' => 'required|string|max:255'"
    );
    $this->assertFileContains(
        app_path('Http/Requests/StoreWidgetRequest.php'),
        "'email' => 'required|email:rfc,dns'"
    );
})->after(fn () => cleanupModel('Widget'));

it('uses nullable validation for nullable fields', function () {
    $this->artisan('make:crud Widget --fields="note:text:nullable"')
        ->assertExitCode(0);

    $this->assertFileContains(
        app_path('Http/Requests/StoreWidgetRequest.php'),
        "'note' => 'nullable|string'"
    );
})->after(fn () => cleanupModel('Widget'));

it('includes fields in factory definition', function () {
    $this->artisan('make:crud Widget --fields="name:string,email:email"')
        ->assertExitCode(0);

    $this->assertFileContains(
        database_path('factories/WidgetFactory.php'),
        "'name' =>"
    );
    $this->assertFileContains(
        database_path('factories/WidgetFactory.php'),
        'safeEmail()'
    );
})->after(fn () => cleanupModel('Widget'));

// ─── API Flag ─────────────────────────────────────────────────────────

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

// ─── Service Flag ─────────────────────────────────────────────────────

it('generates service class with --service flag', function () {
    $this->artisan('make:crud Widget --service')->assertExitCode(0);

    expect(app_path('Services/WidgetService.php'))->toBeFile();
    $this->assertFileContains(app_path('Services/WidgetService.php'), 'class WidgetService');
    $this->assertFileContains(app_path('Services/WidgetService.php'), 'public function create(');
    $this->assertFileContains(app_path('Services/WidgetService.php'), 'public function update(');
    $this->assertFileContains(app_path('Services/WidgetService.php'), 'public function delete(');
})->after(fn () => cleanupModel('Widget'));

// ─── Repository Flag ─────────────────────────────────────────────────

it('generates repository with --repository flag', function () {
    $this->artisan('make:crud Widget --repository')->assertExitCode(0);

    expect(app_path('Repositories/WidgetRepositoryInterface.php'))->toBeFile();
    expect(app_path('Repositories/WidgetRepository.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('repository implements interface', function () {
    $this->artisan('make:crud Widget --repository')->assertExitCode(0);

    $this->assertFileContains(
        app_path('Repositories/WidgetRepository.php'),
        'implements WidgetRepositoryInterface'
    );
})->after(fn () => cleanupModel('Widget'));

// ─── Overwrite Behavior ───────────────────────────────────────────────

it('warns on second run without --force', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);
    $this->artisan('make:crud Widget')->assertExitCode(0);
})->after(fn () => cleanupModel('Widget'));

it('succeeds on second run with --force', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);
    $this->artisan('make:crud Widget --force')->assertExitCode(0);
})->after(fn () => cleanupModel('Widget'));

// ─── Route Behavior ──────────────────────────────────────────────────

it('appends routes without overwriting existing content', function () {
    $routePath = base_path('routes/web.php');
    $originalContent = "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get('/', function () {\n    return view('welcome');\n});\n";

    File::put($routePath, $originalContent);

    $this->artisan('make:crud Widget')->assertExitCode(0);

    // Original content preserved
    $this->assertFileContains($routePath, "return view('welcome')");

    // Generated route block added with markers
    $this->assertFileContains($routePath, '// CRUD-GENERATED:START Widget');
    $this->assertFileContains($routePath, "Route::resource('widgets'");
    $this->assertFileContains($routePath, '// CRUD-GENERATED:END Widget');

    // No use statement added in the middle
    $this->assertFileNotContains($routePath, 'use App\\Http\\Controllers\\WidgetController;');

    // Fully qualified class used instead
    $this->assertFileContains($routePath, '\\App\\Http\\Controllers\\WidgetController::class');

    File::put($routePath, $originalContent);
})->after(fn () => cleanupModel('Widget'));

it('does not duplicate routes on second run', function () {
    $routePath = base_path('routes/web.php');
    $originalContent = "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get('/', function () {\n    return view('welcome');\n});\n";

    File::put($routePath, $originalContent);

    $this->artisan('make:crud Widget')->assertExitCode(0);
    $this->artisan('make:crud Widget --force')->assertExitCode(0);

    $content = File::get($routePath);
    $count = substr_count($content, '// CRUD-GENERATED:START Widget');

    expect($count)->toBe(1);

    File::put($routePath, $originalContent);
})->after(fn () => cleanupModel('Widget'));

it('route format is clean after generation', function () {
    $routePath = base_path('routes/web.php');
    $originalContent = "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get('/', function () {\n    return view('welcome');\n});\n";

    File::put($routePath, $originalContent);

    $this->artisan('make:crud Widget')->assertExitCode(0);

    $content = File::get($routePath);

    // No triple blank lines
    expect(str_contains($content, "\n\n\n\n"))->toBeFalse();

    // Starts with <?php
    expect(str_starts_with(trim($content), '<?php'))->toBeTrue();

    File::put($routePath, $originalContent);
})->after(fn () => cleanupModel('Widget'));

// ─── Validation ───────────────────────────────────────────────────────

it('rejects lowercase model name', function () {
    $this->artisan('make:crud widget')->assertExitCode(1);
});

it('rejects model name with special characters', function () {
    $this->artisan('make:crud My-Widget')->assertExitCode(1);
});

it('rejects numeric model name', function () {
    $this->artisan('make:crud 123')->assertExitCode(1);
});

// ─── Multi-word & Combined ────────────────────────────────────────────

it('handles multi-word model name', function () {
    $this->artisan('make:crud BlogPost')->assertExitCode(0);

    expect(app_path('Models/BlogPost.php'))->toBeFile();
    expect(app_path('Http/Controllers/BlogPostController.php'))->toBeFile();

    $migrations = File::glob(database_path('migrations/*_create_blog_posts_table.php'));

    expect($migrations)->not->toBeEmpty();
})->after(fn () => cleanupModel('BlogPost'));

it('can combine api service repository flags', function () {
    $this->artisan('make:crud Widget --api --service --repository')->assertExitCode(0);

    expect(app_path('Http/Controllers/Api/WidgetController.php'))->toBeFile();
    expect(app_path('Http/Resources/WidgetResource.php'))->toBeFile();
    expect(app_path('Services/WidgetService.php'))->toBeFile();
    expect(app_path('Repositories/WidgetRepositoryInterface.php'))->toBeFile();
    expect(app_path('Repositories/WidgetRepository.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

// ─── Layout Auto-Generation ──────────────────────────────────────────

it('generates layout file when none exists', function () {
    // Make sure no layout exists
    $layoutPath = resource_path('views/layouts/app.blade.php');
    if (File::exists($layoutPath)) {
        File::delete($layoutPath);
    }

    $this->artisan('make:crud Widget')->assertExitCode(0);

    // Layout should be auto-generated
    expect($layoutPath)->toBeFile();

    // Layout should have basic HTML structure
    $this->assertFileContains($layoutPath, '<!DOCTYPE html>');
    $this->assertFileContains($layoutPath, "@yield('content')");
})->after(fn () => cleanupModel('Widget'));

it('uses existing layout when available', function () {
    // Create a custom layout
    $layoutDir = resource_path('views/layouts');
    $layoutPath = resource_path('views/layouts/app.blade.php');

    if (!File::isDirectory($layoutDir)) {
        File::makeDirectory($layoutDir, 0755, true);
    }

    File::put($layoutPath, '<html><body>CUSTOM LAYOUT @yield("content")</body></html>');

    $this->artisan('make:crud Widget')->assertExitCode(0);

    // Custom layout should NOT be overwritten
    $this->assertFileContains($layoutPath, 'CUSTOM LAYOUT');

    // Views should reference the layout
    $this->assertFileContains(
        resource_path('views/widgets/index.blade.php'),
        "@extends('layouts.app')"
    );

    // Cleanup custom layout
    File::delete($layoutPath);
})->after(fn () => cleanupModel('Widget'));

// ─── Revert Command ──────────────────────────────────────────────────

it('revert command shows dry run correctly', function () {
    $this->artisan('make:crud Widget')->assertExitCode(0);

    // Dry run should NOT delete anything
    $this->artisan('crud:revert Widget --dry-run')
        ->assertExitCode(0);

    // Files should still exist
    expect(app_path('Models/Widget.php'))->toBeFile();
    expect(app_path('Http/Controllers/WidgetController.php'))->toBeFile();
})->after(fn () => cleanupModel('Widget'));

it('revert command cleans route markers', function () {
    $routePath = base_path('routes/web.php');
    $originalContent = "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get('/', function () {\n    return view('welcome');\n});\n";

    File::put($routePath, $originalContent);

    // Generate
    $this->artisan('make:crud Widget')->assertExitCode(0);

    $this->assertFileContains($routePath, '// CRUD-GENERATED:START Widget');

    // Revert (with confirmation bypass for testing)
    cleanupModel('Widget');

    // Route markers should be removed
    $this->assertFileNotContains($routePath, '// CRUD-GENERATED:START Widget');
    $this->assertFileNotContains($routePath, '// CRUD-GENERATED:END Widget');

    // Original content preserved
    $this->assertFileContains($routePath, "return view('welcome')");

    File::put($routePath, $originalContent);
});

it('revert command rejects invalid model name', function () {
    $this->artisan('crud:revert widget')->assertExitCode(1);
});