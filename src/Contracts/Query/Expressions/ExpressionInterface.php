<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query\Expressions;

use Stringable;

/**
 * @psalm-immutable
 */
interface ExpressionInterface extends Stringable
{
    public function __toString(): string;

    public function getParams(): array;
}
