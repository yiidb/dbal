<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query;

use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\DeleteQueryInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryTypeEnum;
use YiiDb\DBAL\Query\Expressions\Expression;

abstract class BaseDeleteQuery extends BaseQuery implements DeleteQueryInterface
{
    use QueryWithWhereTrait;

    private string|TableName|ExpressionInterface $table;

    public function __construct(string|TableName|ExpressionInterface $table, ConditionBuilderInterface $condBuilder)
    {
        $this->table = $table;

        /** @see QueryWithWhereTrait */
        $this->condBuilder = $condBuilder;
    }

    final public function getType(): QueryTypeEnum
    {
        return QueryTypeEnum::Delete;
    }

    /**
     * @return $this
     */
    public function table(string|TableName|ExpressionInterface $table): static
    {
        $this->table = $table;

        return $this;
    }

    protected function build(): Expression
    {
        $composer = new SQLComposer($this->separator);

        $composer->addWithPrefix('DELETE ', $this->buildTable($this->table));
        $composer->addWithPrefix('WHERE ', $this->buildWhere());
        $composer->addParams($this->getExtraParams());

        return $composer->toExpr();
    }
}
