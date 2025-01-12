<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Token;

readonly class IntegerLiteral extends Token
{
    public int $value;

    public function __construct(int $line, int $position, int $value)
    {
        parent::__construct(line: $line, position: $position);
        $this->value = $value;
    }
}