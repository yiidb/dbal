<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\SelectQuery\JoinInterface;
use YiiDb\DBAL\Contracts\Query\SelectQueryInterface;
use YiiDb\DBAL\Query\Expressions\Conditions\BetweenCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\ColumnSimpleCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\ExistsCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\SimpleCondition;
use YiiDb\DBAL\Query\Expressions\Operator;

/**
 * @psalm-require-implements JoinInterface
 * @see JoinInterface
 */
trait JoinOnHelpersTrait
{
    abstract public function andOn(
        string|ExpressionInterface|ConditionInterface|array $on,
        array $params = null
    ): static;

    public function andOnColumn(string $columnLeft, string $operator, string $columnRight): static
    {
        return $this->andOn(new ColumnSimpleCondition($columnLeft, $operator, $columnRight));
    }

    public function andOnColumnEQ(string $columnLeft, string $columnRight): static
    {
        return $this->andOn(new ColumnSimpleCondition($columnLeft, Operator::EQ, $columnRight));
    }

    public function andOnExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->andOn(new ExistsCondition($query));
    }

    public function andOnNotExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->andOn(new ExistsCondition($query, true));
    }

    public function andOnNotNull(string|ExpressionInterface $column): static
    {
        return $this->andOn(new SimpleCondition($column, Operator::NEQ, null));
    }

    public function andOnNull(string|ExpressionInterface $column): static
    {
        return $this->andOn(new SimpleCondition($column, Operator::EQ, null));
    }

    public function andOnValue(string|ExpressionInterface $column, string $operator, mixed $value): static
    {
        return $this->andOn(new SimpleCondition($column, $operator, $value));
    }

    public function andOnValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->andOn(new BetweenCondition($column, $minValue, $maxValue));
    }

    public function andOnValueEQ(string|ExpressionInterface $column, mixed $value): static
    {
        return $this->andOn(new SimpleCondition($column, Operator::EQ, $value));
    }

    public function andOnValueIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->andOn(new SimpleCondition($column, Operator::IN, $values));
    }

    public function andOnValueNotBetween(
        string|ExpressionInterface $column,
        mixed $minValue,
        mixed $maxValue
    ): static {
        return $this->andOn(new BetweenCondition($column, $minValue, $maxValue, true));
    }

    public function andOnValueNotIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->andOn(new SimpleCondition($column, Operator::NOT_IN, $values));
    }

    abstract public function on(
        string|ExpressionInterface|ConditionInterface|array|null $on,
        array $params = null
    ): static;

    public function onColumn(string $columnLeft, string $operator, string $columnRight): static
    {
        return $this->on(new ColumnSimpleCondition($columnLeft, $operator, $columnRight));
    }

    public function onColumnEQ(string $columnLeft, string $columnRight): static
    {
        return $this->on(new ColumnSimpleCondition($columnLeft, Operator::EQ, $columnRight));
    }

    public function onExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->on(new ExistsCondition($query));
    }

    public function onNotExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->on(new ExistsCondition($query, true));
    }

    public function onNotNull(string|ExpressionInterface $column): static
    {
        return $this->on(new SimpleCondition($column, Operator::NEQ, null));
    }

    public function onNull(string|ExpressionInterface $column): static
    {
        return $this->on(new SimpleCondition($column, Operator::EQ, null));
    }

    public function onValue(string|ExpressionInterface $column, string $operator, mixed $value): static
    {
        return $this->on(new SimpleCondition($column, $operator, $value));
    }

    public function onValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->on(new BetweenCondition($column, $minValue, $maxValue));
    }

    public function onValueEQ(string|ExpressionInterface $column, mixed $value): static
    {
        return $this->on(new SimpleCondition($column, Operator::EQ, $value));
    }

    public function onValueIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->on(new SimpleCondition($column, Operator::IN, $values));
    }

    public function onValueNotBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->on(new BetweenCondition($column, $minValue, $maxValue, true));
    }

    public function onValueNotIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->on(new SimpleCondition($column, Operator::NOT_IN, $values));
    }

    abstract public function orOn(
        string|ExpressionInterface|ConditionInterface|array $on,
        array $params = null
    ): static;

    public function orOnColumn(string $columnLeft, string $operator, string $columnRight): static
    {
        return $this->orOn(new ColumnSimpleCondition($columnLeft, $operator, $columnRight));
    }

    public function orOnColumnEQ(string $columnLeft, string $columnRight): static
    {
        return $this->orOn(new ColumnSimpleCondition($columnLeft, Operator::EQ, $columnRight));
    }

    public function orOnExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->orOn(new ExistsCondition($query));
    }

    public function orOnNotExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->orOn(new ExistsCondition($query, true));
    }

    public function orOnNotNull(string|ExpressionInterface $column): static
    {
        return $this->orOn(new SimpleCondition($column, Operator::NEQ, null));
    }

    public function orOnNull(string|ExpressionInterface $column): static
    {
        return $this->orOn(new SimpleCondition($column, Operator::EQ, null));
    }

    public function orOnValue(string|ExpressionInterface $column, string $operator, mixed $value): static
    {
        return $this->andOn(new SimpleCondition($column, $operator, $value));
    }

    public function orOnValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->orOn(new BetweenCondition($column, $minValue, $maxValue));
    }

    public function orOnValueEQ(string|ExpressionInterface $column, mixed $value): static
    {
        return $this->orOn(new SimpleCondition($column, Operator::EQ, $value));
    }

    public function orOnValueIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->orOn(new SimpleCondition($column, Operator::IN, $values));
    }

    public function orOnValueNotBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->orOn(new BetweenCondition($column, $minValue, $maxValue, true));
    }

    public function orOnValueNotIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->orOn(new SimpleCondition($column, Operator::NOT_IN, $values));
    }
}
