<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\IntegerLiteral;
use PHPUnit\Framework\TestCase;

class EvaluatorOfBlockTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testPushObjectToStack(): void
    {
        // Setup
        $tokens = [
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new IntegerLiteral(1, 2, 1),
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfBlock::evaluate($evaluator);

        // Assert
        // Expected AlocasiaBlock(tokens: [IntegerLiteral(line: 1, position: 2, value: 1)
        $this->assertInstanceOf(AlocasiaBlock::class, $updated_evaluator->stack[0]);
        $this->assertInstanceOf(IntegerLiteral::class, $updated_evaluator->stack[0]->tokens[0]);
        $this->assertEquals(1, $updated_evaluator->stack[0]->tokens[0]->line);
        $this->assertEquals(2, $updated_evaluator->stack[0]->tokens[0]->position);
        $this->assertEquals(1, $updated_evaluator->stack[0]->tokens[0]->value);
    }
}