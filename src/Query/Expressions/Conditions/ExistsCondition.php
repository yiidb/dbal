<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use TypeError;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\SelectQueryInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;

use function count;

/**
 * @psalm-immutable
 */
final class ExistsCondition implements ConditionInterface
{
    public function __construct(
        private readonly ExpressionInterface|SelectQueryInterface $query,
        private readonly bool $not = false
    ) {
    }

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /** @var self $self */
        $query = $self->query;
        if ($query instanceof SelectQueryInterface) {
            $query = $query->toExpr();
        }

        return $self->not
            ? $exprBuilder->notExists($query)
            : $exprBuilder->exists($query);
    }

    /**
     * @param array{0: string, 1: ExpressionInterface|SelectQueryInterface} $arr
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        if (count($arr) !== 2) {
            throw new InvalidExpressionFormatException();
        }

        [$name, $query] = $arr;

        try {
            return new static($query, str_contains($name, 'not'));
        } catch (TypeError $e) {
            throw new InvalidExpressionFormatException(previous: $e);
        }
    }
}
