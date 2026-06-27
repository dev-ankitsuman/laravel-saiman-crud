<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class FilamentGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function shouldRun(array $options = []): bool
    {
        return (bool) ($options['filament'] ?? false);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $path      = $this->configString('paths.filament', 'app/Filament/Resources');
        $namespace = $this->configString('namespaces.filament', 'App\\Filament\\Resources');
        $force     = (bool) ($options['force'] ?? false);

        $content = $this->renderStub('filament/resource', [
            '{{Namespace}}'      => $namespace,
            '{{ModelNamespace}}' => $this->configString('namespaces.model', 'App\\Models'),
            '{{formSchema}}'     => $this->buildFormSchema(),
            '{{tableColumns}}'   => $this->buildTableColumns(),
        ]);

        $filePath = $this->resolve("{$path}/{$this->nameResolver->getFilamentResourceName()}.php");

        return [$this->writeFile($filePath, $content, $force)];
    }

    private function buildFormSchema(): string
    {
        if (empty($this->fields)) {
            return "            Forms\\Components\\TextInput::make('name')->required(),";
        }

        $lines = [];

        foreach ($this->fields as $field) {
            /** @var string $type */
            $type = $field['type'];
            /** @var string $name */
            $name     = $field['name'];
            $nullable = (bool) ($field['nullable'] ?? false);
            $req      = $nullable ? '' : '->required()';

            $lines[] = match (true) {
                in_array($type, ['text', 'longtext'], true) => "            Forms\\Components\\Textarea::make('{$name}'){$req},",

                in_array($type, ['boolean', 'bool'], true) => "            Forms\\Components\\Toggle::make('{$name}'),",

                in_array($type, ['integer', 'int', 'bigint', 'smallint', 'tinyint', 'float', 'double', 'decimal'], true) => "            Forms\\Components\\TextInput::make('{$name}')->numeric(){$req},",

                $type === 'date' => "            Forms\\Components\\DatePicker::make('{$name}'){$req},",

                in_array($type, ['datetime', 'timestamp'], true) => "            Forms\\Components\\DateTimePicker::make('{$name}'){$req},",

                $type === 'email' => "            Forms\\Components\\TextInput::make('{$name}')->email(){$req},",

                $type === 'url' => "            Forms\\Components\\TextInput::make('{$name}')->url(){$req},",

                $type === 'password' => "            Forms\\Components\\TextInput::make('{$name}')->password(){$req},",

                in_array($type, ['file', 'image'], true) => "            Forms\\Components\\FileUpload::make('{$name}'){$req},",

                default => "            Forms\\Components\\TextInput::make('{$name}'){$req},",
            };
        }

        return implode(PHP_EOL, $lines);
    }

    private function buildTableColumns(): string
    {
        if (empty($this->fields)) {
            return "            Tables\\Columns\\TextColumn::make('id')->sortable(),";
        }

        $lines = [];

        foreach ($this->fields as $field) {
            /** @var string $type */
            $type = $field['type'];
            /** @var string $name */
            $name = $field['name'];

            $lines[] = match (true) {
                in_array($type, ['boolean', 'bool'], true) => "            Tables\\Columns\\IconColumn::make('{$name}')->boolean(),",

                $type === 'image' => "            Tables\\Columns\\ImageColumn::make('{$name}'),",

                $type === 'date' => "            Tables\\Columns\\TextColumn::make('{$name}')->date()->sortable(),",

                in_array($type, ['datetime', 'timestamp'], true) => "            Tables\\Columns\\TextColumn::make('{$name}')->dateTime()->sortable(),",

                default => "            Tables\\Columns\\TextColumn::make('{$name}')->searchable()->sortable(),",
            };
        }

        return implode(PHP_EOL, $lines);
    }
}
