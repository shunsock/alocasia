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
        // if tokenを消費
        $e->dequeueToken(); // if tokenを消費
        EvaluatorOfCreatingAlocasiaBlock::evaluate($e);
        EvaluatorOfAlocasiaBlock::evaluate($e);
        $stackTop = $e->popItemFromStack();

        // stack: [... result]
        // tokens: true_branch false_branch
        /** @var AlocasiaObject $alocasiaObject */
        $alocasiaObject = $e->validateStackedItem(
            expectedStackedItemClass: AlocasiaObject::class,
            actualStackedItem: $stackTop,
        );

        $stackTopIsOne = $alocasiaObject->value === 1;
        if ($stackTopIsOne) {
            // true branchを評価
            EvaluatorOfCreatingAlocasiaBlock::evaluate($e);
            EvaluatorOfAlocasiaBlock::evaluate($e);
            // false branchをSkip
            $e->dequeueToken();
        } else {
            // true branchをSkip
            $e->dequeueToken();
            // false branchを評価
            EvaluatorOfCreatingAlocasiaBlock::evaluate($e);
            EvaluatorOfAlocasiaBlock::evaluate($e);
        }
        return $e;
    }
}