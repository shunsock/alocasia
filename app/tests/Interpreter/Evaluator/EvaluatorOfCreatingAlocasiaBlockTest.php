<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCreatingAlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\IntegerLiteral;
use PHPUnit\Framework\TestCase;

class EvaluatorOfCreatingAlocasiaBlockTest extends TestCase
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
        $updated_evaluator = EvaluatorOfCreatingAlocasiaBlock::evaluate($evaluator);

        // Assert
        // Expected AlocasiaBlock(tokens: [IntegerLiteral(line: 1, position: 2, value: 1)])
        $this->assertInstanceOf(AlocasiaBlock::class, $updated_evaluator->stack[0]);
        $this->assertInstanceOf(IntegerLiteral::class, $updated_evaluator->stack[0]->tokens[0]);
        $this->assertEquals(1, $updated_evaluator->stack[0]->tokens[0]->line);
        $this->assertEquals(2, $updated_evaluator->stack[0]->tokens[0]->position);
        $this->assertEquals(1, $updated_evaluator->stack[0]->tokens[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testPushObjectToStackAsLastObject(): void
    {
        // Setup
        $stack = [
            new AlocasiaBlock(
                line: 1,
                position: 1,
                tokens: [
                    new IntegerLiteral(1, 1, 0),
                ]
            )
        ];
        $tokens = [
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new IntegerLiteral(1, 2, 1),
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfCreatingAlocasiaBlock::evaluate($evaluator);

        // Assert
        // Expected [
        //    AlocasiaBlock(tokens: [IntegerLiteral(line: 1, position: 1, value: 0)]),
        //    AlocasiaBlock(tokens: [IntegerLiteral(line: 1, position: 2, value: 1)]),  -- stacked
        // ]
        $this->assertInstanceOf(AlocasiaBlock::class, $updated_evaluator->stack[1]);
        $this->assertInstanceOf(IntegerLiteral::class, $updated_evaluator->stack[1]->tokens[0]);
        $this->assertEquals(1, $updated_evaluator->stack[1]->tokens[0]->line);
        $this->assertEquals(2, $updated_evaluator->stack[1]->tokens[0]->position);
        $this->assertEquals(1, $updated_evaluator->stack[1]->tokens[0]->value);
    }
}