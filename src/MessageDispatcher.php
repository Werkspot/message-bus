<?php

namespace Werkspot\MessageBus;

use DateTimeImmutable;
use Werkspot\MessageBus\Bus\Bus;
use Werkspot\MessageBus\Bus\BusInterface;
use Werkspot\MessageBus\Message\AsynchronousMessage;
use Werkspot\MessageBus\Message\Message;

final class MessageDispatcher implements MessageDispatcherInterface
{
    /**
     * @var Bus
     */
    private $bus;

    public function __construct(BusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function dispatchSynchronousMessage($payload, string $destination): void
    {
        $this->bus->deliver(new Message($payload, $destination));
    }

    public function dispatchQueuedMessage(
        $payload,
        string $destination,
        DateTimeImmutable $deliverAt = null,
        int $priority = AsynchronousMessage::PRIORITY_LOWEST
    ): void {
        $this->bus->deliver(new AsynchronousMessage($payload, $destination, $deliverAt, $priority));
    }
}
