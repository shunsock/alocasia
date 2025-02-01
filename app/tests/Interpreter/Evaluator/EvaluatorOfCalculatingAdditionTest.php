<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCalculatingAddition;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCreatingAlocasiaIntegerTypeObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Plus;
use PHPUnit\Framework\TestCase;

class EvaluatorOfCalculatingAdditionTest extends TestCase
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
        EvaluatorOfCalculatingAddition::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType::INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(1, $evaluator->stack[0]->value);
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
        EvaluatorOfCalculatingAddition::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType::FLOAT, value: 1.0)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(1.0, $evaluator->stack[0]->value);
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
        EvaluatorOfCalculatingAddition::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType::FLOAT, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(1.0, $evaluator->stack[0]->value);
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
        EvaluatorOfCalculatingAddition::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType::FLOAT, value: 1.0)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(1.0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testAdditionBlock(): void
    {
        // Setup
        // 1 + 0.0 = 1.0
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
        EvaluatorOfCalculatingAddition::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType::FLOAT, value: 1.0)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(1.0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testAdditionComplexBlock(): void
    {
        // Setup
        // 0 { 1 1.0 + } +
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
                    ),
                    new Plus(line: 1, position: 11)
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingAddition::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType:FLOAT, value: 2.0)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(2.0, $evaluator->stack[0]->value);
    }
}