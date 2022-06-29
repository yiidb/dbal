<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query;

use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Query\Expressions\Conditions\CompositeCondition;

interface ConditionBuilderInterface
{
    public function andCond(
        string|ExpressionInterface|ConditionInterface $cond1,
        string|ExpressionInterface|ConditionInterface|array $cond2,
        ?array $params,
        ExpressionBuilderInterface $exprBuilder
    ): CompositeCondition;

    public function build(
        string|ExpressionInterface|ConditionInterface $cond,
        ExpressionBuilderInterface $exprBuilder
    ): string|ExpressionInterface|null;

    public function cond(
        string|ExpressionInterface|ConditionInterface|array $cond,
        ?array $params,
        ExpressionBuilderInterface $exprBuilder
    ): string|ExpressionInterface|ConditionInterface|null;

    public function getRealtimeCondBuilding(): bool;

    public function merge(
        string|ExpressionInterface|ConditionInterface|null $item,
        string|ExpressionInterface|ConditionInterface ...$items
    ): string|ExpressionInterface|ConditionInterface|null;

    public function mixedCond(
        mixed $cond,
        ExpressionBuilderInterface $exprBuilder
    ): string|ExpressionInterface|ConditionInterface;

    public function orCond(
        string|ExpressionInterface|ConditionInterface $cond1,
        string|ExpressionInterface|ConditionInterface|array $cond2,
        ?array $params,
        ExpressionBuilderInterface $exprBuilder
    ): CompositeCondition;

    public function setRealtimeCondBuilding(bool $value): bool;
}
