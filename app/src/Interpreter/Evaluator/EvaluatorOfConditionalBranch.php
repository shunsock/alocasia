<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;

class EvaluatorOfConditionalBranch implements IEvaluator
{
    /**
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        // stack: [...]
        // tokens: if condition_block true_branch false_branch
        $if_token = array_shift($e->tokens); // if tokenを消費
        EvaluatorOfBlock::evaluate($e); // conditional_blockのtokensをStackに積む
        $e->evaluateAlocasiaBlock(); // conditional_blockのtokensを評価
        $stackTop = array_shift($e->stack); // stack top (conditional_blockの評価結果を取得

        // stack: [... result]
        // tokens: true_branch false_branch
        if ($stackTop instanceof AlocasiaObject === false) {
            throw new EvaluatorException(
                source_code_line: $if_token->line,
                source_code_position: $if_token->position,
                message: "分岐条件の計算結果の取得に失敗しました. 期待されるStackedItem: AlocasiaObject. 取得したStackedItem: " . get_class($stackTop)
            );
        } else {
            $stackTopIsOne = $stackTop->value === 1;
            if ($stackTopIsOne) {
                EvaluatorOfBlock::evaluate($e); // true_branchをStackに積む
                $e->evaluateAlocasiaBlock(); // true_branchを評価
                array_shift($e->tokens); // false_branchをskip
            } else {
                array_shift($e->tokens); // true_branchをskip
                EvaluatorOfBlock::evaluate($e); // false_branchをStackに積む
                $e->evaluateAlocasiaBlock(); // false_branchを評価
            }
        }
        return $e;
    }
}