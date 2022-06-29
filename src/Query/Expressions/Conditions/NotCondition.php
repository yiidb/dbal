<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;

use function count;

/**
 * @psalm-immutable
 */
final class NotCondition implements ConditionInterface
{
    public function __construct(
        private readonly string|ExpressionInterface|ConditionInterface $cond
    ) {
    }

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /** @var self $self */
        $cond = $self->cond;
        if ($cond instanceof ConditionInterface) {
            $cond = $cond::build($cond, $exprBuilder);
        }

        return $exprBuilder->not($cond);
    }

    /**
     * @param array{0: string, 1: mixed} $arr
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        if (count($arr) !== 2 || empty($cond = $arr[1] ?? null)) {
            throw new InvalidExpressionFormatException();
        }

        return new static($condBuilder->mixedCond($cond, $exprBuilder));
    }
}
