<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\SelectQuery;

use YiiDb\DBAL\Contracts\ConnectionInterface;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryInterface;
use YiiDb\DBAL\Contracts\Query\SelectQuery\JoinInterface;
use YiiDb\DBAL\Contracts\Query\SelectQueryInterface;
use YiiDb\DBAL\Exceptions\InvalidArgumentException;
use YiiDb\DBAL\Query\Expressions\Expression;
use YiiDb\DBAL\Query\SQLComposer;
use YiiDb\DBAL\Query\TableName;

use function is_array;
use function is_string;

/**
 * @psalm-type From = string|TableName|ExpressionInterface|SelectQueryInterface
 * @psalm-require-implements \YiiDb\DBAL\Contracts\Query\SelectQueryInterface
 */
trait FromTrait
{
    /**
     * @var array<string|TableName|ExpressionInterface>
     */
    private array $from = [];
    /**
     * @var list<Join>
     */
    private array $joins = [];
    /**
     * @var array<string, list<Join>>
     */
    private array $refJoins = [];

    abstract public function clearBuiltExpr(): void;

    final public function crossJoin(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        ExpressionInterface|array|string $where = null,
        callable $func = null,
        string $ref = null
    ): static {
        $join = $this->crossJoinEx($table, $alias, $ref);
        if (!empty($where)) {
            $join->where($where);
        }
        if ($func !== null) {
            $func($join);
        }

        return $this;
    }

