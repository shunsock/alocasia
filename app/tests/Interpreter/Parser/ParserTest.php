<?php

declare(strict_types = 1);

namespace Interpreter\Parser;

use Alocasia\Interpreter\Parser\Parser;
use Alocasia\Interpreter\Parser\ParserException;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\Equal;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\LeftBrace;
use Alocasia\Interpreter\Token\Plus;
use Alocasia\Interpreter\Token\RightBrace;
use Alocasia\Interpreter\Token\Variable;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @throws ParserException
     */
    public function testParseSimpleBlock(): void
    {
        $tokens = [
            new LeftBrace(1, 1),
            new IntegerLiteral(1, 2, 42),
            new RightBrace(1, 3),
        ];

        $parser = new Parser($tokens);

        $parsed = $parser->parse();

        $this->assertCount(1, $parsed);
        $this->assertInstanceOf(Block::class, $parsed[0]);

        /** @var Block $block */
        $block = $parsed[0];
        $this->assertCount(1, $block->tokens);
        $this->assertInstanceOf(IntegerLiteral::class, $block->tokens[0]);
        $this->assertSame(42, $block->tokens[0]->value);
    }

    /**
     * @throws ParserException
     */
    public function testParseNestedBlocks(): void
    {
        // { { 1 } }
        $tokens = [
            new LeftBrace(1, 1),
            new LeftBrace(1, 2),
            new IntegerLiteral(1, 3, 99),
            new RightBrace(1, 4),
            new RightBrace(1, 5),
        ];

        $parser = new Parser($tokens);

        $parsed = $parser->parse();

        $this->assertCount(1, $parsed);
        $this->assertInstanceOf(Block::class, $parsed[0]);

        /** @var Block $outerBlock */
        $outerBlock = $parsed[0];
        $this->assertCount(1, $outerBlock->tokens);
        $this->assertInstanceOf(Block::class, $outerBlock->tokens[0]);

        /** @var Block $innerBlock */
        $innerBlock = $outerBlock->tokens[0];
        $this->assertCount(1, $innerBlock->tokens);
        $this->assertInstanceOf(IntegerLiteral::class, $innerBlock->tokens[0]);
        $this->assertSame(99, $innerBlock->tokens[0]->value);
    }

    public function testAssertUnmatchedLeftBrace(): void
    {
        // { 1
        $tokens = [
            new LeftBrace(1, 1),
            new IntegerLiteral(1, 2, 123),
        ];

        $parser = new Parser($tokens);

        $this->expectException(ParserException::class);
        $this->expectExceptionMessage("{ (line: 1, position: 1) に対応する } が存在しません");

        $parser->parse();
    }

    /**
     * @throws ParserException
     */
    public function testParseWithMultipleTokens(): void
    {
        // 10 + 20 + { 30 }
        $tokens = [
            new IntegerLiteral(1, 1, 10),
            new Plus(1, 2),
            new IntegerLiteral(1, 3, 20),
            new LeftBrace(1, 4),
            new IntegerLiteral(1, 5, 30),
            new RightBrace(1, 6),
        ];

        $parser = new Parser($tokens);

        $parsed = $parser->parse();

        $this->assertCount(4, $parsed);
        $this->assertInstanceOf(IntegerLiteral::class, $parsed[0]);
        $this->assertInstanceOf(Plus::class, $parsed[1]);
        $this->assertInstanceOf(IntegerLiteral::class, $parsed[2]);
        $this->assertInstanceOf(Block::class, $parsed[3]);

        /** @var Block $block */
        $block = $parsed[3];
        $this->assertCount(1, $block->tokens);
        $this->assertInstanceOf(IntegerLiteral::class, $block->tokens[0]);
        $this->assertSame(30, $block->tokens[0]->value);
    }

    /**
     * @throws ParserException
     */
    public function testParseBlockForDeclaration(): void
    {
        // x = {1}
        $tokens = [
            new Variable(1, 1, "x"),
            new Equal(1, 2),
            new LeftBrace(1, 3),
            new IntegerLiteral(1, 4, 0),
            new RightBrace(1, 5),
        ];

        $parser = new Parser($tokens);

        $parsed = $parser->parse();

        $this->assertCount(3, $parsed);
        $this->assertInstanceOf(Variable::class, $parsed[0]);
        $this->assertInstanceOf(Equal::class, $parsed[1]);
        $this->assertInstanceOf(Block::class, $parsed[2]);

        /** @var Block $block */
        $block = $parsed[2];
        $this->assertCount(1, $block->tokens);
        $this->assertInstanceOf(IntegerLiteral::class, $block->tokens[0]);
        $this->assertSame(0, $block->tokens[0]->value);
    }
}