<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query;

use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryWithWhereInterface;

/**
 * @psalm-require-implements QueryWithWhereInterface
 * @see QueryWithWhereInterface
 *
 */
trait QueryWithWhereTrait
{
    use QueryWithWhereHelpersTrait;

    private readonly ConditionBuilderInterface $condBuilder;
    private string|ExpressionInterface|ConditionInterface|null $where = null;

    /**
     * @return $this
     */
    public function andWhere(string|ExpressionInterface|ConditionInterface|array $where, array $params = null): static
    {
        if (!empty($where)) {
            if (empty($this->where)) {
                return $this->where($where, $params);
            }

            $this->clearBuiltExpr();

            $this->where = $this->condBuilder->andCond($this->where, $where, $params, $this->expr());
        }

        return $this;
    }

    abstract public function clearBuiltExpr(): void;

    abstract public function expr(): ExpressionBuilderInterface;

    public function getConditionBuilder(): ConditionBuilderInterface
    {
        return $this->condBuilder;
    }

    /**
     * @return $this
     */
    public function orWhere(string|ExpressionInterface|ConditionInterface|array $where, array $params = null): static
    {
        if (!empty($where)) {
            if (empty($this->where)) {
                return $this->where($where);
            }

            $this->clearBuiltExpr();

            $this->where = $this->condBuilder->orCond($this->where, $where, $params, $this->expr());
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function where(string|ExpressionInterface|ConditionInterface|array|null $where, array $params = null): static
    {
        $this->clearBuiltExpr();

        $this->where = empty($where) ? null : $this->condBuilder->cond($where, $params, $this->expr());

        return $this;
    }

    /**
     * @param list<string|ExpressionInterface> $joinsWhere
     */
    protected function buildWhere(array $joinsWhere = null): string|ExpressionInterface|null
    {
        $where = $this->where;

        if (!empty($joinsWhere)) {
            $where = $this->condBuilder->merge($where, ...$joinsWhere);
        }

        return empty($where) ? null : $this->condBuilder->build($where, $this->expr());
    }
}
