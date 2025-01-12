<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Token\Token;

interface IEvaluator
{
    /**
     * @param Evaluator $e
     * @return Evaluator
     */
    public static function evaluate(Evaluator $e): Evaluator;
}