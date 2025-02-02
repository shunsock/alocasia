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
//    public function testEvaluateAlocasiaBlock(): void
//    {
//        // Setup
//        $block = [
//            new AlocasiaBlock(
//                line: 1,
//                position: 1,
//                tokens: [
//                    new IntegerLiteral(
//                        line: 1,
//                        position: 1,
//                        value: 0,
//                    )
//                ]
//            )
//        ];
//        $evaluator = new Evaluator(hashmap: [], stack: $block, tokens: []);
//
//        // Run
//        EvaluatorOfAlocasiaBlock::evaluate($evaluator);
//
//        // Assert
//        // Expected AlocasiaObject(line: 1, position: 1, value: 0)
//        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
//        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
//        $this->assertEquals(0, $evaluator->stack[0]->value);
//    }

    /**
     * @throws EvaluatorException
     */
//    public function testEvaluateCalculatingInBlock(): void
//    {
//        // Setup
//        $hashmap = [
//            "alocasia" => new AlocasiaObject(
//                type: AlocasiaObjectType::INTEGER,
//                value: 10,
//            )
//        ];
//        $stack = [
//            // { alocasia 1 + }
//            new AlocasiaBlock(
//                line: 1,
//                position: 1,
//                tokens: [
//                    new Variable(
//                        line: 1,
//                        position: 1,
//                        name: "alocasia",
//                    ),
//                    new IntegerLiteral(
//                        line: 1,
//                        position: 1,
//                        value: 1,
//                    ),
//                    new Plus(
//                        line: 1,
//                        position: 1,
//                    )
//                ]
//            )
//        ];
//        $evaluator = new Evaluator(hashmap: $hashmap, stack: $stack, tokens: []);
//
//        // Run
//        EvaluatorOfAlocasiaBlock::evaluate($evaluator);
//
//        // Assert
//        // HashMapはそのまま AlocasiaObject(line: 1, position: 1, value: 10)
//        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->hashmap["alocasia"]);
//        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->hashmap["alocasia"]->type);
//        $this->assertEquals(10, $evaluator->hashmap["alocasia"]->value);
//
//        // Stackには計算結果が積まれる AlocasiaObject(line: 1, position: 1, value: 11)
//        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
//        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
//        $this->assertEquals(11, $evaluator->stack[0]->value);
//    }

    /**
     * @throws EvaluatorException
     */
    public function testEvaluateReassignInBlock(): void
    {
        // Setup
        $hashmap = [
            "alocasia" => new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 10,
            )
        ];
        $stack = [
            new Variable(
                line: 1,
                position: 1,
                name: "alocasia",
            ),
            new Equal(
                line: 1,
                position: 1
            ),
            // { alocasia = { alocasia 1 + } }
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new Variable(
                        line: 1,
                        position: 1,
                        name: "alocasia",
                    ),
                    new IntegerLiteral(
                        line: 1,
                        position: 1,
                        value: 1,
                    ),
                    new Plus(
                        line: 1,
                        position: 1,
                    )
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: $hashmap, stack: [], tokens: $token);

        // Run
        EvaluatorOfAlocasiaBlock::evaluate($evaluator);

        // Assert
        // HashMapが更新される AlocasiaObject(line: 1, position: 1, value: 11)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->hashmap["alocasia"]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->hashmap["alocasia"]->type);
        $this->assertEquals(10, $evaluator->hashmap["alocasia"]->value);

        // Stackには計算結果が積まれる AlocasiaObject(line: 1, position: 1, value: 11)
        $this->assertEmpty($evaluator->stack);
    }

    /**
     * @throws EvaluatorException
     */
//    public function testEvaluateNestedAlocasiaBlock(): void
//    {
//        // Setup
//        $block = [
//            new AlocasiaBlock(
//                line: 1,
//                position: 1,
//                tokens: [
//                    new Block(
//                        line: 2,
//                        position: 1,
//                        tokens: [
//                            new IntegerLiteral(
//                                line: 1,
//                                position: 2,
//                                value: 0,
//                            )
//                        ]
//                    )
//                ]
//            )
//        ];
//        $evaluator = new Evaluator(hashmap: [], stack: $block, tokens: []);
//
//        // Run
//        EvaluatorOfAlocasiaBlock::evaluate($evaluator);
//
//        // Assert
//        // Expected AlocasiaObject(line: 1, position: 1, value: 0)
//        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
//        $this->assertEquals(0, $evaluator->stack[0]->value);
//    }
}