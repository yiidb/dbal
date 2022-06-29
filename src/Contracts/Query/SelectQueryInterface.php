<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\SelectQuery\JoinInterface;
use YiiDb\DBAL\Contracts\ResultInterface;

interface SelectQueryInterface extends BaseQueryInterface, QueryWithWhereInterface
{
    /**
     * @psalm-param string|ExpressionInterface|array<string|ExpressionInterface> ...$columns
     * @return $this
     */
    public function addSelect(string|ExpressionInterface|array ...$columns): static;

    /**
     * @return $this
     */
    public function addSelectAvg(string|ExpressionInterface $column, bool $distinct = false): static;

    /**
     * @return $this
     */
    public function addSelectCount(string|ExpressionInterface $column = '*', bool $distinct = false): static;

    /**
     * @param string|string[]|ExpressionInterface $columns
     * @return $this
     */
    public function addSelectCountDistinct(string|array|ExpressionInterface $columns): static;

    /**
     * @return $this
     */
    public function addSelectMax(string|ExpressionInterface $column, bool $distinct = false): static;

    /**
     * @return $this
     */
    public function addSelectMin(string|ExpressionInterface $column, bool $distinct = false): static;

    /**
     * @return $this
     */
    public function addSelectRaw(string $rawString, array $params = [], bool $wrap = true): static;

    /**
     * @return $this
     */
    public function addSelectString(string $columns): static;

    /**
     * @return $this
     */
    public function addSelectSum(string|ExpressionInterface $column, bool $distinct = false): static;

    /**
     * @return $this
     */
    public function byId(int|string $id): static;

    /**
     * @psalm-param (callable(JoinInterface):void)|null $func
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
    public function distinct(bool $value = true): static;

    public function doesntExist(): bool;

    public function exists(): bool;

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

    public function get(): ResultInterface;

    public function getAggregateAvg(string|ExpressionInterface $column, bool $distinct = false): mixed;

    public function getAggregateCount(string $column = null, bool $distinct = false): int;

    /**
     * @param string|string[]|ExpressionInterface $columns
     */
    public function getAggregateCountDistinct(string|array|ExpressionInterface $columns): int;

    public function getAggregateMax(string $column, bool $distinct = false): mixed;

    public function getAggregateMin(string $column, bool $distinct = false): mixed;

    public function getAggregateSum(string $column, bool $distinct = false): mixed;

    /**
     * @return array<array>
     */
    public function getAll(): array;

    /**
     * @return array<list<mixed>>
     */
    public function getAllNumeric(): array;

    /**
     * @return array<object>
     */
    public function getAllObject(): array;

    public function getColumn(string $column = null): array;

    /**
     * Alias for getAggregateCount method.
     *
     * @see getAggregateCount()
     */
    public function getCount(): int;

    public function getFirst(): ?array;

    /**
     * @return list<mixed>|null
     */
    public function getFirstNumeric(): ?array;

    public function getFirstObject(): ?object;

    public function getIndexColumn(): int|string|null;

    public function getLimit(): ?int;

    public function getOffset(): ?int;

    /**
     * @throw MoreRowsReceivedException
     */
    public function getOne(): ?array;

    /**
     * @return list<mixed>|null
     * @throw MoreRowsReceivedException
     */
    public function getOneNumeric(): ?array;

    /**
     * @throw MoreRowsReceivedException
     */
    public function getOneObject(): ?object;

    public function getValue(string $column = null): mixed;

    /**
     * @return $this
     */
    public function join(
        string|ExpressionInterface|SelectQueryInterface $table,
        string $alias = null,
        string|ExpressionInterface|array $on = null,
        string|ExpressionInterface|array $where = null,
        callable $func = null,
        string $ref = null
    ): static;

    public function joinEx(
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
    ): JoinInterface;

    /**
     * @psalm-param string|ExpressionInterface|array<string|ExpressionInterface> ...$columns
     * @return $this
     */
    public function select(string|ExpressionInterface|array ...$columns): static;

    /**
     * @return $this
     */
    public function selectAvg(string|ExpressionInterface $column, bool $distinct = false): static;

    /**
     * @return $this
     */
    public function selectCount(string|ExpressionInterface $column = '*', bool $distinct = false): static;

    /**
     * @param string|string[]|ExpressionInterface $columns
     * @return $this
     */
    public function selectCountDistinct(string|array|ExpressionInterface $columns): static;

    /**
     * @return $this
     */
    public function selectMax(string|ExpressionInterface $column, bool $distinct = false): static;

    /**
     * @return $this
     */
    public function selectMin(string|ExpressionInterface $column, bool $distinct = false): static;

    /**
     * @return $this
     */
    public function selectRaw(string $rawString, array $params = [], bool $wrap = true): static;

    /**
     * @return $this
     */
    public function selectString(string $columns): static;

    /**
     * @return $this
     */
    public function selectSum(string|ExpressionInterface $column, bool $distinct = false): static;

    /**
     * @return $this
     */
    public function setIndexColumn(int|string|null $column): static;

    /**
     * @return $this
     */
    public function setLimit(?int $value): static;

    /**
     * @return $this
     */
    public function setOffset(?int $value): static;

    /**
     * @return $this
     */
    public function skip(int $count): static;

    /**
     * @return $this
     */
    public function take(int $count): static;

    /**
     * @return $this
     */
    public function union(SelectQueryInterface|ExpressionInterface $query): static;
}
