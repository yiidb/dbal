<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\SelectQuery;

use YiiDb\DBAL\Contracts\ConnectionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Query\Expressions\Expression;
use YiiDb\DBAL\Query\TableName;

use function is_string;

/**
 * @internal
 * @psalm-immutable
 */
final class TableItem
{
    public function __construct(
        public readonly string|TableName|ExpressionInterface $table,
        public readonly ?string $alias
    ) {
    }

    public static function build(self $tableItem, ?ConnectionInterface $conn): string|ExpressionInterface
    {
        $alias = $tableItem->alias;
        $table = $tableItem->table;

        if ($alias === null || $alias === $table) {
            /** @var string|TableName $table */
            return is_string($table) ? $conn?->wrap($table) ?? $table : TableName::getQuoted($table, $conn);
        }

        $alias = $conn?->wrapSingle($alias) ?? $alias;

        if ($table instanceof ExpressionInterface) {
            return new Expression("($table) $alias", $table->getParams());
        }

        $table = is_string($table)
            ? $conn?->wrapSingle($table) ?? $table
            : TableName::getQuoted($table, $conn);

        return $alias === $table ? $table : "$table $alias";
    }

    public function getRef(): string
    {
        if (!is_string($ref = $this->alias)) {
            /** @var string|TableName $table */
            $table = $this->table;
            $ref = is_string($table) ? $table : $table->getRef();
        }

        return $ref;
    }
}
