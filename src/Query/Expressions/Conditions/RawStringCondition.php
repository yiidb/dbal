<?php

declare(strict_types=1);

namespace Yii\DBAL\Query\Expressions\Conditions;

use Yii\DBAL\Contracts\Query\ConditionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ConditionInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use Yii\DBAL\Exceptions\InvalidExpressionFormatException;

use function count;
use function is_string;

final class RawStringCondition implements ConditionInterface
{
    public function __construct(public readonly string $string)
    {
    }

    public function build(ExpressionBuilderInterface $exprBuilder): string
    {
        return $this->string;
    }

    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        if (count($arr) !== 2 || empty($string = $arr[1] ?? null) || !is_string($string)) {
            throw new InvalidExpressionFormatException();
        }

        return new static($string);
    }
}
