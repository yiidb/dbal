<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

use YiiDb\DBAL\Contracts\ConnectionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Query\TableName;

interface QueryInterface
{
    public function delete(string|TableName|ExpressionInterface $table): DeleteQueryInterface;

    public function expr(): ExpressionBuilderInterface;

    public function getConditionBuilder(): ConditionBuilderInterface;

    public function getConnection(): ?ConnectionInterface;

    public function insert(string|TableName $table): InsertQueryInterface;

    /**
     * @psalm-param string|ExpressionInterface|array<string|ExpressionInterface> ...$columns
     */
    public function select(string|ExpressionInterface|array ...$columns): SelectQueryInterface;

    public function selectRaw(string $rawString, array $params = [], bool $wrap = true): SelectQueryInterface;

    public function selectString(string $columns): SelectQueryInterface;

    public function update(string|TableName $table): UpdateQueryInterface;
}
