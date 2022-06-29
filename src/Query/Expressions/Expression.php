<?php

declare(strict_types=1);

namespace Yii\DBAL\Query\Expressions;

use Yii\DBAL\Contracts\Query\Expressions\ExpressionInterface;

/**
 * @psalm-immutable
 */
final class Expression implements ExpressionInterface
{
    public function __construct(
        private readonly string $rawString,
        private readonly array $params = []
    ) {
    }

    public function __toString(): string
    {
        return $this->rawString;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
