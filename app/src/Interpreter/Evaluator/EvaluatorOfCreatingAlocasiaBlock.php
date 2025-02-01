<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Token\Block;

class EvaluatorOfCreatingAlocasiaBlock implements IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        $token =$e->dequeueToken();

        /** @var Block $blockToken */
        $blockToken = $e->validateToken(
            expectedTokenClass: Block::class,
            actualToken: $token,
        );

        $e->pushItemToStack(self::createAlocasiaBlock($blockToken));
        return $e;
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