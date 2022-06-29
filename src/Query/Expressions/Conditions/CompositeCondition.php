<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions\Conditions;

use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;
use YiiDb\DBAL\Exceptions\NotSupportedException;

use function is_string;

/**
 * @psalm-immutable
 */
final class CompositeCondition implements ConditionInterface
{
    public const TYPE_AND = 'and';
    public const TYPE_OR = 'or';

    public readonly string $type;

    /**
     * @var array<string|ConditionInterface|ExpressionInterface>
     */
    private array $items;

    /**
     * @param array<string|ExpressionInterface|ConditionInterface> $items
     */
    public function __construct(string $type, array $items)
    {
        if ($type !== self::TYPE_AND && $type !== self::TYPE_OR) {
            throw new InvalidExpressionFormatException();
        }

        foreach ($items as $item) {
            if (
                !is_string($item)
                && !($item instanceof ConditionInterface)
                && !($item instanceof ExpressionInterface)
            ) {
                throw new InvalidExpressionFormatException();
            }
        }

        $this->type = $type;
        $this->items = $items;
    }

    public static function build(
        self|ConditionInterface $self,
        ExpressionBuilderInterface $exprBuilder
    ): ExpressionInterface {
        /** @var self $self */
        $items = $self->items;

        foreach ($items as &$item) {
            if ($item instanceof ConditionInterface) {
                $item = $item::build($item, $exprBuilder);
            }
        }
        /** @var array<string|ExpressionInterface> $items */

        return $self->type === self::TYPE_AND ? $exprBuilder->and(...$items) : $exprBuilder->or(...$items);
    }

    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): never {
        throw new NotSupportedException();
    }

    public function __debugInfo(): array
    {
        return ['type' => $this->type, ...$this->items];
    }

    /**
     * @param array<string|ExpressionInterface|ConditionInterface> $arr
     */
    public function addFromArray(array $arr): self
    {
        if (!$arr) {
            return $this;
        }

        foreach ($arr as $item) {
            if (
                !is_string($item)
                && !($item instanceof ConditionInterface)
                && !($item instanceof ExpressionInterface)
            ) {
                throw new InvalidExpressionFormatException();
            }
        }

        $new = clone $this;
        $new->items = [...$new->items, ...$arr];

        return $new;
    }
}
