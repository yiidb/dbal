<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query;

use Closure;
use YiiDb\DBAL\Contracts\Query\BaseQueryInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Query\Expressions\Expression;

use function is_string;

abstract class BaseQuery implements BaseQueryInterface
{
    protected bool $emulateExecution = false;
    protected string $separator = ' ';

    private ?Expression $builtExpr = null;
    private array $params = [];

    public function addParam(mixed $value): static
    {
        $this->clearBuiltExpr();

        $this->params[] = $value;

        return $this;
    }

    public function addParams(array $values): static
    {
        if ($values) {
            $this->clearBuiltExpr();

            $values = array_values($values);
            $this->params = ($oldParams = $this->params) ? [...$oldParams, ...$values] : $values;
        }

        return $this;
    }

    public function clearBuiltExpr(): void
    {
        $this->builtExpr = null;
    }

    final public function emulateExecution(bool $value = true): static
    {
        if ($this->emulateExecution !== $value) {
            $this->clearBuiltExpr();

            $this->emulateExecution = $value;
        }

        return $this;
    }

    final public function getDebugSql(): string
    {
        return $this->getSql();
    }

    final public function getParams(): array
    {
        return $this->toExpr()->getParams();
    }

    final public function getSql(): string
    {
        return (string)$this->toExpr();
    }

    final public function isEmulateExecution(): bool
    {
        return $this->emulateExecution;
    }

    final public function setParam(string|int $param, mixed $value): static
    {
        $this->clearBuiltExpr();

        $this->params[$param] = $value;

        return $this;
    }

    final public function setParams(array $values): static
    {
        if ($values) {
            $this->clearBuiltExpr();

            $this->params = ($oldParams = $this->params) ? [...$oldParams, ...$values] : $values;
        }

        return $this;
    }

    public function setSeparator(string $value): static
    {
        if ($this->separator !== $value) {
            $this->clearBuiltExpr();

            $this->separator = $value;
        }

        return $this;
    }

    final public function toExpr(): Expression
    {
        return $this->builtExpr ?? ($this->builtExpr = $this->build());
    }

    public function when(mixed $condition, Closure $func): static
    {
        if ($condition) {
            $func($this, $condition);
        }

        return $this;
    }

    abstract protected function build(): Expression;

    protected function buildTable(string|TableName|ExpressionInterface $table): string
    {
        if ($table instanceof ExpressionInterface) {
            return (string)$table;
        }

        return is_string($table)
            ? $this->getConnection()?->wrap($table) ?? $table
            : TableName::getQuoted($table, $this->getConnection());
    }

    protected function getExtraParams(): array
    {
        return $this->params;
    }
}
