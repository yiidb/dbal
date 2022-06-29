<?php

declare(strict_types=1);

namespace Yii\DBAL\Query\SelectQuery;

use Yii\DBAL\Contracts\ConnectionInterface;
use Yii\DBAL\Contracts\Query\ConditionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ConditionInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use Yii\DBAL\Contracts\Query\SelectQuery\JoinInterface;
use Yii\DBAL\Contracts\Query\SelectQueryInterface;
use Yii\DBAL\Exceptions\InvalidArgumentException;
use Yii\DBAL\Query\Expressions\Expression;
use Yii\DBAL\Query\QueryWithWhereTrait;
use Yii\DBAL\Query\SQLComposer;
use Yii\DBAL\Query\TableName;

use function is_string;

final class Join implements JoinInterface
{
    use QueryWithWhereTrait;

    public const TYPE_CROSS = 'CROSS JOIN';
    public const TYPE_FULL = 'FULL JOIN';
    public const TYPE_INNER = 'INNER JOIN';
    public const TYPE_LEFT = 'LEFT JOIN';
    public const TYPE_RIGHT = 'RIGHT JOIN';

    private string|Expression|null $builtExpr = null;
    private string|ExpressionInterface|null $builtWhereExpr = null;
    private ExpressionBuilderInterface $exprBuilder;
    private string|ExpressionInterface|ConditionInterface|null $on = null;
    private TableItem $table;
    private string $type;

    /**
     * @internal
     */
    public function __construct(
        string $type,
        string|TableName|ExpressionInterface|SelectQueryInterface $table,
        ?string $alias,
        ExpressionBuilderInterface $exprBuilder,
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
        $this->exprBuilder = $exprBuilder;

        /** @see QueryWithWhereTrait */
        $this->condBuilder = $condBuilder;
    }

    public function andOn(ExpressionInterface|array|string $on, array $params = null): static
    {
        if (!empty($on)) {
            if (empty($this->on)) {
                return $this->on($on, $params);
            }

            $this->on = $this->condBuilder->andCond($this->on, $on, $params, $this->exprBuilder);

            $this->clearBuiltExpr();
        }

        return $this;
    }

    public function expr(): ExpressionBuilderInterface
    {
        return $this->exprBuilder;
    }

    public function getRef(): string
    {
        return $this->table->getRef();
    }

    public function getWhereExpr(): string|ExpressionInterface|null
    {
        return $this->builtWhereExpr;
    }

    public function on(ExpressionInterface|array|string|null $on, array $params = null): static
    {
        $this->on = empty($on) ? null : $this->condBuilder->cond($on, $params, $this->exprBuilder);

        $this->clearBuiltExpr();

        return $this;
    }

    public function orOn(ExpressionInterface|array|string $on, array $params = null): static
    {
        if (!empty($on)) {
            if (empty($this->on)) {
                return $this->on($on, $params);
            }

            $this->on = $this->condBuilder->orCond($this->on, $on, $params, $this->exprBuilder);

            $this->clearBuiltExpr();
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

    protected function clearBuiltExpr(): void
    {
        $this->builtExpr = null;
        $this->builtWhereExpr = null;
    }

    private function build(?ConnectionInterface $conn): string|Expression
    {
        return SQLComposer::composeItems(
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

        return is_string($sql) || !($params = $sql->getParams())
            ? "ON $sql"
            : new Expression("ON $sql", $params);
    }
}
