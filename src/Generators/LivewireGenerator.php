<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class LivewireGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function shouldRun(array $options = []): bool
    {
        return (bool) ($options['livewire'] ?? false);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $componentPath = $this->configString('paths.livewire', 'app/Livewire');
        $namespace     = $this->configString('namespaces.livewire', 'App\\Livewire');
        $viewPath      = $this->configString('paths.views', 'resources/views');
        $force         = (bool) ($options['force'] ?? false);
        $generated     = [];

        $extra = [
            '{{Namespace}}'      => $namespace,
            '{{ModelNamespace}}' => $this->configString('namespaces.model', 'App\\Models'),
            '{{createInputs}}'   => $this->fieldParser->toCreateFormInputs($this->fields),
            '{{tableHeaders}}'   => $this->fieldParser->toTableHeaders($this->fields),
            '{{tableCells}}'     => $this->fieldParser->toTableCells($this->fields, 'item'),
        ];

        $componentName = $this->nameResolver->getLivewireComponentName();
        $generated[]   = $this->writeFile(
            $this->resolve("{$componentPath}/{$componentName}.php"),
            $this->renderStub('livewire/component', $extra),
            $force
        );

        $viewFolder  = $this->nameResolver->getViewFolder();
        $generated[] = $this->writeFile(
            $this->resolve("{$viewPath}/livewire/{$viewFolder}/manager.blade.php"),
            $this->renderStub('livewire/view', $extra),
            $force
        );

        return $generated;
    }
}
