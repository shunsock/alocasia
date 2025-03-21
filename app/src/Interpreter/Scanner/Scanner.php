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

    private const array BuiltinFunctions = ['print', 'print_ascii_str'];

    /**
     * @param string $source
     */
    public function __construct(string $source) {
        $this->source = $source;
    }

    /**
     * @return Token[]
     * @throws ScannerException
     */
    public function scan(): array {
        $tokens = [];
        $buffer = '';
        $characters = mb_str_split($this->source);

        while (!empty($characters)) {

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
                    $this->position++;
                    // - に続く数字の処理
                    $tokens[] = $this->scanNumber('-', $characters);
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
                // すでに最初の数字をバッファに入れておく
                $tokens[] = $this->scanNumber($firstCharacter, $characters);
                continue;
            }
            // identifier: 関数名・制御構文
            if (ctype_alpha($firstCharacter)) {
                $tokens[] = $this->scanIdentifier($firstCharacter, $characters);
                continue;
            }
            throw new ScannerException(
                source_code_line: $this->line,
                source_code_position: $this->position,
                message: "プログラムで使用できない文字が含まれています: " . $firstCharacter
            );
        }
        return $tokens;
    }

    /**
     * 数値リテラル（正負両方）の処理を共通化する
     *
     * @param string $initialBuffer 初期バッファ（符号を含む場合もある）
     * @param string[] &$characters 残りの文字列配列（参照渡し）
     * @return Token IntegerLiteral or FloatLiteral
     * @throws ScannerException
     */
    private function scanNumber(string $initialBuffer, array &$characters): token {
        $buffer = $initialBuffer;
        $isFloat = false;

        while (!empty($characters)) {
            // 次の文字を覗く
            $next = $characters[0];
            // 空白が来たら、トークンの境界とみなす
            if (trim($next) === "") {
                break;
            }
            // 数字またはドット以外が来た場合はエラー
            if (!ctype_digit($next) && $next !== '.') {
                throw new ScannerException(
                    source_code_line: $this->line,
                    source_code_position: $this->position,
                    message: "数値リテラルに予期しない文字が読み込まれました. 読み込まれた文字: " . $next
                );
            }
            // 消費してバッファに追加
            $buffer .= array_shift($characters);
            if ($next === '.') {
                $isFloat = true;
            }
            $this->position++;
        }

        if ($isFloat) {
            return new FloatLiteral(
                $this->line,
                $this->position,
                (float)$buffer
            );
        } else {
            return new IntegerLiteral(
                $this->line,
                $this->position,
                (int)$buffer
            );
        }
    }

    /**
     * 識別子（関数名・制御構文・変数名）の走査処理を共通化する
     *
     * @param string $initialBuffer 初期バッファ（既に1文字分の識別子の先頭が入っている）
     * @param string[] &$characters 残りの文字列配列（参照渡し）
     * @return Token 識別子に対応する Token（BuiltinFunction、ConditionalBranch、Loop、Variable など）
     * @throws ScannerException
     */
    private function scanIdentifier(string $initialBuffer, array &$characters): Token {
        $buffer = $initialBuffer;

        while (!empty($characters)) {
            $next = $characters[0];

            // 空白にぶつかった場合はトークンの区切りとみなす
            if (trim($next) === "") {
                break;
            }

            // 識別子として許容するのはアルファベットとアンダースコアのみ
            if (!preg_match('/[a-zA-Z_]/', $next)) {
                break;
            }

            $buffer .= array_shift($characters);
            $this->position++;
        }

        return $this->identifyToken($buffer);
    }


    /**
     * @throws ScannerException
     */
    private function identifyToken(string $buffer): Token {
        if ($buffer === "if") {
            return new ConditionalBranch(
                line: $this->line,
                position: $this->position,
            );
        }
        if ($buffer === "loop") {
            return new Loop(
                line: $this->line,
                position: $this->position,
            );
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