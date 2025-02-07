<?php

declare(strict_types=1);

namespace Alocasia\Controller;

use Alocasia\Interpreter\Evaluator\Evaluator;
use Alocasia\Interpreter\Evaluator\EvaluatorException;
use Alocasia\Interpreter\Parser\Parser;
use Alocasia\Interpreter\Parser\ParserException;
use Alocasia\Interpreter\Scanner\Scanner;
use Alocasia\Interpreter\Scanner\ScannerException;

class ControllerOfInterpretingInteractively implements IController
{
    public function run(): void {
        echo "入力してください (exitで終了):\n";
        $evaluator = new Evaluator(
            hashmap: [],
            stack: [],
            tokens: []
        );

        while (true) {
            echo "> ";
            $userInput = fgets(STDIN);

            if (trim($userInput) == "exit") {
                echo "bye";
                exit(0);
            }

            $scanner = new Scanner($userInput);
            $tokens = [];
            try {
                $tokens = $scanner->scan();
            } catch (ScannerException $e) {
                printf(
                    "Scan Error: line: %d, position: %d: %s\n",
                    $e->source_code_line,
                    $e->source_code_position,
                    $e->getMessage(),
                );
            }

            $parser = new Parser(tokens: $tokens);
            $tokensParsed = [];
            try {
                $tokensParsed = $parser->parse();
            } catch (ParserException $e) {
                printf(
                    "Parse Error: line: %d, position: %d: %s\n",
                    $e->source_code_line,
                    $e->source_code_position,
                    $e->getMessage(),
                );
            }

            $evaluator->token_queue = $tokensParsed;
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
            }
        }
    }
}