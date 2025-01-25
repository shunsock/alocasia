<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator\AlocasiaBlock;

use Alocasia\Interpreter\Evaluator\StackedItem;
use Alocasia\Interpreter\Token\Token;

readonly class AlocasiaBlock extends StackedItem
{
    public int $line;

    public int $position;

    /** @var Token[]  */
    public array $tokens;

    /**
     * @param Token[] $tokens
     */
    public function __construct(int $line, int $position, array $tokens) {
        $this->line = $line;
        $this->position = $position;
        $this->tokens = $tokens;
    }
}