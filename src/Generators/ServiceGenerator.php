<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class ServiceGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function shouldRun(array $options = []): bool
    {
        return (bool) ($options['service'] ?? false);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $path      = $this->configString('paths.service', 'app/Services');
        $namespace = $this->configString('namespaces.service', 'App\\Services');
        $force     = (bool) ($options['force'] ?? false);

        $content = $this->renderStub('service', [
            '{{Namespace}}'      => $namespace,
            '{{ModelNamespace}}' => $this->configString('namespaces.model', 'App\\Models'),
        ]);

        $filePath = $this->resolve("{$path}/{$this->nameResolver->getServiceName()}.php");

        return [$this->writeFile($filePath, $content, $force)];
    }
}
