<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Exceptions;

use RuntimeException;

final class GeneratorException extends RuntimeException
{
    public static function fileAlreadyExists(string $path): self
    {
        return new self(
            sprintf('File already exists: [%s]. Use --force to overwrite.', $path)
        );
    }

    public static function directoryCreationFailed(string $path): self
    {
        return new self(
            sprintf('Failed to create directory: [%s]. Check permissions.', $path)
        );
    }

    public static function fileWriteFailed(string $path): self
    {
        return new self(
            sprintf('Failed to write file: [%s]. Check permissions.', $path)
        );
    }

    public static function invalidFieldDefinition(string $definition, string $reason): self
    {
        return new self(
            sprintf('Invalid field definition [%s]: %s', $definition, $reason)
        );
    }

    /**
     * @param  array<int, string>  $supported
     */
    public static function unsupportedFieldType(string $type, array $supported): self
    {
        return new self(
            sprintf(
                'Unsupported field type [%s]. Supported types: %s',
                $type,
                implode(', ', $supported)
            )
        );
    }
}
