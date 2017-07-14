<?php

namespace Werkspot\MessageBus\Test\Unit;

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
            [Priority::PRIORITY_LOWEST],
            [Priority::PRIORITY_LOW],
            [Priority::PRIORITY_MEDIUM],
            [Priority::PRIORITY_HIGH],
            [Priority::PRIORITY_HIGHEST],
            [0],
            [10],
            [6],
        ];
    }

    public function invalidPriorities(): array
    {
        return [
            [Priority::PRIORITY_LOWEST - 1],
            [Priority::PRIORITY_HIGHEST + 1],
            [-1],
            [11],
        ];
    }
}
