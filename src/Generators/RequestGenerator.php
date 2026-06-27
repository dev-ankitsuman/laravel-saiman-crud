<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class RequestGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $path      = $this->configString('paths.request', 'app/Http/Requests');
        $namespace = $this->configString('namespaces.request', 'App\\Http\\Requests');
        $force     = (bool) ($options['force'] ?? false);

        $generated = [];

        $storeContent = $this->renderStub('request.store', [
            '{{Namespace}}'       => $namespace,
            '{{validationRules}}' => $this->fieldParser->toStoreValidationRules($this->fields),
        ]);

        $generated[] = $this->writeFile(
            $this->resolve("{$path}/{$this->nameResolver->getStoreRequestName()}.php"),
            $storeContent,
            $force
        );

        $updateContent = $this->renderStub('request.update', [
            '{{Namespace}}'       => $namespace,
            '{{validationRules}}' => $this->fieldParser->toUpdateValidationRules($this->fields),
        ]);

        $generated[] = $this->writeFile(
            $this->resolve("{$path}/{$this->nameResolver->getUpdateRequestName()}.php"),
            $updateContent,
            $force
        );

        return $generated;
    }
}
