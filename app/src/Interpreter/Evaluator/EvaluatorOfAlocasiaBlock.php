<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;

class EvaluatorOfAlocasiaBlock implements IEvaluator
{
    /**
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        while (true) {
            $stackedItem = $e->popItemFromStack();

            /** @var AlocasiaBlock $alocasiaBlock */
            $alocasiaBlock = $e->validateStackedItem(
                expectedStackedItemClass: AlocasiaBlock::class,
                actualStackedItem: $stackedItem
            );

            $blockEvaluator = new Evaluator(
            // 変数は全てGlobal Scope
                hashmap: $e->hashmap,
                // stackも共有
                stack: $e->stack,
                // tokenはBlockの持つtoken
                tokens: $alocasiaBlock->tokens,
            );
            $blockEvaluator->evaluate();

            // 共有して更新されたhashmapとstackを元のevaluatorに渡す
            // Note: $e->token_queue の Tokenは使い切っている
            $e->hashmap = $blockEvaluator->hashmap;
            $e->stack = $blockEvaluator->stack;

            // Stackが空の場合は評価終了
            if (empty($e->stack)) {
                return $e;
            }

            // StackにAlocasiaBlock以外のStackedItemが積まれたら評価終了
            if (!$e->stack[0] instanceof AlocasiaBlock) {
                return $e;
            }
        }
    }
}
