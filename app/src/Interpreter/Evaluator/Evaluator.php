<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator;

use Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject\AlocasiaObject;
use Alocasia\Interpreter\Evaluator\StackedItem\StackedItem;
use Alocasia\Interpreter\Token\Asterisk;
use Alocasia\Interpreter\Token\Block;
use Alocasia\Interpreter\Token\BuiltinFunction;
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
            IntegerLiteral::class => EvaluatorOfCreatingAlocasiaIntegerTypeObject::evaluate($this),
            FloatLiteral::class => EvaluatorOfCreatingAlocasiaFloatTypeObject::evaluate($this),
            Block::class => EvaluatorOfCreatingAlocasiaBlock::evaluate($this),
            Variable::class => EvaluatorOfVariableOperation::evaluate($this),
            Plus::class => EvaluatorOfCalculatingAddition::evaluate($this),
            Minus::class => EvaluatorOfCalculatingSubtraction::evaluate($this),
            Asterisk::class => EvaluatorOfCalculatingMultiplication::evaluate($this),
            Slash::class => EvaluatorOfCalculatingDivision::evaluate($this),
            DoubleEqual::class => EvaluatorOfCalculatingCondition::evaluate($this),
            ConditionalBranch::class => EvaluatorOfConditionalBranch::evaluate($this),
            Loop::class => EvaluatorOfLoop::evaluate($this),
            BuiltinFunction::class => EvaluatorOfBuiltinFunction::evaluate($this),
            default => $this,
        };
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
     * @param class-string<StackedItem> $expectedStackedItemClass
     * @param StackedItem $actualStackedItem
     * @return StackedItem
     * @throws EvaluatorException
     */
    public function validateStackedItem(
        string $expectedStackedItemClass,
        StackedItem $actualStackedItem
    ): StackedItem
    {
        // 期待するクラスと一致するかを確認
        if (!($actualStackedItem instanceof $expectedStackedItemClass)) {
            throw new EvaluatorException(
                message: sprintf(
                    "予期しないトークンが検出されました。期待: %s, 実際: %s",
                    $expectedStackedItemClass,
                    get_class($actualStackedItem)
                ),
            );
        }

        return $actualStackedItem;
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

    /**
     * @return AlocasiaObject
     * @throws EvaluatorException
     */
    public function getOperandObject(): AlocasiaObject
    {
        // stackにindexでアクセスするのでValidation
        if (empty($this->stack)) throw new EvaluatorException(
            message: "Stack Underflow: Stackが空でOperandを作成できません"
        );

        $last_stacked_item = end($this->stack);

        // Stack topがAlocasiaObjectの場合, AlocasiaObjectを返す
        if ($last_stacked_item instanceof AlocasiaObject) {
            $this->popItemFromStack();
            return $last_stacked_item;
        }

        // Stack topがAlocasiaBlockの場合は
        // AlocasiaBlockの評価後のStack topのAlocasiaObjectを返す
        EvaluatorOfAlocasiaBlock::evaluate($this);

        $stacked_item_after_evaluate_block = $this->popItemFromStack();

        /** @var AlocasiaObject $alocasiaObject */
        $alocasiaObject = $this->validateStackedItem(
            expectedStackedItemClass: AlocasiaObject::class,
            actualStackedItem: $stacked_item_after_evaluate_block,
        );

        return $alocasiaObject;
    }
}