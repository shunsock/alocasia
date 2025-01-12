<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Token;

readonly class FloatLiteral extends Token
{
    public float $value;

    public function __construct(int $line, int $position, float $value)
    {
        parent::__construct(line: $line, position: $position);
        $this->value = $value;
    }
}