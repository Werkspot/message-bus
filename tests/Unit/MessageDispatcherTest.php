<?php

namespace Werkspot\MessageBus\Test\Unit;

use DateTimeImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Bus\BusInterface;
use Werkspot\MessageBus\Message\AsynchronousMessage;
use Werkspot\MessageBus\Message\Message;
use Werkspot\MessageBus\MessageDispatcher;
use Werkspot\MessageBus\MessageQueue\Priority;
use Werkspot\MessageBus\Test\WithMessage;

final class MessageDispatcherTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface|BusInterface
     */
    private $bus;

    /**
     * @var MessageDispatcher
     */
    private $messageDispatcher;

    public function setUp(): void
    {
        $this->bus = Mockery::mock(BusInterface::class);
        $this->messageDispatcher = new MessageDispatcher($this->bus);
    }

    /**
     * @test
     */
    public function dispatchQueuedMessage_ShouldDeliverQueuedMessage(): void
    {
        $payload = 'payload';
        $destination = 'destination';
        $deliverAt = new DateTimeImmutable('2017-10-08');
        $priority = new Priority(Priority::PRIORITY_MEDIUM);

        $this->bus->shouldReceive('deliver')
            ->once()
            ->with(
                WithMessage::equalToMessageWithoutComparingDatesNorErrors(
                    new AsynchronousMessage($payload, $destination, $deliverAt, $priority)
                )
            );

        $this->messageDispatcher->dispatchQueuedMessage($payload, $destination, $deliverAt, $priority);
    }

    /**
     * @test
     */
    public function dispatchSynchronousMessage_ShouldDeliverSynchronousMessage(): void
    {
        $payload = 'payload';
        $destination = 'destination';
        $message = new Message($payload, $destination);
        $this->bus->shouldReceive('deliver')
            ->once()
            ->with(WithMessage::equalToSynchronousMessage($message));

        $this->messageDispatcher->dispatchSynchronousMessage($payload, $destination);
    }
}
