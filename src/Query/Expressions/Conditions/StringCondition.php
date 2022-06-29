<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;

use function count;
use function is_string;

/**
 * @psalm-immutable
 */
final class StringCondition implements ConditionInterface
{
    public function __construct(
        public readonly string $string
    ) {
    }

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /** @var self $self */
        return $exprBuilder->raw($self->string);
    }

    /**
     * @param array{0: string, 1: non-empty-string} $arr
     * @psalm-suppress MoreSpecificImplementedParamType, DocblockTypeContradiction
     */
    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        if (count($arr) !== 2 || empty($string = $arr[1] ?? null) || !is_string($string)) {
            throw new InvalidExpressionFormatException();
        }

        return new static($string);
    }
}
