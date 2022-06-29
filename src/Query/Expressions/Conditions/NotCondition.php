<?php

declare(strict_types=1);

namespace Yii\DBAL\Query\Expressions\Conditions;

use Yii\DBAL\Contracts\Query\ConditionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ConditionInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use Yii\DBAL\Exceptions\InvalidExpressionFormatException;

use function count;

final class NotCondition implements ConditionInterface
{
    public function __construct(
        private readonly string|ExpressionInterface|ConditionInterface $cond
    ) {
    }

    public function build(ExpressionBuilderInterface $exprBuilder): ExpressionInterface
    {
        $cond = $this->cond;
        if ($cond instanceof ConditionInterface) {
            $cond = $cond->build($exprBuilder);
        }

        return $exprBuilder->not($cond);
    }

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
