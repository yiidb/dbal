<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use TypeError;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;
use YiiDb\DBAL\Query\Expressions\BaseExpressionBuilder;
use YiiDb\DBAL\Query\Expressions\Operator;

use function count;
use function in_array;
use function is_array;

/**
 * @psalm-immutable
 */
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

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /** @var self $self */
        $operator = $self->operator;
        $value = BaseExpressionBuilder::normalizeValue($self->value, $exprBuilder);

        if ($operator === Operator::IN || $operator === Operator::NOT_IN) {
            if (!is_array($value) && !($value instanceof ExpressionInterface)) {
                throw new InvalidExpressionFormatException();
            }

            return $operator === Operator::IN
                ? $exprBuilder->in($self->column, $value)
                : $exprBuilder->notIn($self->column, $value);
        }

        if ($value === null) {
            return match ($operator) {
                Operator::EQ => $exprBuilder->isNull($self->column),
                Operator::NEQ => $exprBuilder->isNotNull($self->column),
                default => throw new InvalidExpressionFormatException()
            };
        }

        if (in_array($self->operator, self::SUPPORT_OPERATORS, true)) {
            return $exprBuilder->comparison($self->column, $self->operator, $self->value);
        }

        throw new InvalidExpressionFormatException();
    }

    /**
     * @param array{0: string|ExpressionInterface, 1: string, 2: mixed} $arr
     */
    public static function newFromAltArray(array $arr): static
    {
        if (count($arr) !== 3) {
            throw new InvalidExpressionFormatException();
        }

        [$column, $operator, $value] = $arr;

        try {
            return new self($column, $operator, $value);
        } catch (TypeError $e) {
            throw new InvalidExpressionFormatException(previous: $e);
        }
    }

    /**
     * @param array{0: string, 1: string|ExpressionInterface, 2: string, 3: mixed} $arr
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        if (count($arr) !== 4) {
            throw new InvalidExpressionFormatException();
        }
        [, $column, $operator, $value] = $arr;

        try {
            return new self($column, $operator, $value);
        } catch (TypeError $e) {
            throw new InvalidExpressionFormatException(previous: $e);
        }
    }
}
