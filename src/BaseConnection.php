<?php

declare(strict_types=1);

namespace YiiDb\DBAL;

use Closure;
use Throwable;
use YiiDb\DBAL\Contracts\ConnectionInterface;
use YiiDb\DBAL\Contracts\Query\ConditionBuilderInterface;
use YiiDb\DBAL\Query\Expressions\SimpleConditionBuilder;

abstract class BaseConnection implements ConnectionInterface
{
    public bool $inlineParams;
    public readonly bool $useNamedParams;

    private readonly ConditionBuilderInterface $condBuilder;

    public function __construct(
        bool $useNamedParams = false,
        bool $inlineParams = false,
        bool $realtimeCondBuilding = false,
        ConditionBuilderInterface $condBuilder = null
    ) {
        $this->useNamedParams = $useNamedParams;
        $this->inlineParams = $inlineParams;
        $this->condBuilder = $condBuilder ?? new SimpleConditionBuilder($realtimeCondBuilding);
    }

    public function getConditionBuilder(): ConditionBuilderInterface
    {
        return $this->condBuilder;
    }

    public function isUseNamedParams(): bool
    {
        return $this->useNamedParams;
    }

    public function transactional(Closure $func): mixed
    {
        $this->beginTransaction();
        try {
            $result = $func($this);

            $this->commit();

            return $result;
        } catch (Throwable $e) {
            $this->rollBack();

            throw $e;
        }
    }

    public function wrapSql(string $sql): string
    {
        return preg_replace_callback(
            '/(\\{\\{(%?[^{}]+%?)}}|\\[\\[([^[\]]+)]])/',
            fn (array $matches): string => isset($matches[3])
                ? $this->wrapSingle($matches[3])
                : str_replace('%', '', $this->wrap($matches[2])),
            $sql
        );
    }
}
