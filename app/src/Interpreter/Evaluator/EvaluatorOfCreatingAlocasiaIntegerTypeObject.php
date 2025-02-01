<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\IntegerLiteral;

class EvaluatorOfCreatingAlocasiaIntegerTypeObject implements IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        $token = $e->dequeueToken();

        /** @var IntegerLiteral $integerLiteralToken */
        $integerLiteralToken = $e->validateToken(
            expectedTokenClass: IntegerLiteral::class,
            actualToken: $token
        );

        $e->pushItemToStack(self::createAlocasiaObject($integerLiteralToken));
        return $e;
    }

    private static function createAlocasiaObject(IntegerLiteral $token): AlocasiaObject {
        return new AlocasiaObject(
            type: alocasiaObjectType::INTEGER,
            value: $token->value,
        );
    }
}