<?php

declare(strict_types=1);

namespace Yii\DBAL\Query\SelectQuery;

use Yii\DBAL\Contracts\ConnectionInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use Yii\DBAL\Query\Expressions\Expression;
use Yii\DBAL\Query\TableName;

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
