<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfLoop;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Loop;
use PHPUnit\Framework\TestCase;

class EvaluatorOfLoopTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testRunStopWithZero(): void
    {
        // Setup
        $tokens = [
            new Loop(
                line: 1,
                position: 1
            ),
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new IntegerLiteral(1, 1, 0),
                ]
            ),
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        $evaluator = EvaluatorOfLoop::evaluate($evaluator);

        // Assert
        // Stackの要素数は 1
        $this->assertCount(1, $evaluator->stack);
        // Stackの要素は AlocasiaObject(type: AlocasiaObjectType::FLOAT, value: 1.0)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testAssertEndlessLoopBranch(): void
    {
        // Expect
        $this->expectException(EvaluatorException::class);
        $this->expectExceptionMessage("Endless Loop: LoopのIteration終了時にStackに何も積まれていません");

        // Setup
        $tokens = [
            new Loop(
                line: 1,
                position: 1
            ),
            new Block(
                line: 1,
                position: 1,
                tokens: []
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        EvaluatorOfLoop::evaluate($evaluator);
    }
}