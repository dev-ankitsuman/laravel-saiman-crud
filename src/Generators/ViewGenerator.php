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
        $layout     = $this->configString('views.layout', 'layouts.app');
        $force      = (bool) ($options['force'] ?? false);
        $variable   = $this->nameResolver->getModelVariable();
        $viewFolder = $this->nameResolver->getViewFolder();
        $generated  = [];

        $shared = [
            '{{layout}}'       => $layout,
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
}
