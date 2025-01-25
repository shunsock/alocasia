<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\IntegerLiteral;

class EvaluatorOfIntegerLiteral implements IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        $token = array_shift($e->tokens);
        if ($token instanceof IntegerLiteral === false) {
            throw new EvaluatorException(
                source_code_line: $token->line,
                source_code_position: $token->position,
                message: "予期しないエラーが発生しました",
            );
        } else {
            $e->stack[] = self::createAlocasiaObject($token);
            return $e;
        }
    }

    private static function createAlocasiaObject(IntegerLiteral $token): AlocasiaObject {
        return new AlocasiaObject(
            type: alocasiaObjectType::INTEGER,
            value: $token->value,
        );
    }
}