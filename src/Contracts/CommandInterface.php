<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts;

use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;

interface CommandInterface
{
    /**
     * @return $this
     */
    public function addParam(mixed $value): static;

    /**
     * @return $this
     */
    public function addParams(array $values): static;

    public function executeQuery(): ResultInterface;

    public function executeStatement(): int|string;

    public function getConnection(): ?ConnectionInterface;

    public function getParam(): array;

    public function getRealConnection(): ConnectionInterface;

    public function getSql(): string;

    /**
     * @return $this
     */
    public function setParam(string|int $name, mixed $value): static;

    /**
     * @return $this
     */
    public function setParams(array $values): static;

    /**
     * @return $this
     */
    public function setSql(string|ExpressionInterface $sql): static;

    public function toExpr(ConnectionInterface $conn = null): ExpressionInterface;

    public function withConnection(ConnectionInterface $conn = null): static;
}
