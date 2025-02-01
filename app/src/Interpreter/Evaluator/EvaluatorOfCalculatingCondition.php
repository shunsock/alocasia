<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;

class EvaluatorOfCalculatingCondition implements IEvaluator
{
    /**
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        // operator, operand1, operand2を取得
        $e->dequeueToken();
        $operand2 = $e->getOperandObject();
        $operand1 = $e->getOperandObject();

        if ($operand1->value === $operand2->value) {
            $e->pushItemToStack(
                new AlocasiaObject(
                    type: AlocasiaObjectType::INTEGER,
                    value: 1
                )
            );
        } else {
            $e->pushItemToStack(
                new AlocasiaObject(
                    type: AlocasiaObjectType::INTEGER,
                    value: 0
                )
            );
        }

        return $e;
    }
}