<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Token\Block;

class EvaluatorOfBlock implements IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        $token = array_shift($e->tokens);
        if ($token instanceof Block === false) {
            throw new EvaluatorException(
                source_code_line: $token->line,
                source_code_position: $token->position,
                message: "予期しないエラーが発生しました",
            );
        } else {
            $e->stack[] = self::createAlocasiaBlock($token);
            return $e;
        }
    }

    private static function createAlocasiaBlock(Block $token): AlocasiaBlock
    {
        return new AlocasiaBlock(
            line: $token->line,
            position: $token->position,
            tokens: $token->tokens,
        );
    }
}