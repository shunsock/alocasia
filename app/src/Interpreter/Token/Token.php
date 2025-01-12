<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Token;

readonly class Token {
    public int $line;
    public int $position;

    public function __construct(int $line, int $position) {
        $this->line = $line;
        $this->position = $position;
    }
}