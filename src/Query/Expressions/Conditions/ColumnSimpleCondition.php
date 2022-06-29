<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use TypeError;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;
use YiiDb\DBAL\Query\Expressions\Operator;

use function count;
use function in_array;

/**
 * @psalm-immutable
 */
final class ColumnSimpleCondition implements ConditionInterface
{
    public const SUPPORT_OPERATORS = [
        Operator::EQ,
        Operator::GT,
        Operator::GTE,
        Operator::LT,
        Operator::LTE,
        Operator::NEQ,
    ];

    public function __construct(
        private readonly string $columnLeft,
        private readonly string $operator,
        private readonly string $columnRight
    ) {
    }

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /** @var self $self */
        if (in_array($self->operator, self::SUPPORT_OPERATORS, true)) {
            return $exprBuilder->comparisonColumns($self->columnLeft, $self->operator, $self->columnRight);
        }

        throw new InvalidExpressionFormatException();
    }

    /**
     * @param array{0: string, 1: string, 2: string, 3: string} $arr
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

        [, $columnLeft, $operator, $columnRight] = $arr;

        try {
            return new static($columnLeft, $operator, $columnRight);
        } catch (TypeError $e) {
            throw new InvalidExpressionFormatException(previous: $e);
        }
    }
}
