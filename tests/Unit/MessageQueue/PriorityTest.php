<?php

namespace Werkspot\MessageBus\Test\Unit\MessageQueue;

use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Message\InvalidPriorityException;
use Werkspot\MessageBus\MessageQueue\Priority;

class PriorityTest extends TestCase
{
    /**
     * @test
     * @dataProvider validPriorities
     */
    public function isValid($value): void
    {
        $priority = new Priority($value);
        self::assertSame($value, $priority->toInt());
    }

    /**
     * @test
     * @dataProvider invalidPriorities
     */
    public function isInvalid($value): void
    {
        self::expectException(InvalidPriorityException::class);
        new Priority($value);
    }

    public function validPriorities(): array
    {
        return [
            [Priority::LOWEST],
            [Priority::LOW],
            [Priority::NORMAL],
            [Priority::HIGH],
            [Priority::URGENT],
            [6],
        ];
    }

    public function invalidPriorities(): array
    {
        return [
            [Priority::LOWEST - 1],
            [Priority::URGENT + 1],
            [-1],
            [11],
        ];
    }
}
