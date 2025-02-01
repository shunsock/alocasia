<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCalculatingDivision;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Slash;
use PHPUnit\Framework\TestCase;

class EvaluatorOfCalculatingDivisionTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testDivisionIntegerAndInteger(): void
    {
        // Setup
        $tokens = [
            new Slash(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: -1,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 1,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingDivision::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType::INTEGER, value: -1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(-1, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testDivisionIntegerAndFloat(): void
    {
        // Setup
        $tokens = [
            new Slash(line: 1, position: 3)
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
        EvaluatorOfCalculatingDivision::evaluate($evaluator);

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
    function testDivisionFloatAndInteger(): void
    {
        // Setup
        $tokens = [
            new Slash(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 1.0,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 2,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingDivision::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType::FLOAT, value: 0.5)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(0.5, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testDivisionFloatAndFloat(): void
    {
        // Setup
        $tokens = [
            new Slash(line: 1, position: 3)
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
        EvaluatorOfCalculatingDivision::evaluate($evaluator);

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
    function testDivisionBlock(): void
    {
        // Setup
        $tokens = [
            new Slash(line: 1, position: 3)
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
                value: 1.0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingDivision::evaluate($evaluator);

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
    function testDivisionComplexBlock(): void
    {
        // Setup
        $tokens = [
            new Slash(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 2,
            ),
            // 1.0
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
                    new Slash(
                        line: 1,
                        position: 11
                    )
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfCalculatingDivision::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType::INTEGER, value: 2)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(2, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testZeroDivisionErrorWithIntegerZero(): void
    {
        // Expect
        $this->expectException(EvaluatorException::class);
        $this->expectExceptionMessage("ゼロ除算が発生しました.");

        // Setup
        $tokens = [
            new Slash(line: 1, position: 3)
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
        EvaluatorOfCalculatingDivision::evaluate($evaluator);
    }

    /**
     * @throws EvaluatorException
     */
    function testZeroDivisionErrorWithFloatZero(): void
    {
        // Expect
        $this->expectException(EvaluatorException::class);
        $this->expectExceptionMessage("ゼロ除算が発生しました.");

        // Setup
        $tokens = [
            new Slash(line: 1, position: 3)
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
        EvaluatorOfCalculatingDivision::evaluate($evaluator);
    }
}