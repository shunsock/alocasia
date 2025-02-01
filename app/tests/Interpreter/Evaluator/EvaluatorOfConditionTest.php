<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCalculatingAddition;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCondition;
use Alocasia\Interpreter\Evaluator\EvaluatorOfCreatingAlocasiaIntegerTypeObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Plus;
use PHPUnit\Framework\TestCase;

class EvaluatorOfConditionTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testConditionFalse(): void
    {
        // Setup
        $tokens = [
            new Plus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 1,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfCondition::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 0)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $updated_evaluator->stack[0]->type);
        $this->assertEquals(0, $updated_evaluator->stack[0]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testConditionTrue(): void
    {
        // Setup
        $tokens = [
            new Plus(line: 1, position: 3)
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 1.0,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 1.0,
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfCondition::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $updated_evaluator->stack[0]->type);
        $this->assertEquals(1, $updated_evaluator->stack[0]->value);
    }

    // TODO: Blockの実装を直したら実行
    /**
     * @throws EvaluatorException
     */
//    function testAdditionComplexBlock(): void
//    {
//        // Setup
//        $tokens = [
//            new Plus(line: 1, position: 3)
//        ];
//        $stack = [
//            new AlocasiaObject(
//                type: AlocasiaObjectType::INTEGER,
//                value: 1,
//            ),
//            new AlocasiaBlock(
//                line: 1,
//                position: 5,
//                tokens: [
//                    new IntegerLiteral(
//                        line: 1,
//                        position: 7,
//                        value: 1
//                    ),
//                ]
//            )
//        ];
//        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);
//
//        // Run
//        $updated_evaluator = EvaluatorOfCondition::evaluate($evaluator);
//
//        // Assert
//        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
//        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
//        $this->assertEquals(AlocasiaObjectType::INTEGER, $updated_evaluator->stack[0]->type);
//        $this->assertEquals(1, $updated_evaluator->stack[0]->value);
//    }
}