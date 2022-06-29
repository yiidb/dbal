<?php

declare(strict_types=1);

namespace YiiDb\DBAL;

final class EmulatedResult extends BaseResult
{
    public function fetch(): ?array
    {
        return null;
    }

    public function fetchAll(): array
    {
        return [];
    }

    public function fetchAllNumeric(): array
    {
        return [];
    }

    public function fetchAllValues(): array
    {
        return [];
    }

    public function fetchNumeric(): ?array
    {
        return null;
    }

    public function fetchValue(): bool
    {
        return false;
    }

    public function free(): void
    {
    }

    public function getColumnCount(): int
    {
        return 0;
    }

    public function getRowCount(): int
    {
        return 0;
    }
}
