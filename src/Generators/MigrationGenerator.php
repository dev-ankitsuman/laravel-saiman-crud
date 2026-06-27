<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class MigrationGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $path        = $this->configString('paths.migration', 'database/migrations');
        $softDeletes = $this->configBool('soft_deletes', false);
        $force       = (bool) ($options['force'] ?? false);

        $content = $this->renderStub('migration', [
            '{{columns}}'     => $this->fieldParser->toMigrationColumns($this->fields),
            '{{softDeletes}}' => $softDeletes ? '            $table->softDeletes();'.PHP_EOL : '',
        ]);

        $timestamp = now()->format('Y_m_d_His');
        $fileName  = "{$timestamp}_create_{$this->nameResolver->getTableName()}_table.php";
        $filePath  = $this->resolve("{$path}/{$fileName}");

        return [$this->writeFile($filePath, $content, $force)];
    }
}
