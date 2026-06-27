<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Support;

use Illuminate\Support\Str;

final class NameResolver
{
    private readonly string $modelName;

    public function __construct(string $modelName)
    {
        $this->modelName = Str::studly($modelName);
    }

    public function getModelName(): string
    {
        return $this->modelName;
    }

    public function getModelNamePlural(): string
    {
        return Str::plural($this->modelName);
    }

    public function getModelVariable(): string
    {
        return Str::camel($this->modelName);
    }

    public function getModelVariablePlural(): string
    {
        return Str::camel(Str::plural($this->modelName));
    }

    public function getModelSnake(): string
    {
        return Str::snake($this->modelName);
    }

    public function getModelSnakePlural(): string
    {
        return Str::snake(Str::plural($this->modelName));
    }

    public function getModelKebab(): string
    {
        return Str::kebab($this->modelName);
    }

    public function getModelKebabPlural(): string
    {
        return Str::kebab(Str::plural($this->modelName));
    }

    public function getTableName(): string
    {
        return $this->getModelSnakePlural();
    }

    public function getControllerName(): string
    {
        return $this->modelName.'Controller';
    }

    public function getStoreRequestName(): string
    {
        return 'Store'.$this->modelName.'Request';
    }

    public function getUpdateRequestName(): string
    {
        return 'Update'.$this->modelName.'Request';
    }

    public function getResourceName(): string
    {
        return $this->modelName.'Resource';
    }

    public function getServiceName(): string
    {
        return $this->modelName.'Service';
    }

    public function getRepositoryInterfaceName(): string
    {
        return $this->modelName.'RepositoryInterface';
    }

    public function getRepositoryName(): string
    {
        return $this->modelName.'Repository';
    }

    public function getSeederName(): string
    {
        return $this->modelName.'Seeder';
    }

    public function getFactoryName(): string
    {
        return $this->modelName.'Factory';
    }

    public function getViewFolder(): string
    {
        return $this->getModelSnakePlural();
    }

    public function getLivewireComponentName(): string
    {
        return $this->modelName.'Manager';
    }

    public function getFilamentResourceName(): string
    {
        return $this->modelName.'Resource';
    }

    /**
     * @return array<string, string>
     */
    public function toReplacementArray(): array
    {
        return [
            '{{ModelName}}'           => $this->getModelName(),
            '{{ModelNamePlural}}'     => $this->getModelNamePlural(),
            '{{modelVariable}}'       => $this->getModelVariable(),
            '{{modelVariablePlural}}' => $this->getModelVariablePlural(),
            '{{model_snake}}'         => $this->getModelSnake(),
            '{{model_snake_plural}}'  => $this->getModelSnakePlural(),
            '{{model-kebab}}'         => $this->getModelKebab(),
            '{{model-kebab-plural}}'  => $this->getModelKebabPlural(),
            '{{tableName}}'           => $this->getTableName(),
            '{{ControllerName}}'      => $this->getControllerName(),
            '{{StoreRequestName}}'    => $this->getStoreRequestName(),
            '{{UpdateRequestName}}'   => $this->getUpdateRequestName(),
            '{{ResourceName}}'        => $this->getResourceName(),
            '{{ServiceName}}'         => $this->getServiceName(),
            '{{RepositoryInterface}}' => $this->getRepositoryInterfaceName(),
            '{{RepositoryName}}'      => $this->getRepositoryName(),
            '{{SeederName}}'          => $this->getSeederName(),
            '{{FactoryName}}'         => $this->getFactoryName(),
            '{{viewFolder}}'          => $this->getViewFolder(),
            '{{LivewireComponent}}'   => $this->getLivewireComponentName(),
            '{{paginationCount}}'     => '15',
        ];
    }
}
