<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Token\Equal;
use Alocasia\Interpreter\Token\Variable;

class EvaluatorOfVariableOperation implements IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        $token = $e->dequeueToken();

        /** @var Variable $variableToken */
        $variableToken = $e->validateToken(
            expectedTokenClass: Variable::class,
            actualToken: $token,
        );

        $variableIsAlreadyExist = array_key_exists($variableToken->name, $e->hashmap);
        if (!$variableIsAlreadyExist) {
            self::assign($e, $variableToken);
            return $e;
        }

        if (empty($e->token_queue)) {
            self::read($e, $variableToken);
        } else if ($e->token_queue[0] instanceof Equal) {
            // 再代入
            // x = {1} x = {2}
            // type checkするならre-assignを定義してarray_popしたclassを型チェック
            self::assign($e, $variableToken);
        } else {
            self::read($e, $variableToken);
        }

        return $e;
    }

    private static function read(Evaluator $e, Variable $token): void
    {
        // 変数の取得
        // hashmapから変数のオブジェクトを取得
        // stackに変数名と関連付けられたAlocasiaObjectをpush
        $e->pushItemToStack($e->hashmap[$token->name]);
    }

    /**
     * @throws EvaluatorException
     */
    private static function assign(Evaluator $e, Variable $token): void
    {
        // Equalを読み飛ばす: "=" は 人間用のかざり
        $e->dequeueToken();
        // Blockを展開, StackにAlocasiaBlockをpush
        EvaluatorOfCreatingAlocasiaBlock::evaluate($e);
        // Blockを解釈: AlocasiaBlockでない場合はevaluateAlocasiaBlockがErrorを投げる
        EvaluatorOfAlocasiaBlock::evaluate($e);
        // Stackをpopして変数として使用する値を取得, hashmapに変数を登録
        $stackedItem = $e->popItemFromStack();

        /** @var AlocasiaObject $alocasiaObject */
        $alocasiaObject = $e->validateStackedItem(
            expectedStackedItemClass: AlocasiaObject::class,
            actualStackedItem: $stackedItem,
        );
        $e->hashmap[$token->name] = $alocasiaObject;
    }
}