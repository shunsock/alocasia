<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Token\Asterisk;
use Alocasia\Interpreter\Token\Token;

class Evaluator
{
    public array $hashmap;
    public array $stack;
    public array $tokens;
    public function __construct(array $hashmap, array $stack, array $tokens) {
        $this->hashmap = $hashmap;
        $this->stack = $stack;
        $this->tokens = $tokens;
    }

    /**
     * @param list<Token> $tokens
     * @return Evaluator
     */
    public function evaluate(array $tokens): Evaluator {
        while(true) {
            match ($tokens) {
                [] => $this,
                default => $this->_evaluate()
            };
        }
    }

    private function _evaluate(): Evaluator
    {
        $evaluator = match (get_class($this->tokens[0])) {
            Asterisk::class => AsteriskEvaluator::evaluate(
                $this,
            ),
            default => $this,
        };

        $this->hashmap = $evaluator->hashmap;
        $this->stack = $evaluator->stack;
        $this->tokens = $evaluator->tokens;
        return $this;
    }
}