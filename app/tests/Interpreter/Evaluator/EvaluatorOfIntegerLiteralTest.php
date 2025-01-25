<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfIntegerLiteral;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\IntegerLiteral;
use PHPUnit\Framework\TestCase;

class EvaluatorOfIntegerLiteralTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testPushObjectToStack(): void
    {
        // Setup
        $tokens = [
            new IntegerLiteral(line: 1, position: 1, value: 1)
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfIntegerLiteral::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1, $updated_evaluator->stack[0]->value);
    }
}