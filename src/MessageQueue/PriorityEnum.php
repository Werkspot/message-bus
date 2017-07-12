<?php

namespace Werkspot\MessageBus\MessageQueue;

use Werkspot\MessageBus\Message\InvalidPriorityException;

final class PriorityEnum
{
    const PRIORITY_LOWEST = 0;
    const PRIORITY_LOW = 2;
    const PRIORITY_MEDIUM = 5;
    const PRIORITY_HIGH = 8;
    const PRIORITY_HIGHEST = 10;

    /**
     * @var int
     */
    private $value;

    public function __construct(int $priority)
    {
        if (!$this->isValid($priority)) {
            throw new InvalidPriorityException("Invalid priority: {$priority}.");
        }

        $this->value = $priority;
    }

    public function toInt(): int
    {
        return $this->value;
    }

    private function isValid(int $priority): bool
    {
        return self::PRIORITY_LOWEST <= $priority && $priority <= self::PRIORITY_HIGHEST;
    }
}
