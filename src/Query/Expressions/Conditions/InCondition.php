<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use TypeError;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\SelectQueryInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;

use function count;

/**
 * @psalm-immutable
 */
final class InCondition implements ConditionInterface
{
    public function __construct(
        private readonly string $column,
        private readonly array|ExpressionInterface|SelectQueryInterface $values,
        private readonly bool $not = false
    ) {
        empty($values) && throw new InvalidExpressionFormatException(
            'For the "in/not in" condition, an empty array of elements is not allowed.'
        );
    }

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /** @var self $self */
        $values = $self->values;
        if ($values instanceof SelectQueryInterface) {
            $values = $values->toExpr();
        }

        return $self->not
            ? $exprBuilder->notIn($self->column, $values)
            : $exprBuilder->in($self->column, $values);
    }

    /**
     * @param array{0: string, 1: string, 2: array|ExpressionInterface|SelectQueryInterface} $arr
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        if (count($arr) !== 3) {
            throw new InvalidExpressionFormatException();
        }

        [$name, $column, $values] = $arr;

        try {
            return new static($column, $values, str_contains($name, 'not'));
        } catch (TypeError $e) {
            throw new InvalidExpressionFormatException(previous: $e);
        }
    }

    public function __debugInfo(): array
    {
        return ['not' => $this->not, ...$this->values];
    }
}
