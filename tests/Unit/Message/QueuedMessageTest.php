<?php

namespace Werkspot\MessageBus\Test\Unit\Message;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Message\AsynchronousMessage;

final class QueuedMessageTest extends TestCase
{
    /**
     * @test
     */
    public function fail_ShouldUpdateErrorListTiesAndDequeueDateTime(): void
    {
        $payload = 'foo';
        $message = new AsynchronousMessage($payload, 'dummy destination');

        $errorMessage = 'some error message';

        $message->fail(new Exception($errorMessage));

        self::assertContains($errorMessage, $message->getErrors());
        self::assertEquals(1, $message->getTries());
        self::assertGreaterThan(new DateTime(), $message->getDeliverAt());
    }
}
