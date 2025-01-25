<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfFloatLiteral;
use Alocasia\Interpreter\Token\FloatLiteral;
use PHPUnit\Framework\TestCase;

class EvaluatorOfFloatLiteralTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testPushObjectToStack(): void
    {
        // Setup
        $tokens = [
            new FloatLiteral(line: 1, position: 1, value: 1.0)
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfFloatLiteral::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1.0, $updated_evaluator->stack[0]->value);
    }
}