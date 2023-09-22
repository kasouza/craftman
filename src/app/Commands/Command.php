<?php


namespace App\Commands;

use Error;

class Command
{
    public function __construct(
        private string $name
    ) {
    }

    public function exec(array $options): bool
    {
        $options = $options;
        throw new Error('Not implemented');
        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
