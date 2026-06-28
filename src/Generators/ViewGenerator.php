<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class ViewGenerator extends BaseGenerator
{
    private const VIEWS = ['index', 'create', 'edit', 'show'];

    /**
     * @param  array<string, mixed>  $options
     */
    public function shouldRun(array $options = []): bool
    {
        return ! (bool) ($options['api'] ?? false);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $viewsPath  = $this->configString('paths.views', 'resources/views');
        $force      = (bool) ($options['force'] ?? false);
        $variable   = $this->nameResolver->getModelVariable();
        $viewFolder = $this->nameResolver->getViewFolder();
        $generated  = [];

        // Detect or create layout
        $layoutName = $this->ensureLayout($viewsPath, $force);

        $shared = [
            '{{layout}}'       => $layoutName,
            '{{tableHeaders}}' => $this->fieldParser->toTableHeaders($this->fields),
            '{{tableCells}}'   => $this->fieldParser->toTableCells($this->fields, $variable),
            '{{createInputs}}' => $this->fieldParser->toCreateFormInputs($this->fields),
            '{{editInputs}}'   => $this->fieldParser->toEditFormInputs($this->fields, $variable),
            '{{showFields}}'   => $this->fieldParser->toShowFields($this->fields, $variable),
        ];

        foreach (self::VIEWS as $view) {
            $content     = $this->renderStub("views/{$view}", $shared);
            $path        = $this->resolve("{$viewsPath}/{$viewFolder}/{$view}.blade.php");
            $generated[] = $this->writeFile($path, $content, $force);
        }

        return $generated;
    }

    /**
     * Check if a layout file exists. If not, generate one from our stub.
     * Returns the layout name to use in @extends().
     */
    private function ensureLayout(string $viewsPath, bool $force): string
    {
        // Check common layout locations
        $commonLayouts = [
            'layouts.app'    => 'layouts/app.blade.php',
            'layouts.main'   => 'layouts/main.blade.php',
            'layouts.master' => 'layouts/master.blade.php',
            'layout'         => 'layout.blade.php',
            'app'            => 'app.blade.php',
        ];

        foreach ($commonLayouts as $bladeName => $relativePath) {
            $fullPath = $this->resolve("{$viewsPath}/{$relativePath}");

            if ($this->files->exists($fullPath)) {
                return $bladeName;
            }
        }

        // No layout found — generate our default layout
        $layoutPath = $this->resolve("{$viewsPath}/layouts/app.blade.php");

        if (! $this->files->exists($layoutPath) || $force) {
            $layoutContent = $this->stubRenderer->render(
                'layout',
                $this->nameResolver->toReplacementArray()
            );
            $this->writeFile($layoutPath, $layoutContent, true);
        }

        return 'layouts.app';
    }
}
