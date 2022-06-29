<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\SelectQuery;

use YiiDb\DBAL\Contracts\ConnectionInterface;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryInterface;
use YiiDb\DBAL\Contracts\Query\SelectQuery\JoinInterface;
use YiiDb\DBAL\Contracts\Query\SelectQueryInterface;
use YiiDb\DBAL\Exceptions\InvalidArgumentException;
use YiiDb\DBAL\Query\Expressions\Expression;
use YiiDb\DBAL\Query\JoinOnHelpersTrait;
use YiiDb\DBAL\Query\QueryWithWhereTrait;
use YiiDb\DBAL\Query\SQLComposer;
use YiiDb\DBAL\Query\TableName;

use function is_string;

final class Join implements JoinInterface
{
    use QueryWithWhereTrait;
    use JoinOnHelpersTrait;

    public const TYPE_CROSS = 'CROSS JOIN';
    public const TYPE_FULL = 'FULL JOIN';
    public const TYPE_INNER = 'INNER JOIN';
    public const TYPE_LEFT = 'LEFT JOIN';
    public const TYPE_RIGHT = 'RIGHT JOIN';

    private string|Expression|null $builtExpr = null;
    private string|ExpressionInterface|null $builtWhereExpr = null;
    private ?ExpressionBuilderInterface $exprBuilder = null;
    private string|ExpressionInterface|ConditionInterface|null $on = null;
    private readonly QueryInterface $query;
    private TableItem $table;
    private string $type;

    /**
     * @internal
     */
    public function __construct(
        string $type,
        string|TableName|ExpressionInterface|SelectQueryInterface $table,
        ?string $alias,
        QueryInterface $query,
        ConditionBuilderInterface $condBuilder
    ) {
        if ($table instanceof SelectQueryInterface) {
            if (!is_string($alias)) {
                throw new InvalidArgumentException('Adding an Query to Join requires an alias.');
            }

            $table = $table->toExpr();
        }

        if (!is_string($alias) && ($table instanceof ExpressionInterface)) {
            throw new InvalidArgumentException('Adding an Expression to Join requires an alias.');
        }


        $this->type = $type;
        $this->table = new TableItem($table, $alias);
        $this->query = $query;

        /** @see QueryWithWhereTrait */
        $this->condBuilder = $condBuilder;
    }

    /**
     * @return $this
     */
    public function andOn(string|ExpressionInterface|ConditionInterface|array $on, array $params = null): static
    {
        if (!empty($on)) {
            if (empty($this->on)) {
                return $this->on($on, $params);
            }

            $this->clearBuiltExpr();

            $this->on = $this->condBuilder->andCond($this->on, $on, $params, $this->expr());
        }

        return $this;
    }

    public function clearBuiltExpr(): void
    {
        $this->builtExpr = null;
        $this->builtWhereExpr = null;
    }

    public function expr(): ExpressionBuilderInterface
    {
        return $this->exprBuilder ?? ($this->exprBuilder = $this->query->expr());
    }

    public function getRef(): string
    {
        return $this->table->getRef();
    }

    public function getWhereExpr(): string|ExpressionInterface|null
    {
        return $this->builtWhereExpr;
    }

    /**
     * @return $this
     */
    public function on(string|ExpressionInterface|ConditionInterface|array|null $on, array $params = null): static
    {
        $this->clearBuiltExpr();

        $this->on = empty($on) ? null : $this->condBuilder->cond($on, $params, $this->expr());

        return $this;
    }

    /**
     * @return $this
     */
    public function orOn(string|ExpressionInterface|ConditionInterface|array $on, array $params = null): static
    {
        if (!empty($on)) {
            if (empty($this->on)) {
                return $this->on($on);
            }

            $this->clearBuiltExpr();

            $this->on = $this->condBuilder->orCond($this->on, $on, $params, $this->expr());
        }

        return $this;
    }

    public function toExpr(?ConnectionInterface $conn): string|Expression
    {
        if ($this->builtExpr === null) {
            $this->builtExpr = $this->build($conn);
            $this->builtWhereExpr = $this->buildWhere();
        }

        return $this->builtExpr;
    }

    private function build(?ConnectionInterface $conn): string|Expression
    {
        return SQLComposer::composeItems(
            ' ',
            $this->type,
            TableItem::build($this->table, $conn),
            $this->buildOn()
        );
    }

    private function buildOn(): string|Expression|null
    {
        $sql = empty($this->on) ? null : $this->condBuilder->build($this->on, $this->expr());

        if (empty($sql)) {
            return null;
        }

        /**
         * @psalm-var array $params Fix psalm bug (https://github.com/vimeo/psalm/issues/8146)
         */
        return is_string($sql) || !($params = $sql->getParams())
            ? "ON $sql"
            : new Expression("ON $sql", $params);
    }
}
