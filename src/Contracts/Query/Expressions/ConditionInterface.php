<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query\Expressions;

use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;

/**
 * @psalm-immutable
 */
interface ConditionInterface
{
    public static function build(self $self, ExpressionBuilderInterface $exprBuilder): string|ExpressionInterface;

    /**
     * @param array{0: string} $arr
     * @throws InvalidExpressionFormatException
     */
    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static;
}
