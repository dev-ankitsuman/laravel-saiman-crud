<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class RepositoryGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function shouldRun(array $options = []): bool
    {
        return (bool) ($options['repository'] ?? false);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $path      = $this->configString('paths.repository', 'app/Repositories');
        $namespace = $this->configString('namespaces.repository', 'App\\Repositories');
        $force     = (bool) ($options['force'] ?? false);

        $shared = [
            '{{Namespace}}'      => $namespace,
            '{{ModelNamespace}}' => $this->configString('namespaces.model', 'App\\Models'),
        ];

        $generated = [];

        $generated[] = $this->writeFile(
            $this->resolve("{$path}/{$this->nameResolver->getRepositoryInterfaceName()}.php"),
            $this->renderStub('repository/interface', $shared),
            $force
        );

        $generated[] = $this->writeFile(
            $this->resolve("{$path}/{$this->nameResolver->getRepositoryName()}.php"),
            $this->renderStub('repository/repository', $shared),
            $force
        );

        return $generated;
    }
}
