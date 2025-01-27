<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Token\Block;

class EvaluatorOfLoop implements IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        // stack: [...]
        // tokens: loop Block
        $loop_token = array_shift($e->tokens); // loop tokenを消費
        $block = array_shift($e->tokens);
        if ($block instanceof Block === false) {
            throw new EvaluatorException(
                source_code_line: $loop_token->line,
                source_code_position: $loop_token->position,
                message: "loop keywordのあとにBlockがありません"
            );
        }
        while (true) {
            EvaluatorOfBlock::evaluate($e); // blockのtokensをStackに積む
            $e->evaluateAlocasiaBlock(); // blockのtokensを評価
            // 配列アクセスをおこなうのでerror回避
            if (empty($e->stack)) continue;
            // Stack topが0ならbreak
            if ($e->stack[0] instanceof AlocasiaObject) {
                if ($e->stack[0]->value === 0) return $e;
            }
            // 同じ処理をするためにtokensに$blockを積みなおす
            array_unshift($e->tokens, $block);
        }
    }
}