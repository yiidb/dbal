<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;
use YiiDb\DBAL\Query\Expressions\BaseExpressionBuilder;
use YiiDb\DBAL\Query\Expressions\CompositeExpression;

use function count;
use function is_array;
use function is_string;

/**
 * @psalm-immutable
 */
final class ArrowCondition implements ConditionInterface
{
    /**
     * @param array<string, mixed> $items
     */
    public function __construct(
        private readonly array $items
    ) {
        empty($items) && throw new InvalidExpressionFormatException('An empty list of expressions is not allowed.');

        foreach ($items as $key => $_) {
            /**
             * @var string|int $key
             */
            if (!is_string($key)) {
                throw new InvalidExpressionFormatException();
            }
        }
    }

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /**
         * @var self $self
         */
        $items = [];
        foreach ($self->items as $key => $value) {
            $items[] = self::buildItem($key, $value, $exprBuilder);
        }

        if (count($items) === 1) {
            return reset($items);
        }

        return CompositeExpression::and(...$items);
    }

    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        unset($arr[0]);

        return new static($arr);
    }

    private static function buildItem(
        string $key,
        mixed $value,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        if ($value === null) {
            $result = str_starts_with($key, '!')
                ? $exprBuilder->isNotNull(substr($key, 1))
                : $exprBuilder->isNull($key);
        } elseif (is_array($value)) {
            $result = str_starts_with($key, '!')
                ? $exprBuilder->notIn(substr($key, 1), $value)
                : $exprBuilder->in($key, $value);
        } else {
            $value = BaseExpressionBuilder::normalizeValue($value, $exprBuilder);

            $result = str_starts_with($key, '!')
                ? $exprBuilder->neq(substr($key, 1), $value)
                : $exprBuilder->eq($key, $value);
        }
        return $result;
    }

    public function __debugInfo(): array
    {
        return [...$this->items];
    }
}
