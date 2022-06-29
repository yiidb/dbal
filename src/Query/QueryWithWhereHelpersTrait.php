<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryWithWhereInterface;
use YiiDb\DBAL\Contracts\Query\SelectQueryInterface;
use YiiDb\DBAL\Query\Expressions\Conditions\BetweenCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\ColumnSimpleCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\ExistsCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\SimpleCondition;
use YiiDb\DBAL\Query\Expressions\Operator;

/**
 * @psalm-require-implements QueryWithWhereInterface
 * @see QueryWithWhereInterface
 */
trait QueryWithWhereHelpersTrait
{
    abstract public function andWhere(
        string|ExpressionInterface|ConditionInterface|array $where,
        array $params = null
    ): static;

    public function andWhereColumn(string $columnLeft, string $operator, string $columnRight): static
    {
        return $this->andWhere(new ColumnSimpleCondition($columnLeft, $operator, $columnRight));
    }

    public function andWhereColumnEQ(string $columnLeft, string $columnRight): static
    {
        return $this->andWhere(new ColumnSimpleCondition($columnLeft, Operator::EQ, $columnRight));
    }

    public function andWhereExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->andWhere(new ExistsCondition($query));
    }

    public function andWhereNotExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->andWhere(new ExistsCondition($query, true));
    }

    public function andWhereNotNull(string|ExpressionInterface $column): static
    {
        return $this->andWhere(new SimpleCondition($column, Operator::NEQ, null));
    }

    public function andWhereNull(string|ExpressionInterface $column): static
    {
        return $this->andWhere(new SimpleCondition($column, Operator::EQ, null));
    }

    public function andWhereValue(string|ExpressionInterface $column, string $operator, mixed $value): static
    {
        return $this->andWhere(new SimpleCondition($column, $operator, $value));
    }

    public function andWhereValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->andWhere(new BetweenCondition($column, $minValue, $maxValue));
    }

    public function andWhereValueEQ(string|ExpressionInterface $column, mixed $value): static
    {
        return $this->andWhere(new SimpleCondition($column, Operator::EQ, $value));
    }

    public function andWhereValueIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->andWhere(new SimpleCondition($column, Operator::IN, $values));
    }

    public function andWhereValueNotBetween(
        string|ExpressionInterface $column,
        mixed $minValue,
        mixed $maxValue
    ): static {
        return $this->andWhere(new BetweenCondition($column, $minValue, $maxValue, true));
    }

    public function andWhereValueNotIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->andWhere(new SimpleCondition($column, Operator::NOT_IN, $values));
    }

    abstract public function orWhere(
        string|ExpressionInterface|ConditionInterface|array $where,
        array $params = null
    ): static;

    public function orWhereColumn(string $columnLeft, string $operator, string $columnRight): static
    {
        return $this->orWhere(new ColumnSimpleCondition($columnLeft, $operator, $columnRight));
    }

    public function orWhereColumnEQ(string $columnLeft, string $columnRight): static
    {
        return $this->orWhere(new ColumnSimpleCondition($columnLeft, Operator::EQ, $columnRight));
    }

    public function orWhereExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->orWhere(new ExistsCondition($query));
    }

    public function orWhereNotExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->orWhere(new ExistsCondition($query, true));
    }

    public function orWhereNotNull(string|ExpressionInterface $column): static
    {
        return $this->orWhere(new SimpleCondition($column, Operator::NEQ, null));
    }

    public function orWhereNull(string|ExpressionInterface $column): static
    {
        return $this->orWhere(new SimpleCondition($column, Operator::EQ, null));
    }

    public function orWhereValue(string|ExpressionInterface $column, string $operator, mixed $value): static
    {
        return $this->andWhere(new SimpleCondition($column, $operator, $value));
    }

    public function orWhereValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->orWhere(new BetweenCondition($column, $minValue, $maxValue));
    }

    public function orWhereValueEQ(string|ExpressionInterface $column, mixed $value): static
    {
        return $this->orWhere(new SimpleCondition($column, Operator::EQ, $value));
    }

    public function orWhereValueIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->orWhere(new SimpleCondition($column, Operator::IN, $values));
    }

    public function orWhereValueNotBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->orWhere(new BetweenCondition($column, $minValue, $maxValue, true));
    }

    public function orWhereValueNotIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->orWhere(new SimpleCondition($column, Operator::NOT_IN, $values));
    }

    abstract public function where(
        string|ExpressionInterface|ConditionInterface|array|null $where,
        array $params = null
    ): static;

    public function whereColumn(string $columnLeft, string $operator, string $columnRight): static
    {
        return $this->where(new ColumnSimpleCondition($columnLeft, $operator, $columnRight));
    }

    public function whereColumnEQ(string $columnLeft, string $columnRight): static
    {
        return $this->where(new ColumnSimpleCondition($columnLeft, Operator::EQ, $columnRight));
    }

    public function whereExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->where(new ExistsCondition($query));
    }

    public function whereNotExists(ExpressionInterface|SelectQueryInterface $query): static
    {
        return $this->where(new ExistsCondition($query, true));
    }

    public function whereNotNull(string|ExpressionInterface $column): static
    {
        return $this->where(new SimpleCondition($column, Operator::NEQ, null));
    }

    public function whereNull(string|ExpressionInterface $column): static
    {
        return $this->where(new SimpleCondition($column, Operator::EQ, null));
    }

    public function whereValue(string|ExpressionInterface $column, string $operator, mixed $value): static
    {
        return $this->where(new SimpleCondition($column, $operator, $value));
    }

    public function whereValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->where(new BetweenCondition($column, $minValue, $maxValue));
    }

    public function whereValueEQ(string|ExpressionInterface $column, mixed $value): static
    {
        return $this->where(new SimpleCondition($column, Operator::EQ, $value));
    }

    public function whereValueIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->where(new SimpleCondition($column, Operator::IN, $values));
    }

    public function whereValueNotBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static
    {
        return $this->where(new BetweenCondition($column, $minValue, $maxValue, true));
    }

    public function whereValueNotIn(string|ExpressionInterface $column, array $values): static
    {
        return $this->where(new SimpleCondition($column, Operator::NOT_IN, $values));
    }
}
