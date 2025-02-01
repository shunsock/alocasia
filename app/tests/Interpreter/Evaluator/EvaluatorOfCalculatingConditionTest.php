<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCalculatingCondition;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Plus;
use PHPUnit\Framework\TestCase;

class EvaluatorOfCalculatingConditionTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testConditionFalse(): void
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
        EvaluatorOfCalculatingCondition::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 0)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testConditionTrue(): void
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
                value: 1.0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingCondition::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(1, $evaluator->stack[0]->value);
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
                value: 1,
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
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingCondition::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(1, $evaluator->stack[0]->value);
    }
}