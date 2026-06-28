<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Support;

use Saiman\SaimanCrud\Exceptions\GeneratorException;

final class FieldParser
{
    /**
     * @var array<string, string>
     */
    private const TYPE_MAP = [
        'bigint'    => 'bigInteger',
        'bool'      => 'boolean',
        'boolean'   => 'boolean',
        'date'      => 'date',
        'datetime'  => 'dateTime',
        'decimal'   => 'decimal',
        'double'    => 'double',
        'email'     => 'string',
        'enum'      => 'enum',
        'file'      => 'string',
        'float'     => 'float',
        'foreignId' => 'foreignId',
        'image'     => 'string',
        'int'       => 'integer',
        'integer'   => 'integer',
        'ip'        => 'ipAddress',
        'json'      => 'json',
        'longtext'  => 'longText',
        'password'  => 'string',
        'smallint'  => 'smallInteger',
        'string'    => 'string',
        'text'      => 'text',
        'time'      => 'time',
        'timestamp' => 'timestamp',
        'tinyint'   => 'tinyInteger',
        'url'       => 'string',
        'uuid'      => 'uuid',
    ];

    /**
     * @var array<string, string>
     */
    private const VALIDATION_MAP = [
        'bigint'    => 'integer',
        'bool'      => 'boolean',
        'boolean'   => 'boolean',
        'date'      => 'date',
        'datetime'  => 'date_format:Y-m-d H:i:s',
        'decimal'   => 'numeric',
        'double'    => 'numeric',
        'email'     => 'email:rfc,dns',
        'enum'      => 'string',
        'file'      => 'file',
        'float'     => 'numeric',
        'foreignId' => 'integer',
        'image'     => 'image|mimes:jpg,jpeg,png,gif,webp',
        'int'       => 'integer',
        'integer'   => 'integer',
        'ip'        => 'ip',
        'json'      => 'array',
        'longtext'  => 'string',
        'password'  => 'string|min:8',
        'smallint'  => 'integer',
        'string'    => 'string|max:255',
        'text'      => 'string',
        'time'      => 'date_format:H:i:s',
        'timestamp' => 'date_format:Y-m-d H:i:s',
        'tinyint'   => 'integer|min:0|max:127',
        'url'       => 'url',
        'uuid'      => 'uuid',
    ];

    /**
     * @var array<string, string>
     */
    private const INPUT_MAP = [
        'bigint'    => 'number',
        'bool'      => 'checkbox',
        'boolean'   => 'checkbox',
        'date'      => 'date',
        'datetime'  => 'datetime-local',
        'decimal'   => 'number',
        'double'    => 'number',
        'email'     => 'email',
        'enum'      => 'select',
        'file'      => 'file',
        'float'     => 'number',
        'foreignId' => 'number',
        'image'     => 'file',
        'int'       => 'number',
        'integer'   => 'number',
        'ip'        => 'text',
        'json'      => 'textarea',
        'longtext'  => 'textarea',
        'password'  => 'password',
        'smallint'  => 'number',
        'string'    => 'text',
        'text'      => 'textarea',
        'time'      => 'time',
        'timestamp' => 'datetime-local',
        'tinyint'   => 'number',
        'url'       => 'url',
        'uuid'      => 'text',
    ];

    /**
     * @var array<string, string>
     */
    private const FAKER_MAP = [
        'bigint'    => '$this->faker->randomNumber()',
        'bool'      => '$this->faker->boolean()',
        'boolean'   => '$this->faker->boolean()',
        'date'      => '$this->faker->date()',
        'datetime'  => '$this->faker->dateTime()->format(\'Y-m-d H:i:s\')',
        'decimal'   => '$this->faker->randomFloat(2, 0, 1000)',
        'double'    => '$this->faker->randomFloat(4, 0, 1000)',
        'email'     => '$this->faker->unique()->safeEmail()',
        'enum'      => '$this->faker->word()',
        'file'      => '$this->faker->word().\'.pdf\'',
        'float'     => '$this->faker->randomFloat(2)',
        'foreignId' => '1',
        'image'     => '$this->faker->imageUrl()',
        'int'       => '$this->faker->randomNumber()',
        'integer'   => '$this->faker->randomNumber()',
        'ip'        => '$this->faker->ipv4()',
        'json'      => '[]',
        'longtext'  => '$this->faker->text()',
        'password'  => 'bcrypt(\'password\')',
        'smallint'  => '$this->faker->numberBetween(0, 32767)',
        'string'    => '$this->faker->words(3, true)',
        'text'      => '$this->faker->paragraph()',
        'time'      => '$this->faker->time()',
        'timestamp' => '$this->faker->dateTime()->format(\'Y-m-d H:i:s\')',
        'tinyint'   => '$this->faker->numberBetween(0, 127)',
        'url'       => '$this->faker->url()',
        'uuid'      => '$this->faker->uuid()',
    ];

