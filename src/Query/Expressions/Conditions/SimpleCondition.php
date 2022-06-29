<?php

declare(strict_types=1);

namespace Yii\DBAL\Query\Expressions\Conditions;

use Yii\DBAL\Contracts\Query\ConditionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ConditionInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use Yii\DBAL\Exceptions\InvalidExpressionFormatException;
use Yii\DBAL\Query\Expressions\BaseExpressionBuilder;
use Yii\DBAL\Query\Expressions\Operator;

use function count;
use function in_array;
use function is_array;

final class SimpleCondition implements ConditionInterface
{
    public const SUPPORT_OPERATORS = [
        Operator::EQ,
        Operator::GT,
        Operator::GTE,
        Operator::LT,
        Operator::LTE,
        Operator::NEQ,
        Operator::IN,
        Operator::NOT_IN,
    ];

    public function __construct(
        private readonly string|ExpressionInterface $column,
        private readonly string $operator,
        private readonly mixed $value
    ) {
    }

    public function build(ExpressionBuilderInterface $exprBuilder): ExpressionInterface
    {
        $operator = $this->operator;
        $value = BaseExpressionBuilder::normalizeValue($this->value, $exprBuilder);

        if ($operator === Operator::IN || $operator === Operator::NOT_IN) {
            if (!is_array($value) && !($value instanceof ExpressionInterface)) {
                throw new InvalidExpressionFormatException();
            }

            return $operator === Operator::IN
                ? $exprBuilder->in($this->column, $value)
                : $exprBuilder->notIn($this->column, $value);
        }

        if ($value === null) {
            return match ($operator) {
                Operator::EQ => $exprBuilder->isNull($this->column),
                Operator::NEQ => $exprBuilder->isNotNull($this->column),
                default => throw new InvalidExpressionFormatException()
            };
        }

        if (in_array($this->operator, self::SUPPORT_OPERATORS, true)) {
            return $exprBuilder->comparison($this->column, $this->operator, $this->value);
        }

        throw new InvalidExpressionFormatException();
    }

    /**
     * @param array{1: string} $arr
     */
    public static function newFromAltArray(array $arr): static
    {
        if (count($arr) !== 3 || !array_is_list($arr)) {
            throw new InvalidExpressionFormatException();
        }

        return new static($arr[0], $arr[1], $arr[2]);
    }

    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        if (count($arr) !== 4 || !array_is_list($arr)) {
            throw new InvalidExpressionFormatException();
        }

        return new static($arr[1], $arr[2], $arr[3]);
    }
}
