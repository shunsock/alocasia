<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\FloatLiteral;

class EvaluatorOfFloatLiteral implements IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        $token = $e->dequeueToken();

        /** @var FloatLiteral $floatLiteralToken */
        $floatLiteralToken = $e->validateToken(
            expectedTokenClass: FloatLiteral::class,
            actualToken: $token
        );

        $e->pushItemToStack(self::createAlocasiaObject($floatLiteralToken));
        return $e;
    }

    private static function createAlocasiaObject(FloatLiteral $token): AlocasiaObject {
        return new AlocasiaObject(
            type: alocasiaObjectType::FLOAT,
            value: $token->value,
        );
    }
}