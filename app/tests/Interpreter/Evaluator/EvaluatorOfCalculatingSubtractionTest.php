<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCalculatingSubtraction;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Minus;
use PHPUnit\Framework\TestCase;

class EvaluatorOfCalculatingSubtractionTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testSubtractionIntegerAndInteger(): void
    {
        // Setup
        $tokens = [
            new Minus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 1,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingSubtraction::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(-1, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testSubtractionIntegerAndFloat(): void
    {
        // Setup
        $tokens = [
            new Minus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 1,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 1.0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingSubtraction::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(0.0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testSubtractionFloatAndInteger(): void
    {
        // Setup
        $tokens = [
            new Minus(line: 1, position: 3)
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
        EvaluatorOfCalculatingSubtraction::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(1.0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testSubtractionFloatAndFloat(): void
    {
        // Setup
        $tokens = [
            new Minus(line: 1, position: 3)
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
        EvaluatorOfCalculatingSubtraction::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(1.0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testSubtractionBlock(): void
    {
        // Setup
        $tokens = [
            new Minus(line: 1, position: 3)
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
        EvaluatorOfCalculatingSubtraction::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(1.0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testSubtractionComplexBlock(): void
    {
        // Setup
        $tokens = [
            new Minus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0,
            ),
            // -1.0
            new AlocasiaBlock(
                line: 1,
                position: 5,
                tokens: [
                    new IntegerLiteral(
                        line: 1,
                        position: 7,
                        value: 0
                    ),
                    new FloatLiteral(
                        line: 1,
                        position: 9,
                        value: 1.0
                    ),
                    new Minus(
                        line: 1,
                        position: 11
                    )
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingSubtraction::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(1.0, $evaluator->stack[0]->value);
    }
}