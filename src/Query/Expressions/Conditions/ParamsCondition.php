<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use TypeError;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;
use YiiDb\DBAL\Query\Expressions\Expression;

use function count;
use function is_string;

/**
 * @psalm-immutable
 */
final class ParamsCondition implements ConditionInterface
{
    public function __construct(
        private readonly string|ExpressionInterface|ConditionInterface $cond,
        private readonly array $params
    ) {
    }

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): string|ExpressionInterface {
        /** @var self $self */
        $cond = $self->cond;
        if ($cond instanceof ConditionInterface) {
            $cond = $cond::build($cond, $exprBuilder);
        }

        if ($params = $self->params) {
            return is_string($cond) || !($exprParams = $cond->getParams())
                ? new Expression((string)$cond, $params)
                : new Expression((string)$cond, [...$exprParams, ...$params]);
        }

        return $cond;
    }

    /**
     * @param array{0: string, 1: mixed, 2: array} $arr
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        if (count($arr) !== 3) {
            throw new InvalidExpressionFormatException();
        }

        [, $cond, $params] = $arr;

        try {
            return new static($condBuilder->mixedCond($cond, $exprBuilder), $params);
        } catch (TypeError $e) {
            throw new InvalidExpressionFormatException(previous: $e);
        }
    }
}
