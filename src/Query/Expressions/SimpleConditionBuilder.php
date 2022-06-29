<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions;

use TypeError;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ConditionInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Exceptions\InvalidExpressionFormatException;
use YiiDb\DBAL\Query\Expressions\Conditions\ArrowCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\BetweenCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\ColumnSimpleCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\CompositeCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\ExistsCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\InCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\NotCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\ParamsCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\RawStringCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\SimpleCondition;
use YiiDb\DBAL\Query\Expressions\Conditions\StringCondition;

use function count;
use function in_array;
use function is_array;
use function is_string;

final class SimpleConditionBuilder implements ConditionBuilderInterface
{
    /**
     * @var array<class-string<ConditionInterface>>
     */
    private array $supportedConditions;

    /**
     *
     * @param array<class-string<ConditionInterface>>|null $supportedConditions
     */
    public function __construct(
        public bool $realtimeCondBuilding = false,
        array $supportedConditions = null
    ) {
        $this->supportedConditions = $supportedConditions ?? [
                'compare' => ArrowCondition::class,
                'col' => ColumnSimpleCondition::class,
                'val' => SimpleCondition::class,
                'string' => StringCondition::class,
                'raw' => RawStringCondition::class,
                'not' => NotCondition::class,
                'in' => InCondition::class,
                'not in' => InCondition::class,
                'notin' => InCondition::class,
                'exists' => ExistsCondition::class,
                'not exists' => ExistsCondition::class,
                'notexists' => ExistsCondition::class,
                'between' => BetweenCondition::class,
                'not between' => BetweenCondition::class,
                'notbetween' => BetweenCondition::class,
            ];
    }

    public function andCond(
        string|ExpressionInterface|ConditionInterface $cond1,
        string|ExpressionInterface|ConditionInterface|array $cond2,
        ?array $params,
        ExpressionBuilderInterface $exprBuilder
    ): CompositeCondition {
        if ($params) {
            $cond2 = new ParamsCondition($this->mixedCond($cond2, $exprBuilder), $params);
        }

        return $this->compositeCond(CompositeCondition::TYPE_AND, $cond1, $cond2, $exprBuilder);
    }

    public function build(
        string|ExpressionInterface|ConditionInterface $cond,
        ExpressionBuilderInterface $exprBuilder
    ): string|ExpressionInterface {
        return $cond instanceof ConditionInterface ? $cond::build($cond, $exprBuilder) : $cond;
    }

    public function cond(
        string|ExpressionInterface|ConditionInterface|array $cond,
        ?array $params,
        ExpressionBuilderInterface $exprBuilder
    ): string|ExpressionInterface|ConditionInterface {
        if ($params) {
            $cond = new ParamsCondition($this->cond($cond, null, $exprBuilder), $params);
        }

        $condArr = $this->prepareCondEx($cond, $exprBuilder);

        return reset($condArr);
    }

    public function getRealtimeCondBuilding(): bool
    {
        return $this->realtimeCondBuilding;
    }

    public function merge(
        string|ExpressionInterface|ConditionInterface|null $item,
        string|ExpressionInterface|ConditionInterface ...$items
    ): string|ExpressionInterface|ConditionInterface|null {
        if (empty($items)) {
            return $item;
        }

        if ($item === null) {
            return count($items) === 1 ? reset($items) : new CompositeCondition(CompositeCondition::TYPE_AND, $items);
        }

        return ($item instanceof CompositeCondition && $item->type === CompositeCondition::TYPE_AND)
            ? $item->addFromArray($items)
            : new CompositeCondition(CompositeCondition::TYPE_AND, [$item, ...$items]);
    }

    public function mixedCond(
        mixed $cond,
        ExpressionBuilderInterface $exprBuilder
    ): string|ExpressionInterface|ConditionInterface {
        try {
            /** @psalm-suppress MixedArgument */
            return $this->cond($cond, null, $exprBuilder);
        } catch (TypeError $e) {
            throw new InvalidExpressionFormatException(previous: $e);
        }
    }

    public function orCond(
        string|ExpressionInterface|ConditionInterface $cond1,
        string|ExpressionInterface|ConditionInterface|array $cond2,
        ?array $params,
        ExpressionBuilderInterface $exprBuilder
    ): CompositeCondition {
        if ($params) {
            $cond2 = new ParamsCondition($this->mixedCond($cond2, $exprBuilder), $params);
        }

        return $this->compositeCond(CompositeCondition::TYPE_OR, $cond1, $cond2, $exprBuilder);
    }

    public function setRealtimeCondBuilding(bool $value): bool
    {
        $old = $this->realtimeCondBuilding;
        $this->realtimeCondBuilding = $value;

        return $old;
    }

    /**
     * @param array<class-string<ConditionInterface>> $supportedConditions
     * @return $this
     */
    public function setSupportedConditions(array $supportedConditions): self
    {
        $this->supportedConditions = [...$this->supportedConditions, ...$supportedConditions];

        return $this;
    }

