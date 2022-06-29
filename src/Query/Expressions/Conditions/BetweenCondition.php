<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use TypeError;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;

use function count;

/**
 * @psalm-immutable
 */
final class BetweenCondition implements ConditionInterface
{
    public function __construct(
        private readonly string|ExpressionInterface $column,
        private readonly mixed $minValue,
        private readonly mixed $maxValue,
        private readonly bool $not = false
    ) {
    }

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /** @var self $self */
        return $self->not
            ? $exprBuilder->notBetween($self->column, $self->minValue, $self->maxValue)
            : $exprBuilder->between($self->column, $self->minValue, $self->maxValue);
    }

    /**
     * @param array{0: string, 1: string|ExpressionInterface, 2: mixed, 3: mixed} $arr
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

        [$name, $column, $minValue, $maxValue] = $arr;

        try {
            return new static($column, $minValue, $maxValue, str_contains($name, 'not'));
        } catch (TypeError $e) {
            throw new InvalidExpressionFormatException(previous: $e);
        }
    }
}
