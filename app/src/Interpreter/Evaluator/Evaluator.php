<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\FloatLiteral;
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
     * @return Evaluator
     * @throws EvaluatorException
     */
    public function evaluate(): Evaluator {
        while($this->tokens) {
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
            FloatLiteral::class => EvaluatorOfFloatLiteral::evaluate($this),
            Block::class => EvaluatorOfBlock::evaluate($this),
            default => $this,
        };

        $this->hashmap = $evaluator->hashmap;
        $this->stack = $evaluator->stack;
        $this->tokens = $evaluator->tokens;
    }

    /**
     * @throws EvaluatorException
     */
    private function _evaluateAlocasiaBlock(): void
    {
        $alocasia_block_token = array_shift($this->tokens);
        if ($alocasia_block_token instanceof AlocasiaBlock === false) {
            throw new EvaluatorException(
                source_code_line: $alocasia_block_token->line,
                source_code_position: $alocasia_block_token->position,
                message: "予期しないエラーが発生しました",
            );
        } else {
            $block_evaluator = new Evaluator(
                // 変数は全てGlobal Scope
                hashmap: $this->hashmap,
                // stackも共有
                stack: $this->stack,
                // tokenはBlockの持つtoken
                tokens: $alocasia_block_token->tokens,
            );
            $block_evaluator->evaluate();

            $this->hashmap = $block_evaluator->hashmap;
            $this->stack = $block_evaluator->stack;
            $this->tokens = $block_evaluator->tokens;
        }
    }
}