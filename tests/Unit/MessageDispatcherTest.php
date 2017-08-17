<?php

namespace Werkspot\MessageBus\Test\Unit;

use DateTimeImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
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
     * @dataProvider getMetadata
     */
    public function dispatchQueuedMessage_ShouldDeliverQueuedMessage(array $metadata): void
    {
        $payload = 'payload';
        $destination = 'destination';
        $deliverAt = new DateTimeImmutable('2017-10-08');
        $priority = new Priority(Priority::PRIORITY_MEDIUM);

        $this->bus->shouldReceive('deliver')
            ->once()
            ->with(
                WithMessage::equalToMessageWithoutComparingDatesNorErrors(
                    new AsynchronousMessage($payload, $destination, $metadata, $deliverAt, $priority)
                )
            );

        $this->messageDispatcher->dispatchQueuedMessage($payload, $destination, $metadata, $deliverAt, $priority);
    }

    /**
     * @test
     * @dataProvider getMetadata
     */
    public function dispatchSynchronousMessage_ShouldDeliverSynchronousMessage(array $metadata): void
    {
        $payload = 'payload';
        $destination = 'destination';
        $message = new Message($payload, $destination, $metadata);
        $this->bus->shouldReceive('deliver')
            ->once()
            ->with(WithMessage::equalToSynchronousMessage($message));

        $this->messageDispatcher->dispatchSynchronousMessage($payload, $destination, $metadata);
    }

    public function getMetadata(): array
    {
        return [
            [[]],
            [[new stdClass()]],
        ];
    }
}
