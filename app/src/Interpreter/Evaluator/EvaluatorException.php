<?php

namespace Alocasia\Interpreter\Evaluator;

use Exception;
use Throwable;

class EvaluatorException extends Exception
{
    public readonly ?int $source_code_line;
    public readonly ?int $source_code_position;
    public function __construct(
        string $message,
        ?int $source_code_line = null,
        ?int $source_code_position = null,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->source_code_line = $source_code_line;
        $this->source_code_position = $source_code_position;
    }
}
