<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query;

use YiiDb\DBAL\Contracts\ConnectionInterface;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryInterface;
use YiiDb\DBAL\Contracts\Query\QueryTypeEnum;
use YiiDb\DBAL\Contracts\Query\SelectQueryInterface;
use YiiDb\DBAL\Exceptions\FromRequiredException;
use YiiDb\DBAL\Exceptions\InvalidArgumentException;
use YiiDb\DBAL\Exceptions\InvalidArgumentTypeException;
use YiiDb\DBAL\Exceptions\MoreRowsReceivedException;
use YiiDb\DBAL\Exceptions\NotSupportedException;
use YiiDb\DBAL\Query\Expressions\BaseExpressionBuilder;
use YiiDb\DBAL\Query\Expressions\Expression;
use YiiDb\DBAL\Query\SelectQuery\FromTrait;

use function count;
use function is_array;
use function is_int;
use function is_string;

abstract class BaseSelectQuery extends BaseQuery implements SelectQueryInterface
{
    use FromTrait;
    use QueryWithWhereTrait;

    protected int|string|null $indexColumn = null;

    private bool $distinct = false;
    private ?ExpressionBuilderInterface $exprBuilder = null;
    private ?int $limit = null;
    private ?int $offset = null;
    private readonly QueryInterface $query;
    /**
     * @var list<string|ExpressionInterface|array<string|ExpressionInterface|array<string|ExpressionInterface>>>
     */
    private array $select;

    /**
     * @param array<string|ExpressionInterface|array<string|ExpressionInterface>> $columns
     */
    public function __construct(QueryInterface $query, array $columns, ConditionBuilderInterface $condBuilder)
    {
        $this->query = $query;
        $this->select = empty($columns) ? [] : [$columns];

        /** @see QueryWithWhereTrait */
        $this->condBuilder = $condBuilder;
    }

