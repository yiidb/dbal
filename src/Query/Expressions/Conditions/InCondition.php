<?php

declare(strict_types=1);

namespace Yii\DBAL\Query\Expressions\Conditions;

use TypeError;
use Yii\DBAL\Contracts\Query\ConditionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ConditionInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use Yii\DBAL\Contracts\Query\SelectQueryInterface;
use Yii\DBAL\Exceptions\InvalidExpressionFormatException;

use function count;

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

    public function __debugInfo(): array
    {
        return ['not' => $this->not, ...$this->values];
    }

    public function build(ExpressionBuilderInterface $exprBuilder): ExpressionInterface
    {
        $values = $this->values;
        if ($values instanceof SelectQueryInterface) {
            $values = $values->toExpr();
        }

        return $this->not
            ? $exprBuilder->notIn($this->column, $values)
            : $exprBuilder->in($this->column, $values);
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
}
