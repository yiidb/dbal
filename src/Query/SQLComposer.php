<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InternalErrorException;
use YiiDb\DBAL\Query\Expressions\Expression;

use function count;
use function is_string;

final class SQLComposer
{
    /**
     * @var array[]
     */
    private array $params = [];
    /**
     * @var string[]
     */
    private array $parts = [];
    private readonly string $separator;

    public function __construct(string $separator)
    {
        $this->separator = $separator;
    }

    public static function composeItems(string $separator, string|ExpressionInterface|null ...$items): string|Expression
    {
        $composer = new SQLComposer($separator);
        foreach ($items as $item) {
            $composer->add($item);
        }

        return $composer->compose(true);
    }

    public function add(string|ExpressionInterface|null $item): void
    {
        if (empty($item)) {
            return;
        }

        if (!empty($sqlString = (string)$item)) {
            $this->parts[] = $sqlString;
        }
        if (!is_string($item) && !empty($params = $item->getParams())) {
            $this->params[] = $params;
        }
    }

    public function addParams(array $params): void
    {
        if ($params) {
            $this->params[] = $params;
        }
    }

    public function addWithPrefix(string $prefix, string|ExpressionInterface|null $item): void
    {
        if (empty($item)) {
            return;
        }

        if (!empty($sqlString = (string)$item)) {
            $this->parts[] = $prefix ? $prefix . $sqlString : $sqlString;
        }
        if (!is_string($item) && !empty($params = $item->getParams())) {
            $this->params[] = $params;
        }
    }

    /**
     * @template T as bool
     * @param T $requiredItems
     * @psalm-return (T is true ? string|Expression : string|Expression|null)
     */
    public function compose(bool $requiredItems = false, string $prefix = null): string|Expression|null
    {
        if (empty($this->parts)) {
            if ($requiredItems) {
                throw new InternalErrorException('No parts in SQLComposer::compose.');
            }

            return null;
        }

        $sql = implode($this->separator, $this->parts);
        if ($prefix) {
            $sql = $prefix . $sql;
        }

        return empty($this->params)
            ? $sql
            : new Expression($sql, array_merge(...$this->params));
    }

    public function toExpr(): Expression
    {
        if (empty($this->parts)) {
            throw new InternalErrorException('No parts in SQLComposer::toExpr.');
        }

        $sql = implode($this->separator, $this->parts);

        $params = $this->params;

        return empty($params)
            ? new Expression($sql)
            : new Expression($sql, count($params) === 1 ? reset($params) : array_merge(...$this->params));
    }
}
