<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Scanner;

use Alocasia\Interpreter\Token\Asterisk;
use Alocasia\Interpreter\Token\BuiltinFunction;
use Alocasia\Interpreter\Token\ConditionalBranch;
use Alocasia\Interpreter\Token\DoubleEqual;
use Alocasia\Interpreter\Token\DoubleSlash;
use Alocasia\Interpreter\Token\Equal;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\GreaterThan;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\LeftBrace;
use Alocasia\Interpreter\Token\LessThan;
use Alocasia\Interpreter\Token\Loop;
use Alocasia\Interpreter\Token\Minus;
use Alocasia\Interpreter\Token\Plus;
use Alocasia\Interpreter\Token\RightBrace;
use Alocasia\Interpreter\Token\Slash;
use Alocasia\Interpreter\Token\Token;
use Alocasia\Interpreter\Token\Variable;

class Scanner
{
    private string $source;
    private int $line = 0;
    private int $position = 0;

    private const array Keywords = ['if', 'loop'];
    private const array BuiltinFunctions = ['print', 'print_ascii_str'];

    /**
     * @param string $source
     */
    public function __construct(string $source) {
        $this->source = $source;
    }

    /**
     * @return list<Token>
     * @throws ScannerException
     */
    public function scan(): array {
        $tokens = [];
        $buffer = '';
        $characters = mb_str_split($this->source);

        while (true) {
            if (empty($characters))  break;

            $firstCharacter = array_shift($characters);

            // 改行
            if ($firstCharacter === '\n') {
                $this->line++;
                continue;
            }
            // スペース
            $isSpace = trim($firstCharacter) === "";
            if ($isSpace) {
                $this->position++;
                continue;
            }
            // 一文字で確定するキーワード
            if ($firstCharacter === '+') {
                $tokens[] = new Plus(
                    $this->line,
                    $this->position,
                );
                $this->position++;
                continue;
            }
            if ($firstCharacter === '*') {
                $tokens[] = new Asterisk(
                    $this->line,
                    $this->position,
                );
                $this->position++;
                continue;
            }
            if ($firstCharacter === '<') {
                $tokens[] = new LessThan(
                    $this->line,
                    $this->position,
                );
                $this->position++;
                continue;
            }
            if ($firstCharacter === '>') {
                $tokens[] = new GreaterThan(
                    $this->line,
                    $this->position,
                );
                $this->position++;
                continue;
            }
            if ($firstCharacter === '{') {
                $tokens[] = new LeftBrace(
                    $this->line,
                    $this->position,
                );
                $this->position++;
                continue;
            }
            if ($firstCharacter === '}') {
                $tokens[] = new RightBrace(
                    $this->line,
                    $this->position,
                );
                $this->position++;
                continue;
            }
            // 2文字で確定するキーワード
            if ($firstCharacter === '=') {
                $nextCharacter = array_shift($characters);
                if ($nextCharacter === '=') {
                    $tokens[] = new DoubleEqual(
                        $this->line,
                        $this->position,
                    );
                } else if (ctype_space($nextCharacter)) {
                    $tokens[] = new Equal(
                        $this->line,
                        $this->position,
                    );
                } else {
                    throw new ScannerException(
                        source_code_line: $this->line,
                        source_code_position: $this->position,
                        message: "予期しない文字が読み込まれました. =の後は=か空白が期待されます. 読み込まれた文字: ".$nextCharacter
                    );
                }
                $this->position += 2;
                continue;
            }
            // /: 除算, //: 整数除算
            if ($firstCharacter === '/') {
                $nextCharacter = array_shift($characters);
                if ($nextCharacter === '/') {
                    $tokens[] = new DoubleSlash(
                        $this->line,
                        $this->position,
                    );
                } else if (ctype_space($nextCharacter)) {
                    $tokens[] = new Slash(
                        $this->line,
                        $this->position,
                    );
                } else {
                    throw new ScannerException(
                        source_code_line: $this->line,
                        source_code_position: $this->position,
                        message: "予期しない文字が読み込まれました. /の後は/か空白が期待されます. 読み込まれた文字: ".$nextCharacter
                    );
                }
                $this->position += 2;
                continue;
            }
            // たくさん見ないとわからない
            // NegativeNumber: -1, -3.14
            if ($firstCharacter === '-') {
                $nextCharacter = $characters[0];
                if (ctype_space($nextCharacter)) {
                    $tokens[] = new Minus(
                        $this->line,
                        $this->position,
                    );
                } else if (ctype_digit($nextCharacter)) {
                    // - 文字の処理
                    $buffer .= $firstCharacter;
                    $this->position++;
                    // - に続く数字の処理
                    $isFloat = false;
                    $firstCharacter = array_shift($characters);
                    while (true) {
                        if (!ctype_digit($firstCharacter) && $firstCharacter != '.') {
                            throw new ScannerException(
                                source_code_line: $this->line,
                                source_code_position: $this->position,
                                message: "数値リテラルに予期しない文字が読み込まれました. 読み込まれた文字: " . $firstCharacter
                            );
                        }

                        $buffer .= $firstCharacter;

                        if ($firstCharacter === '.') {
                            $isFloat = true;
                        }

                        $isSpace = isset($characters[0]) && trim($characters[0]) === "";
                        if ($isSpace || count($characters) === 0) {
                            if ($isFloat) {
                                $tokens[] = new FloatLiteral(
                                    $this->line,
                                    $this->position,
                                    (float)$buffer
                                );
                            } else {
                                $tokens[] = new IntegerLiteral(
                                    $this->line,
                                    $this->position,
                                    (integer)$buffer
                                );
                            }
                            // bufferをクリア
                            $buffer = '';
                            break;
                        }
                        $this->position++;
                        $firstCharacter = array_shift($characters);
                    }
                } else {
                    throw new ScannerException(
                        source_code_line: $this->line,
                        source_code_position: $this->position,
                        message: "予期しない文字が読み込まれました. -の後は数値か空白が期待されます. 読み込まれた文字: " . $nextCharacter
                    );
                }
                continue;
            }
            // PositiveNumber: 0, 1, 3.14
            if (ctype_digit($firstCharacter)) {
                $isFloat = false;
                while (true) {
                    // bufferに追加しても問題ないかチェック
                    if (!ctype_digit($firstCharacter) && $firstCharacter != '.') {
                        throw new ScannerException(
                            source_code_line: $this->line,
                            source_code_position: $this->position,
                            message: "数値リテラルに予期しない文字が読み込まれました. 読み込まれた文字: " . $firstCharacter
                        );
                    }
                    $buffer .= $firstCharacter;

                    if ($firstCharacter === '.') {
                        $isFloat = true;
                    }
                    // スペースか最終文字
                    $isSpace = isset($characters[0]) && trim($characters[0]) === "";
                    if ($isSpace || count($characters) === 0) {
                        if ($isFloat) {
                            $tokens[] = new FloatLiteral(
                                $this->line,
                                $this->position,
                                (float)$buffer
                            );
                        } else {
                            $tokens[] = new IntegerLiteral(
                                $this->line,
                                $this->position,
                                (integer)$buffer
                            );
                        }
                        // bufferをクリア
                        $buffer = '';
                        break;
                    }
                    $this->position++;
                    $firstCharacter = array_shift($characters);
                }
                continue;
            }
            // identifier: 関数名・制御構文
            if (ctype_alpha($firstCharacter)) {
                while (preg_match('/[a-zA-Z_]/', $firstCharacter)) {
                    $buffer .= $firstCharacter;
                    $isSpace = isset($characters[0]) && trim($characters[0]) === "";
                    if ($isSpace || count($characters) === 0) {
                        $tokens[] = $this->identifyToken($buffer);
                        // bufferをクリア
                        $buffer = '';
                        break;
                    }
                    $this->position++;
                    $firstCharacter = array_shift($characters);
                }
            }
        }
        return $tokens;
    }

    /**
     * @throws ScannerException
     */
    private function identifyToken(string $buffer): Token {
        if (in_array($buffer, self::Keywords)) {
            return match ($buffer) {
                "if" => new ConditionalBranch(
                    line: $this->line,
                    position: $this->position,
                ),
                "loop" => new Loop(
                    line: $this->line,
                    position: $this->position,
                )
            };
        }

        if (in_array($buffer, self::BuiltinFunctions)) {
            return new BuiltinFunction(
                line: $this->line,
                position: $this->position,
                name: $buffer,
            );
        }

        if (preg_match('/[a-zA-Z_]+/', $buffer)) {
            return new Variable(
                $this->line,
                $this->position,
                $buffer,
            );
        }

        throw new ScannerException(
            source_code_line: $this->line,
            source_code_position: $this->position,
            message: "変数名で使用できない文字が含まれています"
        );
    }
}