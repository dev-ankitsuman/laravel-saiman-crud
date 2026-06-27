<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class ControllerGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $isApi = (bool) ($options['api'] ?? false);
        $force = (bool) ($options['force'] ?? false);

        $basePath      = $this->configString('paths.controller', 'app/Http/Controllers');
        $baseNamespace = $this->configString('namespaces.controller', 'App\\Http\\Controllers');

        $path      = $isApi ? $basePath.'/Api' : $basePath;
        $namespace = $isApi ? $baseNamespace.'\\Api' : $baseNamespace;
        $stub      = $isApi ? 'controller.api' : 'controller';

        $content = $this->renderStub($stub, [
            '{{Namespace}}'         => $namespace,
            '{{ModelNamespace}}'    => $this->configString('namespaces.model', 'App\\Models'),
            '{{RequestNamespace}}'  => $this->configString('namespaces.request', 'App\\Http\\Requests'),
            '{{ResourceNamespace}}' => $this->configString('namespaces.resource', 'App\\Http\\Resources'),
            '{{paginationCount}}'   => (string) $this->configInt('pagination', 15),
        ]);

        $filePath = $this->resolve("{$path}/{$this->nameResolver->getControllerName()}.php");

        return [$this->writeFile($filePath, $content, $force)];
    }
}
