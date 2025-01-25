<?php

declare(strict_types=1);

namespace Alocasia\Interpreter\Evaluator\StackedItem\AlocasiaObject;

use Alocasia\Interpreter\Evaluator\StackedItem\StackedItem;

readonly class AlocasiaObject extends StackedItem
{
    public AlocasiaObjectType $type;
    public mixed $value;

    public function __construct(AlocasiaObjectType $type, mixed $value) {
        $this->type = $type;
        $this->value = $value;
    }
}