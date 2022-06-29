<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Query\Expressions\Operator;

interface QueryWithWhereInterface
{
    /**
     * @return $this
     */
    public function andWhere(string|ExpressionInterface|ConditionInterface|array $where, array $params = null): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function andWhereColumn(string $columnLeft, string $operator, string $columnRight): static;

    /**
     * @return $this
     */
    public function andWhereColumnEQ(string $columnLeft, string $columnRight): static;

    /**
     * @return $this
     */
    public function andWhereExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function andWhereNotExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function andWhereNotNull(string|ExpressionInterface $column): static;

    /**
     * @return $this
     */
    public function andWhereNull(string|ExpressionInterface $column): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function andWhereValue(string|ExpressionInterface $column, string $operator, mixed $value): static;

    /**
     * @return $this
     */
    public function andWhereValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static;

    /**
     * @return $this
     */
    public function andWhereValueEQ(string|ExpressionInterface $column, mixed $value): static;

    /**
     * @return $this
     */
    public function andWhereValueIn(string|ExpressionInterface $column, array $values): static;

    /**
     * @return $this
     */
    public function andWhereValueNotBetween(
        string|ExpressionInterface $column,
        mixed $minValue,
        mixed $maxValue
    ): static;

    /**
     * @return $this
     */
    public function andWhereValueNotIn(string|ExpressionInterface $column, array $values): static;

    public function expr(): ExpressionBuilderInterface;

    public function getConditionBuilder(): ConditionBuilderInterface;

    /**
     * @return $this
     */
    public function orWhere(string|ExpressionInterface|ConditionInterface|array $where, array $params = null): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function orWhereColumn(string $columnLeft, string $operator, string $columnRight): static;

    /**
     * @return $this
     */
    public function orWhereColumnEQ(string $columnLeft, string $columnRight): static;

    /**
     * @return $this
     */
    public function orWhereExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function orWhereNotExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function orWhereNotNull(string|ExpressionInterface $column): static;

    /**
     * @return $this
     */
    public function orWhereNull(string|ExpressionInterface $column): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function orWhereValue(string|ExpressionInterface $column, string $operator, mixed $value): static;

    /**
     * @return $this
     */
    public function orWhereValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static;

    /**
     * @return $this
     */
    public function orWhereValueEQ(string|ExpressionInterface $column, mixed $value): static;

    /**
     * @return $this
     */
    public function orWhereValueIn(string|ExpressionInterface $column, array $values): static;

    /**
     * @return $this
     */
    public function orWhereValueNotBetween(
        string|ExpressionInterface $column,
        mixed $minValue,
        mixed $maxValue
    ): static;

    /**
     * @return $this
     */
    public function orWhereValueNotIn(string|ExpressionInterface $column, array $values): static;

    /**
     * @return $this
     */
    public function where(
        string|ExpressionInterface|ConditionInterface|array|null $where,
        array $params = null
    ): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function whereColumn(string $columnLeft, string $operator, string $columnRight): static;

    /**
     * @return $this
     */
    public function whereColumnEQ(string $columnLeft, string $columnRight): static;

    /**
     * @return $this
     */
    public function whereExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function whereNotExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function whereNotNull(string|ExpressionInterface $column): static;

    /**
     * @return $this
     */
    public function whereNull(string|ExpressionInterface $column): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function whereValue(string|ExpressionInterface $column, string $operator, mixed $value): static;

    /**
     * @return $this
     */
    public function whereValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static;

    /**
     * @return $this
     */
    public function whereValueEQ(string|ExpressionInterface $column, mixed $value): static;

    /**
     * @return $this
     */
    public function whereValueIn(string|ExpressionInterface $column, array $values): static;

    /**
     * @return $this
     */
    public function whereValueNotBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static;

    /**
     * @return $this
     */
    public function whereValueNotIn(string|ExpressionInterface $column, array $values): static;
}
