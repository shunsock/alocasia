<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Parser;

use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\LeftBrace;
use Alocasia\Interpreter\Token\RightBrace;
use Alocasia\Interpreter\Token\Token;

class Parser
{
    /** @var Token[]  */
    private array $tokens;

    /**
     * @param list<Token> $tokens
     */
    public function __construct(array $tokens) {
        $this->tokens = $tokens;
    }

    /**
     * @return Token[]
     * @throws ParserException
     */
    public function parse(): array {
        $parsed_tokens = [];
        while ($token = array_shift($this->tokens)) {
            if ($token instanceof LeftBrace) {
                $parsed_tokens[] = $this->parseBlock($this->tokens, $token->line, $token->position);
                continue;
            }
            $parsed_tokens[] = $token;
        }
        return $parsed_tokens;
    }

    /**
     * @param Token[]& $tokens
     * @param int $line
     * @param int $position
     * @return Block
     * @throws ParserException
     */
    private function parseBlock(array &$tokens, int $line, int $position): Block {
        $tokens_in_block = [];
        while ($token = array_shift($tokens)) {
            if ($token instanceof LeftBrace) {
                $tokens_in_block[] = $this->parseBlock($tokens, $token->line, $token->position);
                continue;
            }
            if ($token instanceof RightBrace) {
                return new Block(
                    line: $line,
                    position: $position,
                    tokens: $tokens_in_block
                );
            }
            $tokens_in_block[] = $token;
        }
        throw new ParserException(
            source_code_line: $line,
            source_code_position: $position,
            message: "{ (line: " . $line . ", position: " . $position . ") に対応する } が存在しません"
        );
    }
}
