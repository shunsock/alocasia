<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\StackedItem;
use Alocasia\Interpreter\Token\Equal;
use Alocasia\Interpreter\Token\Token;
use Alocasia\Interpreter\Token\Variable;

class EvaluatorOfIdentifier implements IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        $token = array_shift($e->token_queue);
        if ($token instanceof Variable === false) {
            throw new EvaluatorException(
                source_code_line: $token->line,
                source_code_position: $token->position,
                message: "予期しないエラーが発生しました",
            );
        }

        $variableIsAlreadyExist = array_key_exists($token->name, $e->hashmap);
        if (!$variableIsAlreadyExist) {
            self::assign($e, $token);
            return $e;
        }

        if (empty($e->token_queue)) {
            self::read($e, $token);
        } else if ($e->token_queue[0] instanceof Equal) {
            // 再代入
            // x = {1} x = {2}
            // type checkするならre-assignを定義してarray_popしたclassを型チェック
            self::assign($e, $token);
        } else {
            self::read($e, $token);
        }

        return $e;
    }

    private static function read(Evaluator $e, Variable $token): void
    {
        // 変数の取得
        // hashmapから変数のオブジェクトを取得
        // stackに変数名と関連付けられたAlocasiaObjectをpush
        $e->stack[] = $e->hashmap[$token->name];
    }

    /**
     * @throws EvaluatorException
     */
    private static function assign(Evaluator $e, Variable $token): void
    {
        // 変数の登録
        // Equalを読み飛ばす: "=" は 人間用のかざり
        array_shift($e->token_queue);
        // Blockを展開, StackにAlocasiaBlockをpush
        EvaluatorOfBlock::evaluate($e);
        // Blockを解釈: AlocasiaBlockでない場合はevaluateAlocasiaBlockがErrorを投げる
        $e->evaluateAlocasiaBlock();
        // Stackをpopして変数として使用する値を取得, hashmapに変数を登録
        $alocasia_object = array_pop($e->stack);
        if ($alocasia_object instanceof AlocasiaObject) {
            $e->hashmap[$token->name] = $alocasia_object;
        } else if ($alocasia_object) {
            // Note: 予期しないエラー.
            // AlocasiaObjectが作れなかった場合に発生
            // StackのtopはevaluateAlocasiaBlockによってAlocasiaObjectがpushされる
            throw new EvaluatorException(
                source_code_line: $token->line,
                source_code_position: $token->position,
                message: "変数の登録に失敗しました. StackのtopがAlocasiaObjectではありません. 変数名: " . $token->name,
            );
        } else {
            // null
            throw new EvaluatorException(
                source_code_line: $token->line,
                source_code_position: $token->position,
                message: "Stack Underflowが発生し変数の登録に失敗しました. 変数名: " . $token->name,
            );
        }
    }
}