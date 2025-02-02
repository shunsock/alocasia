<?php

declare(strict_types=1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfAlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\Equal;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\Plus;
use Alocasia\Interpreter\Token\Variable;
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
    public function testEvaluateReAssignInBlock(): void
    {
        // Setup
        $hashmap = [
            "alocasia" => new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0,
            )
        ];
        $stack = [
            // {}
            new AlocasiaBlock(
                line: 1,
                position: 1,
                tokens: [
                    // { alocasia }
                    new Variable(
                        line: 1,
                        position: 1,
                        name: "alocasia",
                    ),
                    // { alocasia = }
                    new Equal(
                        line: 1,
                        position: 1,
                    ),
                    // { alocasia = { 1 alocasia + } }
                    new Block(
                        line: 1,
                        position: 1,
                        tokens: [
                            new IntegerLiteral(
                                line: 1,
                                position: 1,
                                value: 1,
                            ),
                            new Variable(
                                line: 1,
                                position: 1,
                                name: "alocasia",
                            ),
                            new Plus(
                                line: 1,
                                position: 1,
                            )
                        ]
                    )
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: $hashmap, stack: $stack, tokens: []);

        // Run
        EvaluatorOfAlocasiaBlock::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(line: 1, position: 1, value: 0)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->hashmap["alocasia"]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->hashmap["alocasia"]->type);
        $this->assertEquals(2, $evaluator->hashmap["alocasia"]->value);
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