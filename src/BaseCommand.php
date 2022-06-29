<?php

declare(strict_types=1);

namespace YiiDb\DBAL;

use YiiDb\DBAL\Contracts\CommandInterface;
use YiiDb\DBAL\Contracts\CommandTypeEnum;
use YiiDb\DBAL\Contracts\ConnectionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\ResultInterface;
use YiiDb\DBAL\Exceptions\ConnectionRequiredException;
use YiiDb\DBAL\Exceptions\NotSupportedException;
use YiiDb\DBAL\Query\Expressions\Expression;

/**
 * @template TConnection of ConnectionInterface
 */
abstract class BaseCommand implements CommandInterface
{
    /**
     * @var TConnection|null
     */
    protected ?ConnectionInterface $conn;
    protected readonly CommandTypeEnum $type;


    private bool $isExpr = false;
    private array $params = [];
    private string $sql = '';

    /**
     * @param TConnection|null $conn
     */
    public function __construct(
        string|ExpressionInterface $sql = '',
        ConnectionInterface $conn = null,
        CommandTypeEnum $type = CommandTypeEnum::Unknown
    ) {
        $this->setSql($sql);
        $this->conn = $conn;
        $this->type = $type;
    }

    public function addParam(mixed $value): static
    {
        $this->params[] = $value;

        return $this;
    }

    public function addParams(array $values): static
    {
        if ($values) {
            $this->params = [...$this->params, ...$values];
        }

        return $this;
    }

    public function executeQuery(): ResultInterface
    {
        if ($this->type === CommandTypeEnum::Statement) {
            throw new NotSupportedException();
        }

        $conn = $this->getRealConnection();

        return $conn->executeQuery($this->getWrappedSql($conn), $this->getParam());
    }

    public function executeStatement(): int|string
    {
        if ($this->type === CommandTypeEnum::Query) {
            throw new NotSupportedException();
        }

        $conn = $this->getRealConnection();

        return $conn->executeStatement($this->getWrappedSql($conn), $this->params);
    }

    /**
     * @return TConnection|null
     */
    public function getConnection(): ?ConnectionInterface
    {
        return $this->conn;
    }

    public function getParam(): array
    {
        return $this->params;
    }

    /**
     * @return TConnection
     */
    public function getRealConnection(): ConnectionInterface
    {
        return $this->conn ?? throw new ConnectionRequiredException();
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function setParam(string|int $name, mixed $value): static
    {
        $this->params[$name] = $value;

        return $this;
    }

    public function setParams(array $values): static
    {
        $this->params = $values;

        return $this;
    }

    /**
     * @psalm-suppress LessSpecificReturnStatement Fix psalm bug (https://github.com/vimeo/psalm/issues/8253)
     */
    public function setSql(string|ExpressionInterface $sql): static
    {
        $this->sql = (string)$sql;
        if ($this->isExpr = ($sql instanceof ExpressionInterface)) {
            $this->params = $sql->getParams();
        }

        return $this;
    }

    public function toExpr(ConnectionInterface $conn = null): Expression
    {
        return new Expression($this->getWrappedSql($conn ?? $this->getConnection()), $this->params);
    }

    /**
     * @param TConnection|null $conn
     * @psalm-suppress MoreSpecificImplementedParamType Fix psalm bug
     */
    public function withConnection(ConnectionInterface $conn = null): static
    {
        $new = clone $this;
        $new->conn = $conn;

        return $new;
    }

    protected function getWrappedSql(?ConnectionInterface $conn): string
    {
        return $this->isExpr
            ? $this->sql
            : $conn?->wrapSql($this->sql) ?? $this->wrapSql($this->sql);
    }

    private function wrapSql(string $sql): string
    {
        return preg_replace_callback(
            '/(\\{\\{(%?[^{}]+%?)}}|\\[\\[([^[\]]+)]])/',
            static fn (array $matches): string => $matches[3] ?? str_replace('%', '', $matches[2]),
            $sql
        );
    }
}
