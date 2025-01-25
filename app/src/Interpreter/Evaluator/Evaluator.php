<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Token;

class Evaluator
{
    /** @var AlocasiaObject[]  */
    public array $hashmap;

    /** @var StackedItem[]  */
    public array $stack;

    /** @var Token[]  */
    public array $tokens;

    /**
     * @param array<string, AlocasiaObject> $hashmap
     * @param StackedItem[] $stack
     * @param Token[] $tokens
     */
    public function __construct(array $hashmap, array $stack, array $tokens) {
        $this->hashmap = $hashmap;
        $this->stack = $stack;
        $this->tokens = $tokens;
    }

    /**
     * @param Token[] $tokens
     * @return Evaluator
     * @throws EvaluatorException
     */
    public function evaluate(array $tokens): Evaluator {
        while($tokens) {
            $this->_evaluate();
        }
        return $this;
    }

    /**
     * @throws EvaluatorException
     */
    private function _evaluate(): void
    {
        $evaluator = match (get_class($this->tokens[0])) {
            IntegerLiteral::class => EvaluatorOfIntegerLiteral::evaluate($this),
            default => $this,
        };

        $this->hashmap = $evaluator->hashmap;
        $this->stack = $evaluator->stack;
        $this->tokens = $evaluator->tokens;
    }
}