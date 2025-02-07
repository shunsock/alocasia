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
        // Stackの要素数は 0 (LoopがBlockの評価終了時にStack topを参照するため)
        $this->assertCount(0, $evaluator->stack);
    }
}