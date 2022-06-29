<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

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
        self|ConditionInterface $_this,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /** @var self $_this */
        if (in_array($_this->operator, self::SUPPORT_OPERATORS, true)) {
            return $exprBuilder->comparisonColumns($_this->columnLeft, $_this->operator, $_this->columnRight);
        }

        throw new InvalidExpressionFormatException();
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
