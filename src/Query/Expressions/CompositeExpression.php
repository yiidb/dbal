<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Query\Expressions;

use YiiDb\DBAL\Contracts\Query\Expressions\ExpressionInterface;

use function count;

/**
 * @psalm-immutable
 */
final class CompositeExpression implements ExpressionInterface
{
    public const TYPE_AND = 'AND';
    public const TYPE_OR = 'OR';

    /**
     * @psalm-var self::TYPE_*
     */
    public readonly string $type;

    private readonly bool $onePart;
    private readonly array $params;
    private readonly string $rawString;

    /**
     * @psalm-param self::TYPE_* $type
     */
    private function __construct(string $type, ?self $parent, string|ExpressionInterface ...$parts)
    {
        $this->type = $type;

        if ($parent !== null) {
            $this->onePart = false;
            $this->rawString = "$parent $this->type (" . implode(") $this->type (", $parts) . ')';
            $this->params = self::mergeParams($parts, $parent->getParams());
        } elseif (empty($parts)) {
            $this->onePart = false;
            $this->rawString = '';
            $this->params = [];
        } elseif (count($parts) === 1) {
            $this->onePart = true;
            $this->rawString = (string)($part = reset($parts));
            $this->params = $part instanceof ExpressionInterface ? $part->getParams() : [];
        } else {
            $this->onePart = false;
            $this->rawString = '(' . implode(") $this->type (", $parts) . ')';
            $this->params = self::mergeParams($parts);
        }
    }

    public static function and(string|ExpressionInterface ...$parts): self
    {
        return new self(self::TYPE_AND, null, ...$parts);
    }

    /**
     * @psalm-pure
     * @param array<string|ExpressionInterface> $parts
     * @return array<string|ExpressionInterface>
     */
    public static function filterEmptyParts(array $parts): array
    {
        return array_filter($parts, static fn (string|ExpressionInterface $p): bool => trim((string)$p) !== '');
    }

    public static function or(string|ExpressionInterface ...$parts): self
    {
        return new self(self::TYPE_OR, null, ...$parts);
    }

    private static function mergeParams(array $parts, array $parentParams = null): array
    {
        $paramsArr = empty($parentParams) ? [] : [$parentParams];

        foreach ($parts as $part) {
            if (($part instanceof ExpressionInterface) && !empty($params = $part->getParams())) {
                $paramsArr[] = $params;
            }
        }

        return empty($paramsArr) ? [] : array_merge(...$paramsArr);
    }

    public function __debugInfo(): array
    {
        return ['type' => $this->type, 'rawString' => $this->rawString];
    }

    public function __toString(): string
    {
        return $this->rawString;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function with(string|ExpressionInterface ...$parts): self
    {
        if (empty($parts)) {
            return $this;
        }

        return $this->onePart
            ? new self($this->type, null, $this, ...$parts)
            : new self($this->type, empty($this->rawString) ? null : $this, ...$parts);
    }

    public function withAnd(string|ExpressionInterface ...$parts): self
    {
        return $this->type === self::TYPE_AND
            ? $this->with(...$parts)
            : new self(self::TYPE_AND, null, $this, ...$parts);
    }

    public function withOr(string|ExpressionInterface ...$parts): self
    {
        return $this->type === self::TYPE_OR
            ? $this->with(...$parts)
            : new self(self::TYPE_OR, null, $this, ...$parts);
    }
}
