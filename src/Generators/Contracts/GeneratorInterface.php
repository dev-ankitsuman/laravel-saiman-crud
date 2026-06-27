<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators\Contracts;

interface GeneratorInterface
{
    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array;

    /**
     * @param  array<string, mixed>  $options
     */
    public function shouldRun(array $options = []): bool;
}
