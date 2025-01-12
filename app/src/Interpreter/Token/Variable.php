<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Token;

readonly class Variable extends Token {
    public string $name;
    public function __construct(int $line, int $position, string $name)
    {
        parent::__construct(line: $line, position: $position);
        $this->name = $name;
    }
}