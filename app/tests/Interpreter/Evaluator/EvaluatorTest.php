<?php

declare(strict_types=1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\Asterisk;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Plus;
use Alocasia\Interpreter\Token\Slash;
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
}