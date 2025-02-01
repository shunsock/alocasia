<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCreatingAlocasiaIntegerTypeObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use PHPUnit\Framework\TestCase;

class EvaluatorOfCreatingAlocasiaIntegerTypeObjectTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testPushObjectToStack(): void
    {
        // Setup
        $tokens = [
            new IntegerLiteral(line: 1, position: 1, value: 0),
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfCreatingAlocasiaIntegerTypeObject::evaluate($evaluator);

        // Assert
        // Expected: AlocasiaObject(type: AlocasiaObjectType::INTEGER, value: 0.0)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $updated_evaluator->stack[0]->type);
        $this->assertEquals(0, $updated_evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testPushObjectToStackAsLastItem(): void
    {
        // Setup
        $stack = [

            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0
            )
        ];
        $tokens = [
            new IntegerLiteral(line: 1, position: 1, value: 1),
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfCreatingAlocasiaIntegerTypeObject::evaluate($evaluator);

        // Assert
        // Expected: [
        //    AlocasiaObject(type: AlocasiaObjectType::INTEGER, value: 0),
        //    AlocasiaObject(type: AlocasiaObjectType::INTEGER, value: 1),
        // ]
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[1]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $updated_evaluator->stack[1]->type);
        $this->assertEquals(1, $updated_evaluator->stack[1]->value);
    }
}