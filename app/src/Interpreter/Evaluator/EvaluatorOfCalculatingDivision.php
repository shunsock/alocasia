<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\IntegerLiteral;

class EvaluatorOfCalculatingDivision implements IEvaluator
{
    /**
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        // operator, operand1, operand2を取得
        $operator = $e->dequeueToken();
        $operand2 = $e->getOperandObject();
        $operand1 = $e->getOperandObject();

        // operand1とoperand2に0が含まれていたらError
        $operand1IsZero = $operand1->value === 0 || $operand1->value === 0.0;
        $operand2IsZero = $operand2->value === 0 || $operand2->value === 0.0;
        if ($operand1IsZero || $operand2IsZero) {
            throw new EvaluatorException(
                message: "ゼロ除算が発生しました.",
                source_code_line: $operator->line,
                source_code_position: $operator->position
            );
        }

        if ($operand1->type === AlocasiaObjectType::INTEGER && $operand2->type === AlocasiaObjectType::INTEGER) {
            // operand1, operand2が両方Integerならtype: FLOATのAlocasiaObjectをpush
            $e->pushItemToStack(
                new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: $operand1->value / $operand2->value,
                )
            );
        } else {
            // それ以外ならtype: FLOATのAlocasiaObjectをpush
            $e->pushItemToStack(
                new AlocasiaObject(
                    type: AlocasiaObjectType::FLOAT,
                    value: (float)$operand1->value / (float)$operand2->value,
                )
            );
        }
        return $e;
    }
}