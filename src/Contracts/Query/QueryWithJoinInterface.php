<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\SelectQuery\JoinInterface;

interface QueryWithJoinInterface
{
    /**
     * @return $this
     */
    public function crossJoin(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string|ExpressionInterface|array $where = null,
        callable $func = null,
        string $ref = null
    ): static;

    public function crossJoinEx(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface;

    /**
     * @return $this
     */
    public function fullJoin(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string|ExpressionInterface|array $on = null,
        string|ExpressionInterface|array $where = null,
        callable $func = null,
        string $ref = null
    ): static;

    public function fullJoinEx(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface;

    /**
     * @return $this
     */
    public function innerJoin(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string|ExpressionInterface|array $on = null,
        string|ExpressionInterface|array $where = null,
        callable $func = null,
        string $ref = null
    ): static;

    public function innerJoinEx(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface;

    /**
     * @return $this
     */
    public function join(
        string $type,
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string|ExpressionInterface|array $on = null,
        string|ExpressionInterface|array $where = null,
        callable $func = null,
        string $ref = null
    ): static;

    public function joinEx(
        string $type,
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface;

    /**
     * @return $this
     */
    public function leftJoin(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string|ExpressionInterface|array $on = null,
        string|ExpressionInterface|array $where = null,
        callable $func = null,
        string $ref = null
    ): static;

    public function leftJoinEx(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface;

    /**
     * @return $this
     */
    public function rightJoin(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string|ExpressionInterface|array $on = null,
        string|ExpressionInterface|array $where = null,
        callable $func = null,
        string $ref = null
    ): static;

    public function rightJoinEx(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string $ref = null
    ): static;
}
