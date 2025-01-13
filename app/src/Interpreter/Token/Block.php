<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Token;

readonly class Block extends Token
{
    /** @var Token[]  */
    public array $tokens;

    /**
     * @param Token[] $tokens
     */
    public function __construct(int $line, int $position, array $tokens) {
        parent::__construct(line: $line, position: $position);
        $this->tokens = $tokens;
    }
}