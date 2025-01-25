<?php

declare(strict_types=1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Token\IntegerLiteral;
use PHPUnit\Framework\TestCase;

class EvaluatorTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    public function testEvaluateAlocasiaBlock(): void
    {
        // Setup
        $block = new AlocasiaBlock(
            line: 1,
            position: 1,
            tokens: [
                new IntegerLiteral(
                    line: 1,
                    position: 1,
                    value: 1,
                )
            ]
        );
        $evaluator = new Evaluator(hashmap: [], stack: [$block], tokens: []);

        // Run
        $evaluator->evaluateAlocasiaBlock();

        // Assert
        // Expected AlocasiaObject(line: 1, position: 2, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $evaluator->stack[0]->type);
        $this->assertEquals(1, $evaluator->stack[0]->value);
    }
}