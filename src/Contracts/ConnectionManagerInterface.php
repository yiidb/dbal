<?php

declare(strict_types=1);

namespace YiiDb\DBAL\Contracts;

interface ConnectionManagerInterface
{
    public function getConnection(string $name = null): ConnectionInterface;

    public function resetConnection(string $name): void;

    public function resetConnections(): void;
}
