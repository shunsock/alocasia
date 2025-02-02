<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObjectType;
use Alocasia\Interpreter\Token\BuiltinFunction;

class EvaluatorOfBuiltinFunction implements IEvaluator
{
    /**
     * @throws EvaluatorException
     */
    public static function evaluate(Evaluator $e): Evaluator
    {
        $token = $e->dequeueToken();
        /** @var BuiltinFunction $builtinFunctionToken */
        $builtinFunctionToken = $e->validateToken(
            expectedTokenClass: BuiltinFunction::class,
            actualToken: $token,
        );

        switch ($builtinFunctionToken->name) {
            case "print":
                self::evaluatePrint($e);
                break;
            case "print_ascii_str":
                self::evaluatePrintAsciiStr($e);
        }

        return $e;
    }

    /**
     * @throws EvaluatorException
     */
    private static function evaluatePrint(Evaluator $e): void {
        $stackedItem = $e->popItemFromStack();
        /** @var AlocasiaObject $alocasiaObject */
        $alocasiaObject = $e->validateStackedItem(
            expectedStackedItemClass: AlocasiaObject::class,
            actualStackedItem: $stackedItem,
        );
        echo $alocasiaObject->value . "\n";
    }

    /**
     * @throws EvaluatorException
     */
    private static function evaluatePrintAsciiStr(Evaluator $e): void {
        // 出力する文字列の数を取得する
        $stackedItem = $e->popItemFromStack();

        /** @var AlocasiaObject $alocasiaObject */
        $alocasiaObject = $e->validateStackedItem(
            expectedStackedItemClass: AlocasiaObject::class,
            actualStackedItem: $stackedItem,
        );

        $lengthIsFloat = $alocasiaObject->type === AlocasiaObjectType::FLOAT;
        $lengthIsZeroOrNegative = $alocasiaObject->value <= 0;
        if ($lengthIsFloat || $lengthIsZeroOrNegative) {
            throw new EvaluatorException(
                message: "printAsciiStr関数のLength引数は正の整数です. 入力: " . $alocasiaObject->value,
            );
        }

        for ($i = 0; $i < $alocasiaObject->value; $i++) {
            // 出力する数値
            $s = $e->popItemFromStack();
            /** @var AlocasiaObject $alocasiaNumberObject */
            $alocasiaNumberObject = $e->validateStackedItem(
                expectedStackedItemClass: AlocasiaObject::class,
                actualStackedItem: $s,
            );
            $lengthIsInteger = $alocasiaNumberObject->type === AlocasiaObjectType::INTEGER;
            if (!$lengthIsInteger) {
                throw new EvaluatorException(
                    message: "print_ascii_str関数のbodyはinteger型ですがとして {$alocasiaNumberObject->value} が渡されました",
                );
            }
            $numberIsNegative = $alocasiaObject->value < 0;
            $numberIsGreaterThan127 = $alocasiaObject->value > 127;
            if ($numberIsNegative || $numberIsGreaterThan127) {
                throw new EvaluatorException(
                    message: "print_ascii_str関数のbodyは0<x<2^7ですが{$alocasiaNumberObject->value} が渡されました",
                );
            }
            // 数値を文字にして出力
            echo chr($alocasiaNumberObject->value);
        }
        echo "\n";
    }
}
