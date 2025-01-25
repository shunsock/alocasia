<?php

declare(strict_types=1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfIdentifier;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\Equal;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Variable;
use PHPUnit\Framework\TestCase;

class EvaluatorOfIdentifierTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testRegisterVariable(): void
    {
        // Setup
        $tokens = [
            new Variable(line: 1, position: 1, name: "alocasia"),
            new Equal(line: 1, position: 10),
            new Block(
                line: 1,
                position: 12,
                tokens: [
                    new IntegerLiteral(line: 1, position: 20, value: 0)
                ]
            )
        ];
        $evaluator = new Evaluator(hashmap: [], stack: [], tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfIdentifier::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 0) is registered as "alocasia"
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->hashmap["alocasia"]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $updated_evaluator->hashmap["alocasia"]->type);
        $this->assertEquals(0, $updated_evaluator->hashmap["alocasia"]->value);
    }

    /**
     * @throws EvaluatorException
     */
    function testReadVariable(): void
    {
        // Setup
        $tokens = [
            new Variable(line: 1, position: 1, name: "alocasia"),
        ];
        $hashmap = [
            "alocasia" => new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 0,
            )
        ];
        $evaluator = new Evaluator(hashmap: $hashmap, stack: [], tokens: $tokens);

        // Run
        $updated_evaluator = EvaluatorOfIdentifier::evaluate($evaluator);

        // Assert
        // Expected AlocasiaObject(type: AlocasiaObjectType:INTEGER, value: 1)
        $this->assertInstanceOf(AlocasiaObject::class, $updated_evaluator->stack[0]);
        $this->assertEquals(AlocasiaObjectType::INTEGER, $updated_evaluator->stack[0]->type);
        $this->assertEquals(0, $updated_evaluator->stack[0]->value);
    }
}