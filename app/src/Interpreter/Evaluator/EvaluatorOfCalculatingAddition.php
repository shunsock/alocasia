<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;

class EvaluatorOfCalculatingAddition implements IEvaluator
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

        if ($operand1->type === AlocasiaObjectType::INTEGER && $operand2->type === AlocasiaObjectType::INTEGER) {
            // operand1, operand2が両方Integerならtype: FLOATのAlocasiaObjectをpush
            $e->pushItemToStack(
                new AlocasiaObject(
                    type: AlocasiaObjectType::INTEGER,
                    value: $operand1->value + $operand2->value,
                )
            );
        } else {
            // それ以外ならtype: FLOATのAlocasiaObjectをpush
            $e->pushItemToStack(
                new AlocasiaObject(
                    type: AlocasiaObjectType::FLOAT,
                    value: (float)$operand1->value + (float)$operand2->value,
                )
            );
        }
        return $e;
    }
}