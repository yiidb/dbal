<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts;

use Closure;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryInterface;
use YiiDb\DBAL\Query\TableName;

interface ConnectionInterface
{
    /**
     * @param array<string|int> $columns
     * @param array[] $rows
     */
    public function batchInsert(string|TableName $table, array $columns, array $rows): int|string;

    public function beginTransaction(): void;

    public function commit(): void;

    public function createCommand(string|ExpressionInterface $sql): CommandInterface;

    public function createSavepoint(string $savepoint): void;

    public function delete(string|TableName $table, array $criteria, bool $allowEmptyCriteria = false): int|string;

    public function executeQuery(string $sql, array $params = null): ResultInterface;

    public function executeStatement(string $sql, array $params = null): int|string;

    public function getConditionBuilder(): ConditionBuilderInterface;

    public function getDatabase(): ?string;

    public function getLastInsertId(): int|string;

    public function getTransactionIsolation(): int;

    public function getTransactionNestingLevel(): int;

    public function insert(string|TableName $table, array $values): int|string;

    public function isAutoCommit(): bool;

    public function isRollbackOnly(): bool;

    public function isTransactionActive(): bool;

    public function isUseNamedParams(): bool;

    public function query(bool $useNamedParams = null, bool $inlineParams = null): QueryInterface;

    public function quote(string $value): string;

    public function releaseSavepoint(string $savepoint): void;

    public function rollBack(): void;

    public function rollbackSavepoint(string $savepoint): void;

    public function select(string $sql, array $params = null): ResultInterface;

    public function setAutoCommit(bool $autoCommit): void;

    public function setRollbackOnly(): void;

    /**
     * Executes a function in a transaction.
     *
     * The function gets passed this Connection instance as an (optional) parameter.
     *
     * If an exception occurs during execution of the function or transaction commit,
     * the transaction is rolled back and the exception re-thrown.
     *
     * @template T
     * @param Closure(self):T $func The function to execute transactionally.
     * @return T The value returned by $func
     */
    public function transactional(Closure $func): mixed;

    public function update(
        string|TableName $table,
        array $values,
        array $criteria,
        bool $allowEmptyCriteria = false
    ): int|string;

    public function wrap(string $value): string;

    public function wrapSingle(string $value): string;

    public function wrapSql(string $sql): string;
}
