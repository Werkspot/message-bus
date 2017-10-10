<?php

namespace Werkspot\MessageBus\Test\Unit\Message;

use DateTime;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Werkspot\MessageBus\Message\AsynchronousMessage;
use Werkspot\MessageBus\MessageQueue\Priority;

final class AsynchronousMessageTest extends TestCase
{
    /**
     * @test
     */
    public function constructor_withMinimalParameters(): void
    {
        $payload = 'some payload';
        $destination = 'dummy destination';

        $message = new AsynchronousMessage($payload, $destination);

        self::assertSame($payload, $message->getPayload());
        self::assertSame($destination, $message->getDestination());

        self::assertSame(time(), $message->getCreatedAt()->getTimestamp(), 2);
        self::assertSame(time(), $message->getDeliverAt()->getTimestamp(), 2);
        self::assertNull($message->getUpdatedAt());

        self::assertEmpty($message->getMetaData());
        self::assertEquals(new Priority(Priority::NORMAL), $message->getPriority());
        self::assertTrue(Uuid::isValid($message->getId()));
        self::assertTrue((Uuid::fromString($message->getId()))->getVersion() == 4);
    }

    /**
     * @test
     */
    public function constructor_withAllParameters(): void
    {
        $payload = 'some payload';
        $destination = 'dummy destination';
        $metaData = ['foo' => 'bar', 'baz' => 'zab'];
        $deliverAt = new DateTimeImmutable('2199-01-01 10:00');
        $priority = new Priority(Priority::HIGH);

        $message = new AsynchronousMessage($payload, $destination, $metaData, $deliverAt, $priority);

        self::assertSame($payload, $message->getPayload());
        self::assertSame($destination, $message->getDestination());
        self::assertSame($metaData, $message->getMetadata());
        self::assertSame($deliverAt, $message->getDeliverAt());
        self::assertEquals($priority, $message->getPriority());
    }

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
