<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query;

use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryTypeEnum;
use YiiDb\DBAL\Contracts\Query\UpdateQueryInterface;
use YiiDb\DBAL\Query\Expressions\Expression;

abstract class BaseUpdateQuery extends BaseQuery implements UpdateQueryInterface
{
    use QueryWithWhereTrait;

    /**
     * @var array<string, mixed>
     */
    private array $sets = [];
    private string|TableName|ExpressionInterface $table;

    public function __construct(string|TableName|ExpressionInterface $table, ConditionBuilderInterface $condBuilder)
    {
        $this->table = $table;

        /** @see QueryWithWhereTrait */
        $this->condBuilder = $condBuilder;
    }

    public function getType(): QueryTypeEnum
    {
        return QueryTypeEnum::Update;
    }

    public function set(string $column, mixed $value): static
    {
        $this->clearBuiltExpr();

        $this->sets[$column] = $value;

        return $this;
    }

    public function setRaw(string $column, string $rawString, array $params = [], bool $wrap = true): static
    {
        $this->clearBuiltExpr();

        $this->sets[$column] = $this->expr()->raw($rawString, $params, $wrap);

        return $this;
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

        $composer->addWithPrefix('UPDATE ', $this->buildTable($this->table));
        $composer->add($this->buildSet());
        $composer->addWithPrefix('WHERE ', $this->buildWhere());
        $composer->addParams($this->getExtraParams());

        return $composer->toExpr();
    }

    private function buildSet(): string|Expression
    {
        $composer = new SQLComposer(', ');

        $expr = $this->expr();

        foreach ($this->sets as $col => $value) {
            $col = $expr->wrapSingle($col);

            if (!($value instanceof ExpressionInterface)) {
                $value = $expr->value($value);
            }

            $value = empty($params = $value->getParams())
                ? "$col = $value"
                : new Expression("$col = $value", $params);

            $composer->add($value);
        }

        return $composer->compose(true, 'SET ');
    }
}