    /**
     * @psalm-param string|ExpressionInterface|array<string|ExpressionInterface> ...$columns
     * @return $this
     */
    public function addSelect(string|ExpressionInterface|array ...$columns): static
    {
        if (!empty($columns)) {
            $this->clearBuiltExpr();

            $this->select[] = $columns;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addSelectAvg(string|ExpressionInterface $column, bool $distinct = false): static
    {
        return $distinct
            ? $this->addSelectAggregate('AVG(DISTINCT ?)', $column)
            : $this->addSelectAggregate('AVG(?)', $column);
    }

    /**
     * @return $this
     */
    public function addSelectCount(string|ExpressionInterface $column = null, bool $distinct = false): static
    {
        if (empty($column) || $column === '*') {
            return $this->addSelectRaw('COUNT(*)', wrap: false);
        }

        return $distinct
            ? $this->addSelectAggregate('COUNT(DISTINCT ?)', $column)
            : $this->addSelectAggregate('COUNT(?)', $column);
    }

    /**
     * @param string|string[]|ExpressionInterface $columns
     * @return $this
     */
    public function addSelectCountDistinct(string|array|ExpressionInterface $columns): static
    {
        if (is_string($columns)) {
            $columns = explode(', ', $columns);
        }
        if (is_array($columns)) {
            $expr = $this->expr();
            foreach ($columns as &$column) {
                $column = $expr->wrap(trim($column));
            }
            unset($column);
            $columns = implode(', ', $columns);

            return $this->addSelectRaw("COUNT(DISTINCT $columns)", wrap: false);
        }

        return $this->addSelectAggregate('COUNT(DISTINCT ?)', $columns);
    }

    /**
     * @return $this
     */
    public function addSelectMax(string|ExpressionInterface $column, bool $distinct = false): static
    {
        return $distinct
            ? $this->addSelectAggregate('MAX(DISTINCT ?)', $column)
            : $this->addSelectAggregate('MAX(?)', $column);
    }

    /**
     * @return $this
     */
    public function addSelectMin(string|ExpressionInterface $column, bool $distinct = false): static
    {
        return $distinct
            ? $this->addSelectAggregate('MIN(DISTINCT ?)', $column)
            : $this->addSelectAggregate('MIN(?)', $column);
    }

    public function addSelectRaw(string $rawString, array $params = [], bool $wrap = true): static
    {
        $this->clearBuiltExpr();

        if ($params) {
            $this->select[] = new Expression($wrap ? $this->expr()->wrapSql($rawString) : $rawString, $params);
        } else {
            $this->select[] = $wrap ? $this->expr()->wrapSql($rawString) : $rawString;
        }

        return $this;
    }

    public function addSelectString(string $columns): static
    {
        return $this->addSelect(...$this->parseSelectString($columns));
    }

    /**
     * @return $this
     */
    public function addSelectSum(string|ExpressionInterface $column, bool $distinct = false): static
    {
        return $distinct
            ? $this->addSelectAggregate('SUM(DISTINCT ?)', $column)
            : $this->addSelectAggregate('SUM(?)', $column);
    }

    public function byId(int|string $id): static
    {
        $this->clearBuiltExpr();

        return $this->andWhereValueEQ('id', $id);
    }

    public function distinct(bool $value = true): static
    {
        if ($this->distinct !== $value) {
            $this->clearBuiltExpr();

            $this->distinct = $value;
        }

        return $this;
    }

    public function doesntExist(): bool
    {
        return !$this->exists();
    }

    public function exists(): bool
    {
        return $this->makeCleanClone(1)
                ->selectRaw('1', wrap: false)
                ->get()
                ->fetchValue() !== false;
    }

    public function expr(): ExpressionBuilderInterface
    {
        return $this->exprBuilder ?? ($this->exprBuilder = $this->query->expr());
    }

    public function getAggregateAvg(string|ExpressionInterface $column, bool $distinct = false): mixed
    {
        return $this->makeCleanClone(1)
            ->selectAvg($column, $distinct)
            ->get()
            ->fetchValue();
    }

    public function getAggregateCount(string $column = null, bool $distinct = false): int
    {
        return (int)$this->makeCleanClone()
            ->selectCount($column, $distinct)
            ->get()
            ->fetchValue();
    }

    public function getAggregateCountDistinct(string|array|ExpressionInterface $columns): int
    {
        return (int)$this->makeCleanClone()
            ->selectCountDistinct($columns)
            ->get()
            ->fetchValue();
    }

    public function getAggregateMax(string $column, bool $distinct = false): mixed
    {
        return $this->makeCleanClone(1)
            ->selectMax($column, $distinct)
            ->get()
            ->fetchValue();
    }

    public function getAggregateMin(string $column, bool $distinct = false): mixed
    {
        return $this->makeCleanClone(1)
            ->selectMin($column, $distinct)
            ->get()
            ->fetchValue();
    }

    public function getAggregateSum(string $column, bool $distinct = false): mixed
    {
        return $this->makeCleanClone(1)
            ->selectSum($column, $distinct)
            ->get()
            ->fetchValue();
    }

    public function getAll(): array
    {
        return $this->indexColumn === null
            ? $this->get()->fetchAll()
            : $this->get()->fetchAllIndexed();
    }

    public function getAllNumeric(): array
    {
        return $this->indexColumn === null
            ? $this->get()->fetchAllNumeric()
            : $this->get()->fetchAllNumericIndexed();
    }

    public function getAllObject(): array
    {
        return $this->indexColumn === null
            ? $this->get()->fetchAllObject()
            : $this->get()->fetchAllObjectIndexed();
    }

    public function getColumn(string $column = null, string $indexColumn = null): array
    {
        $query = clone $this;

        if ($indexColumn === null) {
            $queryIndexColumn = $query->indexColumn;

            if (is_int($queryIndexColumn)) {
                throw new NotSupportedException(
                    'The "getColumn" method requires a string or null "indexColumn" value.'
                );
            }

            $indexColumn = $queryIndexColumn;
        } else {
            $query->setIndexColumn($indexColumn);
        }

        if ($column !== null) {
            if ($indexColumn === null) {
                $query->select($column);
            } else {
                $query->select($indexColumn, $column);
            }
        } else {
            if (empty($query->select)) {
                throw new InvalidArgumentException(
                    'The $column argument cannot be null if there are no select elements.'
                );
            }

            if ($indexColumn !== null) {
                $query->clearBuiltExpr();
                array_unshift($query->select, [$indexColumn]);
            }
        }

        return $indexColumn === null
            ? $query->get()->fetchAllValues()
            : $query->get()->fetchAllValuesIndexed();
    }

    public function getCount(): int
    {
        return $this->getAggregateCount();
    }

    public function getFirst(): ?array
    {
        return (clone $this)->setLimit(1)->get()->fetch();
    }

    public function getFirstNumeric(): ?array
    {
        return (clone $this)->setLimit(1)->get()->fetchNumeric();
    }

    public function getFirstObject(): ?object
    {
        return (clone $this)->setLimit(1)->get()->fetchObject();
    }

    public function getIndexColumn(): int|string|null
    {
        return $this->indexColumn;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getOne(): ?array
    {
        $result = (clone $this)->setLimit(2)->get()->fetchAll();

        if (count($result) > 1) {
            throw new MoreRowsReceivedException();
        }

        return $result ? reset($result) : null;
    }

    public function getOneNumeric(): ?array
    {
        $result = (clone $this)->setLimit(2)->get()->fetchAllNumeric();

        if (count($result) > 1) {
            throw new MoreRowsReceivedException();
        }

        return $result ? reset($result) : null;
    }

    public function getOneObject(): ?object
    {
        $result = (clone $this)->setLimit(2)->get()->fetchAllObject();

        if (count($result) > 1) {
            throw new MoreRowsReceivedException();
        }

        return $result ? reset($result) : null;
    }

    final public function getType(): QueryTypeEnum
    {
        return QueryTypeEnum::Select;
    }

    public function getValue(string $column = null): mixed
    {
        $query = clone $this;

        if ($column !== null) {
            $query->select($column);
        }

        return $query->setLimit(1)->get()->fetchValue();
    }

    public function select(int|string|ExpressionInterface|array ...$columns): static
    {
        $this->clearBuiltExpr();

        $this->select = empty($columns) ? [] : [$columns];

        return $this;
    }

    /**
     * @return $this
     */
    public function selectAvg(string|ExpressionInterface $column, bool $distinct = false): static
    {
        $this->select = [];

        return $this->addSelectAvg($column, $distinct);
    }

    /**
     * @return $this
     */
    public function selectCount(string|ExpressionInterface $column = null, bool $distinct = false): static
    {
        $this->select = [];

        return $this->addSelectCount($column, $distinct);
    }

    /**
     * @param string|string[]|ExpressionInterface $columns
     * @return $this
     */
    public function selectCountDistinct(string|array|ExpressionInterface $columns): static
    {
        $this->select = [];

        return $this->addSelectCountDistinct($columns);
    }

    /**
     * @return $this
     */
    public function selectMax(string|ExpressionInterface $column, bool $distinct = false): static
    {
        $this->select = [];

        return $this->addSelectMax($column, $distinct);
    }

    /**
     * @return $this
     */
    public function selectMin(string|ExpressionInterface $column, bool $distinct = false): static
    {
        $this->select = [];

        return $this->addSelectMin($column, $distinct);
    }

    public function selectRaw(string $rawString, array $params = [], bool $wrap = true): static
    {
        $this->clearBuiltExpr();

        if ($params) {
            $this->select = [$this->expr()->raw($rawString, $params, $wrap)];
        } else {
            $this->select = [$wrap ? $this->expr()->wrapSql($rawString) : $rawString];
        }

        return $this;
    }

    public function selectString(string $columns): static
    {
        return $this->select(...$this->parseSelectString($columns));
    }

    /**
     * @return $this
     */
    public function selectSum(string|ExpressionInterface $column, bool $distinct = false): static
    {
        $this->select = [];

        return $this->addSelectSum($column, $distinct);
    }

    public function setIndexColumn(int|string|null $column): static
    {
        $this->indexColumn = $column;

        return $this;
    }

    public function setLimit(?int $value): static
    {
        if ($this->limit !== $value) {
            $this->clearBuiltExpr();

            $this->limit = $value;
        }

        return $this;
    }

    public function setOffset(?int $value): static
    {
        if ($this->offset !== $value) {
            $this->clearBuiltExpr();

            $this->offset = $value;
        }

        return $this;
    }

    public function skip(int $count): static
    {
        return $this->setOffset($count);
    }

    public function take(int $count): static
    {
        return $this->setLimit($count);
    }

    public function union(ExpressionInterface|SelectQueryInterface $query): static
    {
        // TODO: Implement union() method.
        throw new NotSupportedException();
    }

    protected function build(): Expression
    {
        /** @var list<string|ExpressionInterface> $joinsWhere */
        $joinsWhere = [];

        $composer = new SQLComposer($this->separator);

        $composer->addWithPrefix($this->distinct ? 'SELECT DISTINCT ' : 'SELECT ', $this->buildSelect());
        $composer->add($this->buildFrom($joinsWhere, $this->separator));
        $composer->addWithPrefix('WHERE ', $this->buildWhere($joinsWhere));
        $composer->addParams($this->getExtraParams());

        return $composer->toExpr();
    }

    protected function buildSelect(): string|Expression|null
    {
        $select = $this->select;

        if (empty($select)) {
            if (empty($this->from)) {
                throw new FromRequiredException('You must add at least one From element.');
            }

            return '*';
        }

        $conn = $this->getConnection();

        $composer = new SQLComposer(', ');

        foreach ($select as $item) {
            if (is_array($item)) {
                $this->buildSelectArray($item, $composer, $conn);
            } else {
                $composer->add($item);
            }
        }

        return $composer->compose(true);
    }

    protected function getParentQuery(): QueryInterface
    {
        return $this->query;
    }

    /**
     * @return $this
     */
    private function addSelectAggregate(string $sql, string|ExpressionInterface $column): static
    {
        $this->clearBuiltExpr();

        return is_string($column)
            ? $this->addSelectRaw(str_replace('?', $column, $sql))
            : $this->addSelectRaw(str_replace('?', (string)$column, $sql), $column->getParams(), false);
    }

    private function buildSelectArray(array $items, SQLComposer $composer, ?ConnectionInterface $conn): void
    {
        foreach ($items as $alias => $item) {
            if (is_array($item)) {
                $this->buildSelectArray($item, $composer, $conn);
            } elseif (is_string($item)) {
                if ($item !== '*' && $conn) {
                    $qItem = !is_string($alias) && str_ends_with($item, '.*')
                        ? $conn->wrap(substr($item, 0, -2)) . '.*'
                        : $conn->wrap($item);
                } else {
                    $qItem = $item;
                }
                if (!is_string($alias) || $alias === $item) {
                    $composer->add($qItem);
                } else {
                    $composer->add("$qItem AS " . ($conn?->wrap($alias) ?? $alias));
                }
            } else {
                $item = BaseExpressionBuilder::normalizeValue($item, $this->expr());

                if ($item instanceof ExpressionInterface) {
                    if (!is_string($alias)) {
                        throw new InvalidArgumentException('Adding an Expression to Select requires an alias.');
                    }
                    $alias = $conn?->wrapSingle($alias) ?? $alias;
                    if ((string)$item === $alias) {
                        $composer->add($item);
                    } else {
                        $composer->add(new Expression("($item) AS $alias", $item->getParams()));
                    }
                } else {
                    $expectedType = [
                        'array',
                        'string',
                        SelectQueryInterface::class,
                        ConditionInterface::class,
                        ExpressionInterface::class
                    ];
                    throw new InvalidArgumentTypeException('$item', $expectedType, $item);
                }
            }
        }
    }

    /**
     * @return static
     */
    private function makeCleanClone(int $limit = null): static
    {
        return (clone $this)
            ->distinct(false)
            ->setOffset(null)
            ->setLimit($limit);
    }

    /**
     * @return list<string|array<string, string>>
     */
    private function parseSelectString(string $columns): array
    {
        return array_map(static function (string $column): string|array {
            $column = trim($column);

            if (stripos($column, ' as ') === false) {
                return $column;
            }

            /** @var string[] $segments */
            $segments = preg_split('/\s+as\s+/i', $column, 2);
            return [$segments[1] => $segments[0]];
        }, explode(',', $columns));
    }
}
