<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;

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
        $loopToken = $e->dequeueToken();
        // BlockTokenからAlocasiaBlockを作成してStackにpush
        EvaluatorOfCreatingAlocasiaBlock::evaluate($e);
        // Blockが持つToken配列を取得
        $stackedItem = end($e->stack);
        /** @var AlocasiaBlock $alocasiaBlock */
        $alocasiaBlock = $e->validateStackedItem(
            expectedStackedItemClass: AlocasiaBlock::class,
            actualStackedItem: $stackedItem
        );
        $tokens = $alocasiaBlock->tokens;

        while (true) {
            // Blockを評価
            EvaluatorOfAlocasiaBlock::evaluate($e);
            // 評価後にStackが何もない場合は無限ループになっている
            // ex1: loop {}
            // ex2: loop { x = 0 }
            if (empty($e->stack)) throw new EvaluatorException(
                message: "Endless Loop: LoopのIteration終了時にStackに何も積まれていません",
                source_code_line: $alocasiaBlock->line,
                source_code_position: $alocasiaBlock->position
            );
            // Stack topが0ならbreak
            if ($e->stack[0] instanceof AlocasiaObject) {
                if ($e->stack[0]->value === 0) return $e;
            }

            // 同じ処理をするためにtokensに$blockを積みなおす
            array_unshift($e->token_queue, $tokens);
        }
    }
}