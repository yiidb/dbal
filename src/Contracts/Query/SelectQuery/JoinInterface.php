<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query\SelectQuery;

use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryWithWhereInterface;
use YiiDb\DBAL\Contracts\Query\SelectQueryInterface;
use YiiDb\DBAL\Query\Expressions\Operator;

interface JoinInterface extends QueryWithWhereInterface
{
    /**
     * @return $this
     */
    public function andOn(string|ExpressionInterface|ConditionInterface|array $on, array $params = null): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function andOnColumn(string $columnLeft, string $operator, string $columnRight): static;

    /**
     * @return $this
     */
    public function andOnColumnEQ(string $columnLeft, string $columnRight): static;

    /**
     * @return $this
     */
    public function andOnExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function andOnNotExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function andOnNotNull(string|ExpressionInterface $column): static;

    /**
     * @return $this
     */
    public function andOnNull(string|ExpressionInterface $column): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function andOnValue(string|ExpressionInterface $column, string $operator, mixed $value): static;

    /**
     * @return $this
     */
    public function andOnValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static;

    /**
     * @return $this
     */
    public function andOnValueEQ(string|ExpressionInterface $column, mixed $value): static;

    /**
     * @return $this
     */
    public function andOnValueIn(string|ExpressionInterface $column, array $values): static;

    /**
     * @return $this
     */
    public function andOnValueNotBetween(
        string|ExpressionInterface $column,
        mixed $minValue,
        mixed $maxValue
    ): static;

    /**
     * @return $this
     */
    public function andOnValueNotIn(string|ExpressionInterface $column, array $values): static;

    public function expr(): ExpressionBuilderInterface;

    public function getConditionBuilder(): ConditionBuilderInterface;

    /**
     * @return $this
     */
    public function on(string|ExpressionInterface|ConditionInterface|array|null $on, array $params = null): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function onColumn(string $columnLeft, string $operator, string $columnRight): static;

    /**
     * @return $this
     */
    public function onColumnEQ(string $columnLeft, string $columnRight): static;

    /**
     * @return $this
     */
    public function onExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function onNotExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function onNotNull(string|ExpressionInterface $column): static;

    /**
     * @return $this
     */
    public function onNull(string|ExpressionInterface $column): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function onValue(string|ExpressionInterface $column, string $operator, mixed $value): static;

    /**
     * @return $this
     */
    public function onValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static;

    /**
     * @return $this
     */
    public function onValueEQ(string|ExpressionInterface $column, mixed $value): static;

    /**
     * @return $this
     */
    public function onValueIn(string|ExpressionInterface $column, array $values): static;

    /**
     * @return $this
     */
    public function onValueNotBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static;

    /**
     * @return $this
     */
    public function onValueNotIn(string|ExpressionInterface $column, array $values): static;

    /**
     * @return $this
     */
    public function orOn(string|ExpressionInterface|ConditionInterface|array $on, array $params = null): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function orOnColumn(string $columnLeft, string $operator, string $columnRight): static;

    /**
     * @return $this
     */
    public function orOnColumnEQ(string $columnLeft, string $columnRight): static;

    /**
     * @return $this
     */
    public function orOnExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function orOnNotExists(SelectQueryInterface|ExpressionInterface $query): static;

    /**
     * @return $this
     */
    public function orOnNotNull(string|ExpressionInterface $column): static;

    /**
     * @return $this
     */
    public function orOnNull(string|ExpressionInterface $column): static;

    /**
     * @psalm-param Operator::* $operator
     * @return $this
     */
    public function orOnValue(string|ExpressionInterface $column, string $operator, mixed $value): static;

    /**
     * @return $this
     */
    public function orOnValueBetween(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): static;

    /**
     * @return $this
     */
    public function orOnValueEQ(string|ExpressionInterface $column, mixed $value): static;

    /**
     * @return $this
     */
    public function orOnValueIn(string|ExpressionInterface $column, array $values): static;

    /**
     * @return $this
     */
    public function orOnValueNotBetween(
        string|ExpressionInterface $column,
        mixed $minValue,
        mixed $maxValue
    ): static;

    /**
     * @return $this
     */
    public function orOnValueNotIn(string|ExpressionInterface $column, array $values): static;
}
