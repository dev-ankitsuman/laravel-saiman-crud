<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class FactoryGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $path      = $this->configString('paths.factory', 'database/factories');
        $namespace = $this->configString('namespaces.factory', 'Database\\Factories');
        $force     = (bool) ($options['force'] ?? false);

        $content = $this->renderStub('factory', [
            '{{Namespace}}'         => $namespace,
            '{{ModelNamespace}}'    => $this->configString('namespaces.model', 'App\\Models'),
            '{{factoryDefinition}}' => $this->fieldParser->toFactoryDefinition($this->fields),
        ]);

        $filePath = $this->resolve("{$path}/{$this->nameResolver->getFactoryName()}.php");

        return [$this->writeFile($filePath, $content, $force)];
    }
}
