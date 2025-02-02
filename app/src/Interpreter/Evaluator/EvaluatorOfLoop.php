<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Token\Block;

class EvaluatorOfLoop implements IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        // stack: [...]
        // tokens: loop Block
        // loopを消費
        $e->dequeueToken();
        while (true) {
            // Blockが持つToken配列を取得
            $token = end($e->token_queue);

            /** @var Block $token */
            $block = $e->validateToken(
                expectedTokenClass: Block::class,
                actualToken: $token,
            );

            // BlockTokenからAlocasiaBlockを作成してStackにpush
            EvaluatorOfCreatingAlocasiaBlock::evaluate($e);

            // Blockを評価
            EvaluatorOfAlocasiaBlock::evaluate($e);

            if (empty($e->stack)) continue;

            // Stack topが0ならbreak
            if ($e->stack[0] instanceof AlocasiaObject) {
                if ($e->stack[0]->value == 0) return $e;
            }

            array_unshift($e->token_queue, $block);
        }
    }
}