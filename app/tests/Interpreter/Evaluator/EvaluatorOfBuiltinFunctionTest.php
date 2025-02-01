<?php

declare(strict_types = 1);

namespace Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Evaluator\EvaluatorOfBuiltinFunction;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\BuiltinFunction;
use PHPUnit\Framework\TestCase;

class EvaluatorOfBuiltinFunctionTest extends TestCase
{
    /**
     * @throws EvaluatorException
     */
    function testPrintFunction(): void
    {
        // Setup
        $tokens = [
            new BuiltinFunction(line: 1, position: 3, name: "print")
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::FLOAT,
                value: 3.14,
            ),
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfBuiltinFunction::evaluate($evaluator);

        // Assert
        $this->assertEmpty($evaluator->stack);
    }

    /**
     * @throws EvaluatorException
     */
    function testPrintAsciiStrFunction(): void
    {
        // Setup
        $tokens = [
            new BuiltinFunction(line: 1, position: 3, name: "print_ascii_str")
        ];
        $stack = [
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 100,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 108,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 114,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 111,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 87,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 32,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 111,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 108,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 108,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 101,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 72,
            ),
            new AlocasiaObject(
                type: AlocasiaObjectType::INTEGER,
                value: 11,
            ),
        ];
        $evaluator = new Evaluator(hashmap: [], stack: $stack, tokens: $tokens);

        // Run
        EvaluatorOfBuiltinFunction::evaluate($evaluator);

        // Assert
        $this->assertEmpty($evaluator->stack);
    }
}