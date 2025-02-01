<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfAddition;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCreatingAlocasiaIntegerTypeObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Plus;
use PHPUnit\Framework\TestCase;

class EvaluatorOfAdditionTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testAdditionIntegerAndInteger(): void
    {
        // Setup
        $tokens = [
            new Plus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 1,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfAddition::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1, $updated_evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testAdditionIntegerAndFloat(): void
    {
        // Setup
        $tokens = [
            new Plus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 1,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 0.0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfAddition::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1.0, $updated_evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testAdditionFloatAndInteger(): void
    {
        // Setup
        $tokens = [
            new Plus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 1.0,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfAddition::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1.0, $updated_evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testAdditionFloatAndFloat(): void
    {
        // Setup
        $tokens = [
            new Plus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 1.0,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 0.0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfAddition::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1.0, $updated_evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testAdditionBlock(): void
    {
        // Setup
        $tokens = [
            new Plus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaBlock(
                line: 1,
                position: 5,
                tokens: [
                    new IntegerLiteral(
                        line: 1,
                        position: 7,
                        value: 1
                    )
                ]
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 0.0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfAddition::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1.0, $updated_evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testAdditionComplexBlock(): void
    {
        // Setup
        $tokens = [
            new Plus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0,
            ),
            new AlocasiaBlock(
                line: 1,
                position: 5,
                tokens: [
                    new IntegerLiteral(
                        line: 1,
                        position: 7,
                        value: 1
                    ),
                    new FloatLiteral(
                        line: 1,
                        position: 9,
                        value: 1.0
                    )
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfAddition::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1.0, $updated_evaluator->stack[0]->value);
    }
}