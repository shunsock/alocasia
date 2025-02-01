<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;

class EvaluatorOfMultiplication implements IEvaluator
{
    /**
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        // operator, operand1, operand2を取得
        array_shift($e->token_queue); // operatorは無視
        $operand1 = self::validateOperand($e);
        $operand2 = self::validateOperand($e);

        // operand1, operand2が両方Integerならtype: FLOATのAlocasiaObjectをpush
        if ($operand1->type === AlocasiaObjectType::INTEGER && $operand2->type === AlocasiaObjectType::INTEGER) {
            $e->stack[] = new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: $operand1->value * $operand2->value,
            );
        } else {
            // それ以外ならtype: FLOATのAlocasiaObjectをpush
            $e->stack[] = new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: (float)$operand1->value * (float)$operand2->value,
            );
        }
        return $e;
    }

    /**
     * @throws EvaluatorException
     */
    private static function validateOperand(Evaluator $e): AlocasiaObject
    {
        $stacked_item = $e->stack[0];
        if ($stacked_item instanceof AlocasiaObject) {
            // stacked_itemを消費
            array_shift($e->stack);
            return $stacked_item;
        } else {
            // Blockの評価をして、Stackのtopを使う
            // Blockの評価
            $e->evaluateAlocasiaBlock();
            // Stackのtopを取得
            $stacked_item_after_evaluate_block = array_shift($e->stack);
            // 取得した値が正常なら返す
            if ($stacked_item_after_evaluate_block instanceof AlocasiaObject) {
                return $stacked_item_after_evaluate_block;
            } else if ($stacked_item_after_evaluate_block instanceof AlocasiaBlock) {
                // Note: 予期しないエラー.
                // AlocasiaObjectが作れなかった場合に発生
                // StackのtopはevaluateAlocasiaBlockによってAlocasiaObjectがpushされる
                // TODO: $stacked_item->lineをnullableに変更
                throw new EvaluatorException(
                    source_code_line: 0,
                    source_code_position: 0,
                    message: "足し算に失敗しました. StackのtopがAlocasiaObjectではありません.",
                );
            } else {
                // null
                throw new EvaluatorException(
                    source_code_line: 0,
                    source_code_position: 0,
                    message: "Stack Underflowが発生し足し算に失敗しました.",
                );
            }
        }
    }
}