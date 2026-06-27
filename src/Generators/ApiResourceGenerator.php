<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class ApiResourceGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function shouldRun(array $options = []): bool
    {
        return (bool) ($options['api'] ?? false);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $path      = $this->configString('paths.resource', 'app/Http/Resources');
        $namespace = $this->configString('namespaces.resource', 'App\\Http\\Resources');
        $force     = (bool) ($options['force'] ?? false);

        $content = $this->renderStub('resource', [
            '{{Namespace}}'      => $namespace,
            '{{resourceFields}}' => $this->fieldParser->toResourceFields($this->fields),
        ]);

        $filePath = $this->resolve("{$path}/{$this->nameResolver->getResourceName()}.php");

        return [$this->writeFile($filePath, $content, $force)];
    }
}
