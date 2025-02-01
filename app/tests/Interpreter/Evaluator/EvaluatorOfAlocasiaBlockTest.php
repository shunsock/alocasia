<?php

declare(strict_types=1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfAlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Block;
use PHPUnit\Framework\TestCase;

class EvaluatorOfAlocasiaBlockTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    public function testEvaluateAlocasiaBlock(): void
    {
        // Setup
        $block = [
            new AlocasiaBlock(
                line: 1,
                position: 1,
                tokens: [
                    new IntegerLiteral(
                        line: 1,
                        position: 1,
                        value: 0,
                    )
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $block, tokens: []);

        // Run
        EvaluatorOfAlocasiaBlock::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(line: 1, position: 1, value: 0)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    public function testEvaluateNestedAlocasiaBlock(): void
    {
        // Setup
        $block = [
            new AlocasiaBlock(
                line: 1,
                position: 1,
                tokens: [
                    new Block(
                        line: 2,
                        position: 1,
                        tokens: [
                            new IntegerLiteral(
                                line: 1,
                                position: 2,
                                value: 0,
                            )
                        ]
                    )
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $block, tokens: []);

        // Run
        EvaluatorOfAlocasiaBlock::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(line: 1, position: 1, value: 0)
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(0, $evaluator->stack[0]->value);
    }
}