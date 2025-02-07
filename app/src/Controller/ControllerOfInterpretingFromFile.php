<?php

declare(strict_types=1);

namespace Alocasia\Controller;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Parser\Parser;
use Alocasia\Interpreter\Parser\ParserException;
use Alocasia\Interpreter\Scanner\Scanner;
use Alocasia\Interpreter\Scanner\ScannerException;

readonly class ControllerOfInterpretingFromFile implements IController
{
    public string $src;
    public function __construct(string $file_path) {
        $file_contents = file_get_contents(
            filename: $file_path
        );
        if (false === $file_contents) {
            echo "入力されたファイルのパスが存在しません: $file_path";
            exit(1);
        }
        $this->src = $file_contents;
    }

    public function run(): void {
        $scanner = new Scanner(source: $this->src);
        try {
            $tokens = $scanner->scan();
        } catch (ScannerException $e) {
            printf(
                "Scan Error: line: %d, position: %d: %s\n",
                $e->source_code_line,
                $e->source_code_position,
                $e->getMessage(),
            );
            exit(1);
        }

        $parser = new Parser(tokens: $tokens);
        try {
            $tokensParsed = $parser->parse();
        } catch (ParserException $e) {
            printf(
                "Parse Error: line: %d, position: %d: %s\n",
                $e->source_code_line,
                $e->source_code_position,
                $e->getMessage(),
            );
            exit(1);
        }

        $evaluator = new Evaluator(
            hashmap: [],
            stack: [],
            tokens: $tokensParsed
        );
        try {
            $evaluator->evaluate();
        } catch (EvaluatorException $e) {
            $line_message = $e->source_code_line !== null ? "line: " . $e->source_code_line . ", " : "";
            $position_message = $e->source_code_position !== null ? "position: " . $e->source_code_position . " " : "";
            printf(
                "Runtime Error: %s%s: エラーが発生しました %s\n",
                $line_message,
                $position_message,
                $e->getMessage(),
            );
            exit(0);
        }
    }
}