    /**
     * @return array<int, array<string, mixed>>
     *
     * @throws GeneratorException
     */
    public function parse(string $fieldsString): array
    {
        $fieldsString = trim($fieldsString);

        if ($fieldsString === '') {
            return [];
        }

        $fields = [];

        foreach (explode(',', $fieldsString) as $definition) {
            $definition = trim($definition);

            if ($definition === '') {
                continue;
            }

            $fields[] = $this->parseDefinition($definition);
        }

        return $fields;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws GeneratorException
     */
    private function parseDefinition(string $definition): array
    {
        $parts = explode(':', $definition);

        if (count($parts) < 2) {
            throw GeneratorException::invalidFieldDefinition(
                $definition,
                'Expected format is "name:type" or "name:type:nullable".'
            );
        }

        $name    = trim($parts[0]);
        $type    = strtolower(trim($parts[1]));
        $options = array_map('trim', array_slice($parts, 2));

        $this->validateFieldName($name, $definition);
        $this->validateFieldType($type);

        return [
            'name'           => $name,
            'type'           => $type,
            'migration_type' => self::TYPE_MAP[$type],
            'validation'     => self::VALIDATION_MAP[$type],
            'input_type'     => self::INPUT_MAP[$type],
            'faker'          => self::FAKER_MAP[$type],
            'nullable'       => in_array('nullable', $options, true),
            'unique'         => in_array('unique', $options, true),
            'unsigned'       => in_array('unsigned', $options, true),
            'options'        => $options,
            'label'          => $this->toLabel($name),
            'placeholder'    => 'Enter '.$this->toLabel($name),
        ];
    }

    /**
     * @throws GeneratorException
     */
    private function validateFieldName(string $name, string $definition): void
    {
        if ($name === '') {
            throw GeneratorException::invalidFieldDefinition(
                $definition,
                'Field name cannot be empty.'
            );
        }

        if (! preg_match('/^[a-z][a-z0-9_]*$/i', $name)) {
            throw GeneratorException::invalidFieldDefinition(
                $definition,
                "Field name [{$name}] must start with a letter and contain only letters, numbers, and underscores."
            );
        }
    }

    /**
     * @throws GeneratorException
     */
    private function validateFieldType(string $type): void
    {
        if (! array_key_exists($type, self::TYPE_MAP)) {
            throw GeneratorException::unsupportedFieldType(
                $type,
                array_keys(self::TYPE_MAP)
            );
        }
    }

    public function toLabel(string $name): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $name));
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toMigrationColumns(array $fields): string
    {
        if (empty($fields)) {
            return '';
        }

        $lines = [];

        foreach ($fields as $field) {
            /** @var string $migrationType */
            $migrationType = $field['migration_type'];
            /** @var string $name */
            $name     = $field['name'];
            $nullable = (bool) ($field['nullable'] ?? false);
            $unique   = (bool) ($field['unique'] ?? false);
            $unsigned = (bool) ($field['unsigned'] ?? false);

            $line = "\$table->{$migrationType}('{$name}')";

            if ($unsigned) {
                $line .= '->unsigned()';
            }

            if ($nullable) {
                $line .= '->nullable()';
            }

            if ($unique) {
                $line .= '->unique()';
            }

            $line .= ';';
            $lines[] = '            '.$line;
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toStoreValidationRules(array $fields): string
    {
        return $this->buildValidationRules($fields, false);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toUpdateValidationRules(array $fields): string
    {
        return $this->buildValidationRules($fields, true);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    private function buildValidationRules(array $fields, bool $isUpdate): string
    {
        if (empty($fields)) {
            return '';
        }

        $lines = [];

        foreach ($fields as $field) {
            /** @var string $name */
            $name = $field['name'];
            /** @var string $validation */
            $validation = $field['validation'];
            $nullable   = (bool) ($field['nullable'] ?? false);

            $parts = [];

            if ($isUpdate) {
                $parts[] = 'sometimes';
            }

            $parts[] = $nullable ? 'nullable' : 'required';
            $parts[] = $validation;

            $lines[] = "            '{$name}' => '".implode('|', $parts)."',";
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toFillable(array $fields): string
    {
        if (empty($fields)) {
            return '';
        }

        $lines = [];

        foreach ($fields as $field) {
            /** @var string $name */
            $name    = $field['name'];
            $lines[] = "        '{$name}',";
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toFactoryDefinition(array $fields): string
    {
        if (empty($fields)) {
            return '';
        }

        $lines = [];

        foreach ($fields as $field) {
            /** @var string $name */
            $name = $field['name'];
            /** @var string $faker */
            $faker   = $field['faker'];
            $lines[] = "            '{$name}' => {$faker},";
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toTableHeaders(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            /** @var string $label */
            $label   = $field['label'];
            $lines[] = "                <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">{$label}</th>";
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toTableCells(array $fields, string $variable): string
    {
        $lines = [];

        foreach ($fields as $field) {
            /** @var string $name */
            $name = $field['name'];
            /** @var string $type */
            $type = $field['type'];

            $value = match (true) {
                in_array($type, ['boolean', 'bool'], true)       => "{{ \${$variable}->{$name} ? 'Yes' : 'No' }}",
                $type === 'date'                                 => "{{ \${$variable}->{$name}?->format('Y-m-d') }}",
                in_array($type, ['datetime', 'timestamp'], true) => "{{ \${$variable}->{$name}?->format('Y-m-d H:i') }}",
                default                                          => "{{ \${$variable}->{$name} }}",
            };

            $lines[] = "                <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\">{$value}</td>";
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toCreateFormInputs(array $fields): string
    {
        $inputs = [];

        foreach ($fields as $field) {
            $inputs[] = $this->buildFormInput($field, '');
        }

        return implode(PHP_EOL.PHP_EOL, $inputs);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toEditFormInputs(array $fields, string $variable): string
    {
        $inputs = [];

        foreach ($fields as $field) {
            $inputs[] = $this->buildFormInput($field, $variable);
        }

        return implode(PHP_EOL.PHP_EOL, $inputs);
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private function buildFormInput(array $field, string $variable): string
    {
        /** @var string $inputType */
        $inputType = $field['input_type'];

        return match ($inputType) {
            'textarea' => $this->buildTextarea($field, $variable),
            'checkbox' => $this->buildCheckbox($field, $variable),
            'select'   => $this->buildSelect($field),
            default    => $this->buildTextInput($field, $variable),
        };
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private function buildTextInput(array $field, string $variable): string
    {
        /** @var string $name */
        $name = $field['name'];
        /** @var string $inputType */
        $inputType = $field['input_type'];
        /** @var string $label */
        $label = $field['label'];
        /** @var string $placeholder */
        $placeholder = $field['placeholder'];
        $nullable    = (bool) ($field['nullable'] ?? false);

        $oldValue = $variable !== ''
            ? "{{ old('{$name}', \${$variable}->{$name}) }}"
            : "{{ old('{$name}') }}";

        $required = $nullable ? '' : ' required';

        return <<<HTML
                <div class="form-group">
                    <label for="{$name}">{$label}</label>
                    <input type="{$inputType}" name="{$name}" id="{$name}" value="{$oldValue}" placeholder="{$placeholder}" class="@error('{$name}') error @enderror"{$required}>
                    @error('{$name}')
                        <div class="error-text">{{ \$message }}</div>
                    @enderror
                </div>
        HTML;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private function buildTextarea(array $field, string $variable): string
    {
        /** @var string $name */
        $name = $field['name'];
        /** @var string $label */
        $label = $field['label'];
        /** @var string $placeholder */
        $placeholder = $field['placeholder'];
        $nullable    = (bool) ($field['nullable'] ?? false);

        $oldValue = $variable !== ''
            ? "{{ old('{$name}', \${$variable}->{$name}) }}"
            : "{{ old('{$name}') }}";

        $required = $nullable ? '' : ' required';

        return <<<HTML
                <div class="form-group">
                    <label for="{$name}">{$label}</label>
                    <textarea name="{$name}" id="{$name}" rows="4" placeholder="{$placeholder}" class="@error('{$name}') error @enderror"{$required}>{$oldValue}</textarea>
                    @error('{$name}')
                        <div class="error-text">{{ \$message }}</div>
                    @enderror
                </div>
        HTML;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private function buildCheckbox(array $field, string $variable): string
    {
        /** @var string $name */
        $name = $field['name'];
        /** @var string $label */
        $label = $field['label'];

        $checked = $variable !== ''
            ? "@checked(old('{$name}', \${$variable}->{$name}))"
            : "@checked(old('{$name}'))";

        return <<<HTML
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="hidden" name="{$name}" value="0">
                        <input type="checkbox" name="{$name}" id="{$name}" value="1" {$checked}>
                        <label for="{$name}">{$label}</label>
                    </div>
                    @error('{$name}')
                        <div class="error-text">{{ \$message }}</div>
                    @enderror
                </div>
        HTML;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    private function buildSelect(array $field): string
    {
        /** @var string $name */
        $name = $field['name'];
        /** @var string $label */
        $label    = $field['label'];
        $nullable = (bool) ($field['nullable'] ?? false);
        $required = $nullable ? '' : ' required';

        return <<<HTML
                <div class="form-group">
                    <label for="{$name}">{$label}</label>
                    <select name="{$name}" id="{$name}" class="@error('{$name}') error @enderror"{$required}>
                        <option value="">-- Select {$label} --</option>
                    </select>
                    @error('{$name}')
                        <div class="error-text">{{ \$message }}</div>
                    @enderror
                </div>
        HTML;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toShowFields(array $fields, string $variable): string
    {
        $rows = [];

        foreach ($fields as $field) {
            /** @var string $name */
            $name = $field['name'];
            /** @var string $type */
            $type = $field['type'];
            /** @var string $label */
            $label = $field['label'];

            $value = match (true) {
                in_array($type, ['boolean', 'bool'], true)       => "{{ \${$variable}->{$name} ? 'Yes' : 'No' }}",
                $type === 'date'                                 => "{{ \${$variable}->{$name}?->format('Y-m-d') ?? '—' }}",
                in_array($type, ['datetime', 'timestamp'], true) => "{{ \${$variable}->{$name}?->format('Y-m-d H:i:s') ?? '—' }}",
                $type === 'url'                                  => "<a href=\"{{ \${$variable}->{$name} }}\" target=\"_blank\" style=\"color: #4f46e5;\">{{ \${$variable}->{$name} }}</a>",
                default                                          => "{{ \${$variable}->{$name} ?? '—' }}",
            };

            $rows[] = <<<HTML
        <div class="detail-row">
            <div class="detail-label">{$label}</div>
            <div class="detail-value">{$value}</div>
        </div>
        HTML;
        }

        return implode(PHP_EOL, $rows);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function toResourceFields(array $fields): string
    {
        $lines   = [];
        $lines[] = "            'id'         => \$this->id,";

        foreach ($fields as $field) {
            /** @var string $name */
            $name    = $field['name'];
            $lines[] = "            '{$name}' => \$this->{$name},";
        }

        $lines[] = "            'created_at' => \$this->created_at,";
        $lines[] = "            'updated_at' => \$this->updated_at,";

        return implode(PHP_EOL, $lines);
    }

    /**
     * @return array<int, string>
     */
    public function supportedTypes(): array
    {
        return array_keys(self::TYPE_MAP);
    }
}
