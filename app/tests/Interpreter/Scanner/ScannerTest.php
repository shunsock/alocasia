<?php

declare(strict_types=1);

namespace Interpreter\Scanner;

use Alocasia\Interpreter\Scanner\Scanner;
use PHPUnit\Framework\TestCase;
use Alocasia\Interpreter\Scanner\ScannerException;
use Alocasia\Interpreter\Token\{DoubleEqual,
    DoubleSlash,
    Equal,
    Plus,
    Asterisk,
    LessThan,
    GreaterThan,
    LeftBrace,
    RightBrace,
    IntegerLiteral,
    FloatLiteral,
    Slash,
    Variable,
    BuiltinFunction,
    ConditionalBranch,
    Loop};

class ScannerTest extends TestCase
{
    /**
     * @throws ScannerException
     */
    public function testSingleCharacterTokens(): void
    {
        $scanner = new Scanner("+ * < > { }");
        $tokens = $scanner->scan();

        $this->assertCount(6, $tokens);
        $this->assertInstanceOf(Plus::class, $tokens[0]);
        $this->assertInstanceOf(Asterisk::class, $tokens[1]);
        $this->assertInstanceOf(LessThan::class, $tokens[2]);
        $this->assertInstanceOf(GreaterThan::class, $tokens[3]);
        $this->assertInstanceOf(LeftBrace::class, $tokens[4]);
        $this->assertInstanceOf(RightBrace::class, $tokens[5]);
    }

    /**
     * @throws ScannerException
     */
    public function testPositiveNumberLiterals(): void
    {
        $scanner = new Scanner("42 3.14");
        $tokens = $scanner->scan();

        $this->assertCount(2, $tokens);
        $this->assertInstanceOf(IntegerLiteral::class, $tokens[0]);
        $this->assertSame(42, $tokens[0]->value);
        $this->assertSame(3.14, $tokens[1]->value);
    }

    /**
     * @throws ScannerException
     */
    public function testAssertInvalidPositiveNumberLiteral(): void
    {
        $this->expectException(ScannerException::class);
        $this->expectExceptionMessage("数値リテラルに予期しない文字が読み込まれました. 読み込まれた文字: a");

        $scanner = new Scanner("4a2");
        $scanner->scan();
    }

    /**
     * @throws ScannerException
     */
    public function testNegativeNumberLiteral(): void
    {
        $scanner = new Scanner("-42 -3.14");
        $tokens = $scanner->scan();

        $this->assertCount(2, $tokens);
        $this->assertInstanceOf(IntegerLiteral::class, $tokens[0]);
        $this->assertSame(-42, $tokens[0]->value);
        $this->assertInstanceOf(FloatLiteral::class, $tokens[1]);
        $this->assertSame(-3.14, $tokens[1]->value);
    }

    /**
     * @throws ScannerException
     */
    public function testAssertInvalidNegativeNumberLiteral(): void
    {
        $this->expectException(ScannerException::class);
        $this->expectExceptionMessage("数値リテラルに予期しない文字が読み込まれました. 読み込まれた文字: a");

        $scanner = new Scanner("-4a2");
        $scanner->scan();
    }

    /**
     * @throws ScannerException
     */
    public function testKeywordsAndBuiltinFunctions(): void
    {
        $scanner = new Scanner("if loop print print_ascii_str");
        $tokens = $scanner->scan();

        $this->assertCount(4, $tokens);

        $this->assertInstanceOf(ConditionalBranch::class, $tokens[0]);
        $this->assertInstanceOf(Loop::class, $tokens[1]);
        $this->assertInstanceOf(BuiltinFunction::class, $tokens[2]);
        $this->assertSame("print", $tokens[2]->name);
        $this->assertInstanceOf(BuiltinFunction::class, $tokens[3]);
        $this->assertSame("print_ascii_str", $tokens[3]->name);
    }

    /**
     * @throws ScannerException
     */
    public function testVariableNames(): void
    {
        $scanner = new Scanner("variable_name anotherVariable");
        $tokens = $scanner->scan();

        $this->assertCount(2, $tokens);

        $this->assertInstanceOf(Variable::class, $tokens[0]);
        $this->assertSame("variable_name", $tokens[0]->name);

        $this->assertInstanceOf(Variable::class, $tokens[1]);
        $this->assertSame("anotherVariable", $tokens[1]->name);
    }

    /**
     * @throws ScannerException
     */
    public function testUnexpectedCharacters(): void
    {
        $this->expectException(ScannerException::class);
        $this->expectExceptionMessage("プログラムで使用できない文字が含まれています: $");

        $scanner = new Scanner("42 $");
        $scanner->scan();
    }

    /**
     * @throws ScannerException
     */
    public function testMultiCharacterTokens(): void
    {
        $scanner = new Scanner("= == / //");
        $tokens = $scanner->scan();

        $this->assertCount(4, $tokens);

        $this->assertInstanceOf(Equal::class, $tokens[0]);
        $this->assertInstanceOf(DoubleEqual::class, $tokens[1]);
        $this->assertInstanceOf(Slash::class, $tokens[2]);
        $this->assertInstanceOf(DoubleSlash::class, $tokens[3]);
    }
}
