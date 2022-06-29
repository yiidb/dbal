<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions;

use LogicException;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\SelectQueryInterface;
use YiiDb\DBAL\Query\TableName;

use function is_string;

abstract class BaseExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @template T
     * @param T $value
     * @return T|ExpressionInterface
     */
    public static function normalizeValue(mixed $value, ExpressionBuilderInterface $exprBuilder): mixed
    {
        if ($value instanceof SelectQueryInterface) {
            $value = $value->toExpr();
        } elseif ($value instanceof ConditionInterface) {
            $value = $value::build($value, $exprBuilder);
            if (is_string($value)) {
                $value = new Expression($value);
            }
        }

        return $value;
    }

    public function and(
        string|ExpressionInterface $expression,
        string|ExpressionInterface ...$expressions
    ): CompositeExpression {
        return CompositeExpression::and($expression, ...$expressions);
    }

    public function comparisonColumns(string $columnA, string $operator, string $columnB): Expression
    {
        return new Expression($this->wrap($columnA) . " $operator " . $this->wrap($columnB));
    }

    public function eqColumns(string $columnA, string $columnB): Expression
    {
        return $this->comparisonColumns($columnA, Operator::EQ, $columnB);
    }

    public function exists(ExpressionInterface $query, bool $not = false): ExpressionInterface
    {
        $operator = $not ? 'NOT EXISTS' : 'EXISTS';

        return new Expression("$operator ($query)", $query->getParams());
    }

    public function formatExpr(string $rawString, array $params, bool $wrap = true): Expression
    {
        $paramsHeap = [];
        $rawString = preg_replace_callback('/\?/', function () use (&$params, &$paramsHeap): string {
            /** @psalm-var array $params */
            if (empty($params)) {
                throw new LogicException('Characters "?" must match the number of params elements.');
            }

            $expr = $this->value(array_pop($params));
            if (!empty($_ = $expr->getParams())) {
                /** @psalm-var array[] $paramsHeap */
                $paramsHeap[] = $_;
            }

            return (string)$expr;
        }, $rawString);

        if (!empty($params)) {
            throw new LogicException('Characters "?" must match the number of params elements.');
        }

        /** @psalm-suppress MixedArgument */
        return $this->raw($rawString, array_merge(...$paramsHeap), $wrap);
    }

    public function gtColumns(string $columnA, string $columnB): Expression
    {
        return $this->comparisonColumns($columnA, Operator::GT, $columnB);
    }

    public function gteColumns(string $columnA, string $columnB): Expression
    {
        return $this->comparisonColumns($columnA, Operator::GTE, $columnB);
    }

    public function isNotNull(string|ExpressionInterface $column): Expression
    {
        return $this->isNull($column, true);
    }

    public function isNull(string|ExpressionInterface $column, bool $not = false): Expression
    {
        $operator = $not ? 'IS NOT NULL' : 'IS NULL';

        if (is_string($column)) {
            return new Expression($this->wrap($column) . " $operator");
        }

        return new Expression("($column) $operator", $column->getParams());
    }

    public function like(
        string|ExpressionInterface $column,
        string $pattern,
        string $escapeChar = null,
        bool $not = false
    ): ExpressionInterface {
        $operator = $not ? Operator::NOT_LIKE : Operator::LIKE;
        $rawString = $this->comparison($column, $operator, $pattern);

        return $escapeChar !== null
            ? new Expression("$rawString ESCAPE $escapeChar", $rawString->getParams())
            : $rawString;
    }

    public function ltColumns(string $columnA, string $columnB): Expression
    {
        return $this->comparisonColumns($columnA, Operator::LT, $columnB);
    }

    public function lteColumns(string $columnA, string $columnB): Expression
    {
        return $this->comparisonColumns($columnA, Operator::LTE, $columnB);
    }

    public function neqColumns(string $columnA, string $columnB): Expression
    {
        return $this->comparisonColumns($columnA, Operator::NEQ, $columnB);
    }

    public function not(string|ExpressionInterface $column): Expression
    {
        if (is_string($column)) {
            return new Expression('NOT ' . $this->wrap($column));
        }

        return new Expression("NOT ($column)", $column->getParams());
    }

    public function notExists(ExpressionInterface $query): ExpressionInterface
    {
        return $this->exists($query, true);
    }

    public function notLike(
        string|ExpressionInterface $column,
        string $pattern,
        string $escapeChar = null
    ): ExpressionInterface {
        return $this->like($column, $pattern, $escapeChar, true);
    }

    public function or(
        string|ExpressionInterface $expression,
        string|ExpressionInterface ...$expressions
    ): CompositeExpression {
        return CompositeExpression::or($expression, ...$expressions);
    }

    final public function raw(string $rawString, array $params = [], bool $wrap = true): Expression
    {
        return new Expression($wrap ? $this->wrapSql($rawString) : $rawString, $params);
    }

    final public function tableName(string $table, string $database = null, string $alias = null): TableName
    {
        $table = $this->wrapSingle($table);
        if ($database) {
            $table = $this->wrapSingle($database) . '.' . $table;
        }

        return new TableName($table, $database, $alias);
    }

    public function wrapSql(string $sql): string
    {
        return preg_replace_callback(
            '/(\\{\\{(%?[^{}]+%?)}}|\\[\\[([^[\]]+)]])/',
            static fn (array $matches): string => $matches[3] ?? str_replace('%', '', $matches[2]),
            $sql
        );
    }
}
