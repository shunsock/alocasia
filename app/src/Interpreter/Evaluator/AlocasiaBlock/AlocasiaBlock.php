<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator\AlocasiaBlock;

use Alocasia\Interpreter\Evaluator\StackedItem;
use Alocasia\Interpreter\Token\Token;

readonly class AlocasiaBlock extends StackedItem
{
    /** @var Token[]  */
    public array $tokens;

    /**
     * @param Token[] $tokens
     */
    public function __construct(array $tokens) {
        $this->tokens = $tokens;
    }
}