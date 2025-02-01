<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCreatingAlocasiaBlock;
use Alocasia\Interpreter\Evaluator\EvaluatorOfConditionalBranch;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\ConditionalBranch;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use PHPUnit\Framework\TestCase;

class EvaluatorOfConditionalBranchTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testRunTrueBranch(): void
    {
        // Setup
        $tokens = [
            new ConditionalBranch(
                line: 1,
                position: 1
            ),
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new IntegerLiteral(1, 1, 1),
                ]
            ),
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new FloatLiteral(1, 2, 1.0),
                ]
            ),
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new FloatLiteral(1, 3, 0.0),
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfConditionalBranch::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType::FLOAT, value: 1.0)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1.0, $updated_evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testRunFalseBranch(): void
    {
        // Setup
        $tokens = [
            new ConditionalBranch(
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
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new FloatLiteral(1, 2, 1.0),
                ]
            ),
            new Block(
                line: 1,
                position: 1,
                tokens: [
                    new FloatLiteral(1, 3, 0.0),
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfConditionalBranch::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType::FLOAT, value: 0.0)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::FLOAT, $updated_evaluator->stack[0]->type);
        $this->assertEquals(0.0, $updated_evaluator->stack[0]->value);
    }
}