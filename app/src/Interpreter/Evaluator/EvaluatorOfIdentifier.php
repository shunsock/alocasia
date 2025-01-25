<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Token\Variable;

class EvaluatorOfIdentifier implements IEvaluator
{
    /**
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        $token = array_shift($e->tokens);
        if ($token instanceof Variable === false) {
            throw new EvaluatorException(
                source_code_line: $token->line,
                source_code_position: $token->position,
                message: "予期しないエラーが発生しました",
            );
        } else {
            // if Variable->name in hash, then push AlocasiaObject->value to stack
            // else register it as variable
            return $e;
        }
    }
}