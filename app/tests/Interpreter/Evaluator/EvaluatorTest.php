<?php

declare(strict_types=1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\Asterisk;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\ConditionalBranch;
use Alocasia\Interpreter\Token\Equal;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Plus;
use Alocasia\Interpreter\Token\Slash;
use Alocasia\Interpreter\Token\Variable;
use PHPUnit\Framework\TestCase;

class EvaluatorTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    public function testEvaluateArithmeticOperation(): void
    {
        // Setup
        // 1 2 + { 4 2 / } * 2 => 6.0
        $tokens = [
            new IntegerLiteral(
                line: 1,
                position: 1,
                value: 1,
            ),
            new FloatLiteral(
                line: 2,
                position: 2,
                value: 2.0,
            ),
            new Plus(line: 1, position: 3),
            // 1 + 2.0 = 3.0
            new Block(
                line: 1,
                position: 4,
                tokens: [
                    new IntegerLiteral(
                        line: 1,
                        position: 5,
                        value: 4,
                    ),
                    new IntegerLiteral(
                        line: 1,
                        position: 6,
                        value: 2,
                    ),
                    new Slash(
                        line: 1,
                        position: 7
                    )
                ]
            ),
            // 4 / 2 = 2
            new Asterisk(
                line: 1,
                position: 8,
            )
            // 3.0 * 2 = 6.0
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        $evaluator->evaluate();

        // Assert
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $evaluator->stack[0]->type);
        $this->assertEquals(6.0, $evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testReAssignVariableWithConditionalBranch(): void
    {
        // Setup
        $hashmap = [
            "alocasia" => new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 100
            )
        ];
        $tokens = [
            // if
            new ConditionalBranch(
                line: 1,
                position: 1,
            ),
            // if { 0 }
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new IntegerLiteral(line: 1, position: 2, value: 0),
                ]
            ),
            // if { 0 } {}
            new Block(
                line: 1,
                position: 1,
                tokens: []
            ),
            // if { 0 } {} { alocasia = { alocasia 1 + } 1 }
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    // { alocasia }
                    new Variable(line: 1, position: 1, name: "alocasia"),
                    // { alocasia = }
                    new Equal(line: 1, position: 10),
                    // { alocasia = { alocasia 1 + } }
                    new Block(
                        line: 1,
                        position: 12,
                        tokens: [
                            new Variable(line: 1, position: 20, name: "alocasia"),
                            new IntegerLiteral(line: 1, position: 20, value: 1),
                            new Plus(line: 1, position: 20)
                        ]
                    ),
                    // 次のトークンはAssignでは無視される
                    new IntegerLiteral(line: 1, position: 2, value: 1),
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: $hashmap, stack: [], tokens: $tokens);

        // Run
        $evaluator->evaluate();

        // Assert
        // AlocasiaObjectが更新される AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 101)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->hashmap["alocasia"]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->hashmap["alocasia"]->type);
        $this->assertEquals(101, $evaluator->hashmap["alocasia"]->value);
        // Stack topは AlocasiaObject(type: AlocasiaObjectType::INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(1, $evaluator->stack[0]->value);
    }
}