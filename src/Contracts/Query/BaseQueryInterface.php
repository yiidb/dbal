<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

use Closure;
use YiiDb\DBAL\Contracts\CommandInterface;
use YiiDb\DBAL\Contracts\ConnectionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;

interface BaseQueryInterface
{
    /**
     * @return $this
     */
    public function addParam(mixed $value): static;

    /**
     * @return $this
     */
    public function addParams(array $values): static;

    public function clearBuiltExpr(): void;

    public function createCommand(): CommandInterface;

    /**
     * @return $this
     */
    public function emulateExecution(bool $value = true): static;

    public function getConnection(): ?ConnectionInterface;

    public function getDebugSql(): string;

    public function getParams(): array;

    public function getRealConnection(): ConnectionInterface;

    public function getSql(): string;

    public function getType(): QueryTypeEnum;

    public function isEmulateExecution(): bool;

    /**
     * @return $this
     */
    public function setParam(string|int $param, mixed $value): static;

    /**
     * @return $this
     */
    public function setParams(array $values): static;

    /**
     * @return $this
     */
    public function setSeparator(string $value): static;

    public function toExpr(): ExpressionInterface;


    /**
     * @template T
     * @param T $condition
     * @param Closure(self, T):mixed $func
     * @return $this
     */
    public function when(mixed $condition, Closure $func): static;
}
