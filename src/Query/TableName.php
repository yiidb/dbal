<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query;

use Stringable;
use YiiDb\DBAL\Contracts\ConnectionInterface;

/**
 * @psalm-immutable
 */
final class TableName implements Stringable
{
    public function __construct(
        public readonly string $table,
        public readonly ?string $database = null,
        public readonly ?string $alias = null
    ) {
    }

    public static function getQuoted(self $tableName, ?ConnectionInterface $conn): string
    {
        $table = $conn?->wrapSingle($tableName->table) ?? $tableName->table;

        if ($tableName->database) {
            $table = ($conn?->wrapSingle($tableName->database) ?? $tableName->database) . ".$table";
        }

        return $table;
    }

    public static function getQuotedRef(self $tableName, ?ConnectionInterface $conn): string
    {
        return $tableName->alias
            ? ($conn?->wrapSingle($tableName->alias) ?? $tableName->alias)
            : self::getQuoted($tableName, $conn);
    }

    public function __toString(): string
    {
        return $this->database ? "$this->database.$this->table" : $this->table;
    }

    public function getRef(): string
    {
        if ($this->alias) {
            return $this->alias;
        }

        return $this->database ? "$this->database.$this->table" : $this->table;
    }

    public function withAlias(string $alias = null): self
    {
        return new self($this->table, $this->database, $alias);
    }
}
