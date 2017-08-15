<?php

namespace Werkspot\MessageBus\MessageQueue;

use Werkspot\MessageBus\Message\InvalidPriorityException;

final class Priority
{
    const LOWEST = 1;
    const LOW = 3;
    const NORMAL = 5;
    const HIGH = 7;
    const URGENT = 9;

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
        return self::LOWEST <= $priority && $priority <= self::URGENT;
    }
}
