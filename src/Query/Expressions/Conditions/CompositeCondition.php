<?php

declare(strict_types=1);

namespace Yii\DBAL\Query\Expressions\Conditions;

use Yii\DBAL\Contracts\Query\ConditionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ConditionInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use Yii\DBAL\Exceptions\InvalidExpressionFormatException;
use Yii\DBAL\Exceptions\NotSupportedException;

use function is_string;

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

    public function __debugInfo(): array
    {
        return ['type' => $this->type, ...$this->items];
    }

    /**
     * @param array<string|ExpressionInterface|ConditionInterface> $arr
     * @return $this
     */
    public function addFromArray(array $arr): static
    {
        if ($arr) {
            foreach ($arr as $item) {
                if (
                    !is_string($item)
                    && !($item instanceof ConditionInterface)
                    && !($item instanceof ExpressionInterface)
                ) {
                    throw new InvalidExpressionFormatException();
                }
            }

            $this->items = [...$this->items, ...$arr];
        }

        return $this;
    }

    public function build(ExpressionBuilderInterface $exprBuilder): ExpressionInterface
    {
        $items = $this->items;

        foreach ($items as &$item) {
            if ($item instanceof ConditionInterface) {
                $item = $item->build($exprBuilder);
            }
        }
        /** @var array<string|ExpressionInterface> $items */

        return $this->type === self::TYPE_AND ? $exprBuilder->and(...$items) : $exprBuilder->or(...$items);
    }

    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): never {
        throw new NotSupportedException();
    }
}
