<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaBlock\AlocasiaBlock;
use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\StackedItem;
use Alocasia\Interpreter\Token\Asterisk;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\ConditionalBranch;
use Alocasia\Interpreter\Token\DoubleEqual;
use Alocasia\Interpreter\Token\FloatLiteral;
use Alocasia\Interpreter\Token\IntegerLiteral;
use Alocasia\Interpreter\Token\Loop;
use Alocasia\Interpreter\Token\Minus;
use Alocasia\Interpreter\Token\Plus;
use Alocasia\Interpreter\Token\Slash;
use Alocasia\Interpreter\Token\Token;
use Alocasia\Interpreter\Token\Variable;

class Evaluator
{
    /** @var AlocasiaObject[]  */
    public array $hashmap;

    /** @var StackedItem[]  */
    public array $stack;

    /** @var Token[]  */
    public array $token_queue;

    /**
     * @param array<string, AlocasiaObject> $hashmap
     * @param StackedItem[] $stack
     * @param Token[] $tokens
     */
    public function __construct(array $hashmap, array $stack, array $tokens) {
        $this->hashmap = $hashmap;
        $this->stack = $stack;
        $this->token_queue = $tokens;
    }

    /**
     * @return Evaluator
     * @throws EvaluatorException
     */
    public function evaluate(): Evaluator {
        while($this->token_queue) {
            $this->_evaluate();
        }
        return $this;
    }

    /**
     * @throws EvaluatorException
     */
    private function _evaluate(): void
    {
        match (get_class($this->token_queue[0])) {
            IntegerLiteral::class => EvaluatorOfIntegerLiteral::evaluate($this),
            FloatLiteral::class => EvaluatorOfFloatLiteral::evaluate($this),
            Block::class => EvaluatorOfBlock::evaluate($this),
            Variable::class => EvaluatorOfIdentifier::evaluate($this),
            Plus::class => EvaluatorOfAddition::evaluate($this),
            Minus::class => EvaluatorOfSubtraction::evaluate($this),
            Asterisk::class => EvaluatorOfMultiplication::evaluate($this),
            Slash::class => EvaluatorOfDivision::evaluate($this),
            DoubleEqual::class => EvaluatorOfCondition::evaluate($this),
            ConditionalBranch::class => EvaluatorOfConditionalBranch::evaluate($this),
            Loop::class => EvaluatorOfLoop::evaluate($this),
            default => $this,
        };
    }

    /**
     * @throws EvaluatorException
     */
    public function evaluateAlocasiaBlock(): void
    {
        $alocasia_block = $this->popItemFromStack();
        if ($alocasia_block instanceof AlocasiaBlock === false) {
            throw new EvaluatorException(
                message: "予期しないエラーが発生しました",
                source_code_line: 0,
                source_code_position: 0,
            );
        } else {
            $block_evaluator = new Evaluator(
                // 変数は全てGlobal Scope
                hashmap: $this->hashmap,
                // stackも共有
                stack: $this->stack,
                // tokenはBlockの持つtoken
                tokens: $alocasia_block->tokens,
            );
            $block_evaluator->evaluate();

            // 共有して更新されたhashmapとstackを元のevaluatorに渡す
            $this->hashmap = $block_evaluator->hashmap;
            $this->stack = $block_evaluator->stack;
        }
    }

    /**
     * @param StackedItem $item
     * @return void
     */
    public function pushItemToStack(StackedItem $item): void
    {
        $this->stack[] = $item;
    }

    /**
     * @return StackedItem
     * @throws EvaluatorException
     */
    public function popItemFromStack(): StackedItem
    {
        $stacked_item = array_pop($this->stack);
        if (!$stacked_item) throw new EvaluatorException(
            message: "Stack Underflowが発生しました"
        );
        return $stacked_item;
    }

    /**
     * @param Token $token
     * @return void
     */
    public function enqueueToken(Token $token): void
    {
        $this->token_queue[] = $token;
    }

    /**
     * @throws EvaluatorException
     */
    public function dequeueToken(): Token
    {
        $t = array_shift($this->token_queue);
        if (!$t) throw new EvaluatorException(
            message: "Token Queue Underflowが発生しました"
        );
        return $t;
    }

    /**
     * @param class-string<Token> $expectedTokenClass
     * @param Token $actualToken
     * @return Token
     * @throws EvaluatorException
     */
    public function validateToken(string $expectedTokenClass, Token $actualToken): Token
    {
        // 期待するクラスと一致するかを確認
        if (!($actualToken instanceof $expectedTokenClass)) {
            throw new EvaluatorException(
                message: sprintf(
                    "予期しないトークンが検出されました。期待: %s, 実際: %s",
                    $expectedTokenClass,
                    get_class($actualToken)
                ),
                source_code_line: $actualToken->line,
                source_code_position: $actualToken->position
            );
        }

        return $actualToken;
    }
}