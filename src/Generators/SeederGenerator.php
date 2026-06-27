<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class SeederGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $path      = $this->configString('paths.seeder', 'database/seeders');
        $namespace = $this->configString('namespaces.seeder', 'Database\\Seeders');
        $force     = (bool) ($options['force'] ?? false);

        $content = $this->renderStub('seeder', [
            '{{Namespace}}'      => $namespace,
            '{{ModelNamespace}}' => $this->configString('namespaces.model', 'App\\Models'),
        ]);

        $filePath = $this->resolve("{$path}/{$this->nameResolver->getSeederName()}.php");

        return [$this->writeFile($filePath, $content, $force)];
    }
}
