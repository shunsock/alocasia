<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Scanner;

use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Plus;
use Alocasia\Interpreter\Token\Token;

class Scanner
{
    public string $source;

    /**
     * @param string $source
     */
    public function __construct(string $source) {
        $this->source = $source;
    }

    /**
     * @return non-empty-list<Token>
     */
    public function scan(): array {
        return [
            new IntegerLiteral(1),
            new FloatLiteral(1.0),
            new Plus(),
        ];
    }
}