<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;

class EvaluatorOfCondition implements IEvaluator
{
    /**
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        // operator, operand1, operand2を取得
        array_pop($e->tokens); // operatorは無視
        $operand2 = self::validateOperand($e);
        $operand1 = self::validateOperand($e);

        if ($operand1->value === $operand2->value) {
            $e->stack[] = new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 1
            );
        } else {
            $e->stack[] = new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0
            );
        }

        return $e;
    }

    /**
     * @param Evaluator $e
     * @return AlocasiaObject
     * @throws EvaluatorException
     */
    private static function validateOperand(Evaluator $e): AlocasiaObject
    {
        $stacked_item = array_pop($e->stack);

        // StackedItemがObjectなら正常終了
        if ($stacked_item instanceof AlocasiaObject) return $stacked_item;

        if ($stacked_item === null) {
            throw new EvaluatorException(
                source_code_line: 0,
                source_code_position: 0,
                message: "Stack Underflowが発生し比較に失敗しました.",
            );
        }

        // Blockの評価をして、StackのtopのObjectを戻す
        // Blockの評価
        $e->evaluateAlocasiaBlock();

        // Stackのtopを取得
        $stacked_item_after_evaluate_block = array_pop($e->stack);

        // 取得した値が正常なら返す
        if ($stacked_item_after_evaluate_block instanceof AlocasiaObject) return $stacked_item_after_evaluate_block;

        // Note: 予期しないエラー.
        // AlocasiaObjectが作れなかった場合に発生
        // StackのtopはevaluateAlocasiaBlockによってAlocasiaObjectがpushされる
        // TODO: $stacked_item->lineをnullableに変更
        throw new EvaluatorException(
            source_code_line: 0,
            source_code_position: 0,
            message: "比較に失敗しました. StackのtopがAlocasiaObjectではありません.",
        );
    }
}