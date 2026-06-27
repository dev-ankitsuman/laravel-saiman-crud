<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Exceptions;

use RuntimeException;

final class StubNotFoundException extends RuntimeException
{
    /**
     * @param  array<int, string>  $searchedPaths
     */
    public static function forStub(string $stubName, array $searchedPaths): self
    {
        return new self(
            sprintf(
                'Stub [%s] not found. Searched in:%s',
                $stubName,
                PHP_EOL.'  - '.implode(PHP_EOL.'  - ', $searchedPaths)
            )
        );
    }
}