    /**
     * @psalm-param CompositeCondition::TYPE_* $type
     */
    private function compositeCond(
        string $type,
        string|ExpressionInterface|ConditionInterface $cond1,
        string|ExpressionInterface|ConditionInterface|array $cond2,
        ExpressionBuilderInterface $exprBuilder
    ): CompositeCondition {
        if ($cond1 instanceof CompositeCondition && $cond1->type === $type) {
            return $cond1->addFromArray($this->prepareCondEx($cond2, $exprBuilder, $type));
        }

        return new CompositeCondition($type, [
            ...$this->prepareCondEx($cond1, $exprBuilder),
            ...$this->prepareCondEx($cond2, $exprBuilder, $type)
        ]);
    }

    /**
     * @psalm-template T
     * @psalm-param array{0: CompositeCondition::TYPE_AND|CompositeCondition::TYPE_OR} $cond
     * @psalm-param T $parentCompType
     * @return array<string|ExpressionInterface|ConditionInterface>
     * @return-psalm (T is null ? array{0: CompositeCondition} : array<string|ExpressionInterface|ConditionInterface>)
     */
    private function prepareCompositeCond(
        array $cond,
        ExpressionBuilderInterface $exprBuilder,
        ?string $parentCompType,
    ): array {
        $type = array_shift($cond);
        /** @psalm-var array $cond */

        if (empty($cond)) {
            throw new InvalidExpressionFormatException();
        }

        if (count($cond) === 1) {
            try {
                /** @psalm-suppress MixedArgument */
                $cond = $this->prepareCondEx(reset($cond), $exprBuilder, $type);
            } catch (TypeError $e) {
                throw new InvalidExpressionFormatException(previous: $e);
            }
        } else {
            $condArr = [];
            try {
                foreach ($cond as $value) {
                    /** @psalm-suppress MixedArgument */
                    $condArr[] = $this->prepareCondEx($value, $exprBuilder, $type);
                }
            } catch (TypeError $e) {
                throw new InvalidExpressionFormatException(previous: $e);
            }
            $cond = array_merge(...$condArr);
        }

        return $parentCompType === $type ? $cond : [new CompositeCondition($type, $cond)];
    }

    /**
     * @return array<string|ExpressionInterface|ConditionInterface>
     */
    private function prepareCondArr(
        array $cond,
        ExpressionBuilderInterface $exprBuilder,
        string $parentCompType = null
    ): array {
        $firstKey = array_key_first($cond);

        if ($firstKey === 0 && is_string($condName = $cond[0])) {
            /** @psalm-var array{0: string} $cond */
            if ($condName === CompositeCondition::TYPE_AND || $condName === CompositeCondition::TYPE_OR) {
                /** @psalm-var array{0: CompositeCondition::TYPE_AND|CompositeCondition::TYPE_OR} $cond */
                // List format conditions ( ['and', 'x', 5] )
                return $this->prepareCompositeCond($cond, $exprBuilder, $parentCompType);
            }

            $condClass = $this->supportedConditions[$condName] ?? null;
            if ($condClass !== null) {
                // List format conditions ( ['val', 'x', '=', 5] )
                $condObj = $condClass::newFromArray($cond, $this, $exprBuilder);
            } elseif (array_is_list($cond) && in_array($cond[1] ?? null, SimpleCondition::SUPPORT_OPERATORS, true)) {
                // List format conditions ( ['x', '=', 5] )
                /** @var array{0: string|ExpressionInterface, 1: string, 2: mixed} $cond */
                $condObj = SimpleCondition::newFromAltArray($cond);
            } else {
                throw new InvalidExpressionFormatException(sprintf('Unknown condition name "%s".', $condName));
            }
        } elseif (is_string($firstKey)) {
            // Arrow format conditions ( ['x' => 5] )
            /** @psalm-suppress MixedArgumentTypeCoercion */
            $condObj = new ArrowCondition($cond);
        } else {
            throw new InvalidExpressionFormatException();
        }

        return [$this->realtimeCondBuilding ? $this->build($condObj, $exprBuilder) : $condObj];
    }

    /**
     * @return array<string|ExpressionInterface|ConditionInterface>
     */
    private function prepareCondEx(
        string|ExpressionInterface|ConditionInterface|array $cond,
        ExpressionBuilderInterface $exprBuilder,
        string $parentCompType = null
    ): array {
        if (empty($cond)) {
            throw new InvalidExpressionFormatException();
        }

        if (!is_array($cond)) {
            // Object or string format conditions
            if (is_string($cond)) {
                $cond = new StringCondition($cond);
            }

            return [$this->realtimeCondBuilding ? $this->build($cond, $exprBuilder) : $cond];
        }

        // Array format conditions
        return $this->prepareCondArr($cond, $exprBuilder, $parentCompType);
    }
}