    final public function crossJoinEx(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface {
        return $this->makeJoin(Join::TYPE_CROSS, $table, $alias, $ref);
    }

    /**
     * @psalm-param From|From[] ...$from
     * @return $this
     */
    final public function from(string|TableName|ExpressionInterface|SelectQueryInterface|array ...$from): static
    {
        $this->clearBuiltExpr();

        $this->fromEx($from);

        return $this;
    }

    final public function fromWithAlias(
        string|TableName|ExpressionInterface|SelectQueryInterface $from,
        string $alias
    ): static {
        $this->clearBuiltExpr();

        if ($from instanceof SelectQueryInterface) {
            $from = $from->toExpr();
        }

        $this->from[$alias] = $from;

        return $this;
    }

    final public function fullJoin(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        ExpressionInterface|array|string $on = null,
        ExpressionInterface|array|string $where = null,
        callable $func = null,
        string $ref = null
    ): static {
        $join = $this->fullJoinEx($table, $alias, $ref);
        if (!empty($on)) {
            $join->on($on);
        }
        if (!empty($where)) {
            $join->where($where);
        }
        if ($func !== null) {
            $func($join);
        }

        return $this;
    }

    final public function fullJoinEx(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface {
        return $this->makeJoin(Join::TYPE_FULL, $table, $alias, $ref);
    }

    abstract public function getConditionBuilder(): ConditionBuilderInterface;

    abstract public function getConnection(): ?ConnectionInterface;

    public function join(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        ExpressionInterface|array|string $on = null,
        ExpressionInterface|array|string $where = null,
        callable $func = null,
        string $ref = null
    ): static {
        $join = $this->joinEx($table, $alias, $ref);
        if (!empty($on)) {
            $join->on($on);
        }
        if (!empty($where)) {
            $join->where($where);
        }
        if ($func !== null) {
            $func($join);
        }

        return $this;
    }

    public function joinEx(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface {
        return $this->makeJoin(Join::TYPE_INNER, $table, $alias, $ref);
    }

    public function leftJoin(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        ExpressionInterface|array|string $on = null,
        ExpressionInterface|array|string $where = null,
        callable $func = null,
        string $ref = null
    ): static {
        $join = $this->leftJoinEx($table, $alias, $ref);
        if (!empty($on)) {
            $join->on($on);
        }
        if (!empty($where)) {
            $join->where($where);
        }
        if ($func !== null) {
            $func($join);
        }

        return $this;
    }

    public function leftJoinEx(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface {
        return $this->makeJoin(Join::TYPE_LEFT, $table, $alias, $ref);
    }

    public function rightJoin(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        ExpressionInterface|array|string $on = null,
        ExpressionInterface|array|string $where = null,
        callable $func = null,
        string $ref = null
    ): static {
        $join = $this->rightJoinEx($table, $alias, $ref);
        if (!empty($on)) {
            $join->on($on);
        }
        if (!empty($where)) {
            $join->where($where);
        }
        if ($func !== null) {
            $func($join);
        }

        return $this;
    }

    public function rightJoinEx(
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        string $ref = null
    ): JoinInterface {
        return $this->makeJoin(Join::TYPE_RIGHT, $table, $alias, $ref);
    }

    /**
     * @param list<string|ExpressionInterface> $joinsWhere
     */
    protected function buildFrom(array &$joinsWhere, string $separator): string|Expression|null
    {
        if (empty($this->from)) {
            return null;
        }

        $conn = $this->getConnection();
        $knownRefs = [];

        $composer = new SQLComposer(', ');

        foreach ($this->from as $alias => $from) {
            if (!is_string($alias)) {
                $alias = null;
            }

            $table = new TableItem($from, $alias);

            if (empty($this->refJoins)) {
                $composer->add(TableItem::build($table, $conn));
                continue;
            }

            $ref = $table->getRef();

            $knownRefs[$ref] = null;

            if (!empty($joins = $this->refJoins[$ref] ?? null)) {
                $joinComposer = new SQLComposer(' ');
                $joinComposer->add(TableItem::build($table, $conn));
                $this->buildJoinItems($joins, $joinComposer, $joinsWhere, $knownRefs, $conn);
                $composer->add($joinComposer->compose());
            } else {
                $composer->add(TableItem::build($table, $conn));
            }
        }

        $fromExpr = $composer->compose(true, 'FROM ');

        if (empty($joins = $this->joins)) {
            return $fromExpr;
        }

        $joinComposer = new SQLComposer($separator);
        $joinComposer->add($fromExpr);
        $this->buildJoinItems($joins, $joinComposer, $joinsWhere, $knownRefs, $conn);

        return $joinComposer->compose();
    }

    abstract protected function getParentQuery(): QueryInterface;

    /**
     * @param list<Join> $joins
     * @param list<string|ExpressionInterface> $joinsWhere
     * @param array<string, null> $knownRefs
     */
    private function buildJoinItems(
        array $joins,
        SQLComposer $composer,
        array &$joinsWhere,
        array &$knownRefs,
        ?ConnectionInterface $conn
    ): void {
        foreach ($joins as $join) {
            $composer->add($join->toExpr($conn));
            if (!empty($joinWhere = $join->getWhereExpr())) {
                $joinsWhere[] = $joinWhere;
            }

            if (!empty($this->refJoins)) {
                $ref = $join->getRef();
                $knownRefs[$ref] = null;
                if (!empty($nestedJoins = $this->refJoins[$ref] ?? null)) {
                    $this->buildJoinItems($nestedJoins, $composer, $joinsWhere, $knownRefs, $conn);
                }
            }
        }
    }

    /**
     * @param array<From|From[]> $fromArr
     */
    private function fromEx(array $fromArr): void
    {
        foreach ($fromArr as $alias => $from) {
            if (is_array($from)) {
                if (is_string($alias)) {
                    throw new InvalidArgumentException('It is illegal to use an alias for an array of elements.');
                }

                $this->fromEx($from);
            } else {
                if ($from instanceof SelectQueryInterface) {
                    if (!is_string($alias)) {
                        throw new InvalidArgumentException('Adding an Query to From requires an alias.');
                    }

                    $from = $from->toExpr();
                }

                if (is_string($alias)) {
                    $this->from[$alias] = $from;
                } elseif (($from instanceof TableName) && ($alias = $from->alias)) {
                    $this->from[$alias] = $from;
                } else {
                    if ($from instanceof ExpressionInterface) {
                        throw new InvalidArgumentException('Adding an Expression to From requires an alias.');
                    }

                    $this->from[] = $from;
                }
            }
        }
    }

    private function makeJoin(
        string $type,
        ExpressionInterface|SelectQueryInterface|string $table,
        string $alias = null,
        string $ref = null
    ): Join {
        $join = new Join($type, $table, $alias, $this->getParentQuery(), $this->getConditionBuilder());

        $this->clearBuiltExpr();

        return $ref === null
            ? $this->joins[] = $join
            : $this->refJoins[$ref][] = $join;
    }
}
