<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts\Query\SelectQuery;

use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;
use YiiDb\DBAL\Contracts\Query\QueryWithWhereInterface;

interface JoinInterface extends QueryWithWhereInterface
{
    /**
     * @return $this
     */
    public function andOn(string|ExpressionInterface|array $on, array $params = null): static;

    /**
     * @return $this
     */
    public function on(string|ExpressionInterface|array|null $on, array $params = null): static;

    /**
     * @return $this
     */
    public function orOn(string|ExpressionInterface|array $on, array $params = null): static;
}
