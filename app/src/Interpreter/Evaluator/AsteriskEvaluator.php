<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use RuntimeException;

class AsteriskEvaluator implements IEvaluator
{
    public static function evaluate(Evaluator $e): Evaluator {
        array_pop($e->tokens); // *を消費
        $op1 = array_pop($e->stack);
        $op2 = array_pop($e->stack);
        match (get_class($op1)) {
            IntegerLiteral::class => match (get_class($op2)) {
                IntegerLiteral::class => array_push($e->stack, $op1->value * $op2->value),
                FloatLiteral::class => array_push($e->stack, $op1->value * $op2->value),
                default => throw new RuntimeException(get_class($op2) . 'に掛け算は実装されていません'),
            },
            FloatLiteral::class => match (get_class($op2)) {
                FloatLiteral::class => array_push($e->stack, $op2->value),
                IntegerLiteral::class => array_push($e->stack, $op2->value),
                default => throw new RuntimeException(get_class($op2) . 'に掛け算は実装されていません'),
            },
            default => throw new RuntimeException(get_class($op1) . 'に掛け算は実装されていません'),
        };

        return $e;
    }
}