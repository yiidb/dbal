<?php

declare(strict_types=1);

namespace Yii\DBAL\Query\Expressions\Conditions;

use Yii\DBAL\Contracts\Query\ConditionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ConditionInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionBuilderInterface;
use Yii\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use Yii\DBAL\Exceptions\InvalidExpressionFormatException;
use Yii\DBAL\Query\Expressions\Expression;

use function count;
use function is_array;
use function is_string;

final class ParamsCondition implements ConditionInterface
{
    public function __construct(
        private readonly string|ExpressionInterface|ConditionInterface $cond,
        private readonly array $params
    ) {
    }

    public function build(ExpressionBuilderInterface $exprBuilder): string|ExpressionInterface
    {
        $cond = $this->cond;
        if ($cond instanceof ConditionInterface) {
            $cond = $cond->build($exprBuilder);
        }

        if ($params = $this->params) {
            return is_string($cond) || !($exprParams = $cond->getParams())
                ? new Expression((string)$cond, $params)
                : new Expression((string)$cond, [...$exprParams, ...$params]);
        }

        return $cond;
    }

    public static function newFromArray(
        array $arr,
        ConditionBuilderInterface $condBuilder,
        ExpressionBuilderInterface $exprBuilder
    ): static {
        if (count($arr) !== 3 || empty($cond = $arr[1] ?? null) || !is_array($params = $arr[2] ?? null)) {
            throw new InvalidExpressionFormatException();
        }

        return new static($condBuilder->mixedCond($cond, $exprBuilder), $params);
    }
}
