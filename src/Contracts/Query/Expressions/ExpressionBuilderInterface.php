<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query\Expressions;

use YiiDb\DBAL\Query\Expressions\CompositeExpression;
use YiiDb\DBAL\Query\TableName;

interface ExpressionBuilderInterface
{
    public function and(
        string|ExpressionInterface $expression,
        string|ExpressionInterface ...$expressions
    ): CompositeExpression;

    public function between(string|ExpressionInterface $column, mixed $minValue, mixed $maxValue): ExpressionInterface;

    public function comparison(
        string|ExpressionInterface $column,
        string $operator,
        mixed $value
    ): ExpressionInterface;

    public function comparisonColumns(string $columnA, string $operator, string $columnB): ExpressionInterface;

    public function eq(string $column, mixed $value): ExpressionInterface;

    public function eqColumns(string $columnA, string $columnB): ExpressionInterface;

    public function exists(ExpressionInterface $query): ExpressionInterface;

    public function formatExpr(string $rawString, array $params, bool $wrap = true): ExpressionInterface;

    public function gt(string $column, mixed $value): ExpressionInterface;

    public function gtColumns(string $columnA, string $columnB): ExpressionInterface;

    public function gte(string $column, mixed $value): ExpressionInterface;

    public function gteColumns(string $columnA, string $columnB): ExpressionInterface;

    public function in(string|ExpressionInterface $column, array|ExpressionInterface $values): ExpressionInterface;

    public function isNotNull(string|ExpressionInterface $column): ExpressionInterface;

    public function isNull(string|ExpressionInterface $column): ExpressionInterface;

    public function like(
        string|ExpressionInterface $column,
        string $pattern,
        string $escapeChar = null
    ): ExpressionInterface;

    public function lt(string $column, mixed $value): ExpressionInterface;

    public function ltColumns(string $columnA, string $columnB): ExpressionInterface;

    public function lte(string $column, mixed $value): ExpressionInterface;

    public function lteColumns(string $columnA, string $columnB): ExpressionInterface;

    public function neq(string $column, mixed $value): ExpressionInterface;

    public function neqColumns(string $columnA, string $columnB): ExpressionInterface;

    public function not(string|ExpressionInterface $column): ExpressionInterface;

    public function notBetween(
        string|ExpressionInterface $column,
        mixed $minValue,
        mixed $maxValue
    ): ExpressionInterface;

    public function notExists(ExpressionInterface $query): ExpressionInterface;

    public function notIn(string|ExpressionInterface $column, array|ExpressionInterface $values): ExpressionInterface;

    public function notLike(
        string|ExpressionInterface $column,
        string $pattern,
        string $escapeChar = null
    ): ExpressionInterface;

    public function or(
        string|ExpressionInterface $expression,
        string|ExpressionInterface ...$expressions
    ): CompositeExpression;

    public function raw(string $rawString, array $params = [], bool $wrap = true): ExpressionInterface;

    public function tableName(string $table, string $database = null): TableName;

    public function value(mixed $value): ExpressionInterface;

    public function wrap(string $value): string;

    public function wrapSingle(string $value): string;

    public function wrapSql(string $sql): string;
}
