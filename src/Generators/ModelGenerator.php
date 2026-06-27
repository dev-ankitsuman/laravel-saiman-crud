<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class ModelGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $path        = $this->configString('paths.model', 'app/Models');
        $namespace   = $this->configString('namespaces.model', 'App\\Models');
        $softDeletes = $this->configBool('soft_deletes', false);
        $timestamps  = $this->configBool('timestamps', true);
        $force       = (bool) ($options['force'] ?? false);

        $content = $this->renderStub('model', [
            '{{Namespace}}'        => $namespace,
            '{{fillable}}'         => $this->fieldParser->toFillable($this->fields),
            '{{softDeletesUse}}'   => $softDeletes ? "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n" : '',
            '{{softDeletesTrait}}' => $softDeletes ? "    use SoftDeletes;\n" : '',
            '{{timestamps}}'       => $timestamps ? '' : "\n    public \$timestamps = false;\n",
        ]);

        $filePath = $this->resolve("{$path}/{$this->nameResolver->getModelName()}.php");

        return [$this->writeFile($filePath, $content, $force)];
    }
}
