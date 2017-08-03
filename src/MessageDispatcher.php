<?php

namespace Werkspot\MessageBus;

use DateTimeImmutable;
use Werkspot\MessageBus\Bus\Bus;
use Werkspot\MessageBus\Bus\BusInterface;
use Werkspot\MessageBus\Message\AsynchronousMessage;
use Werkspot\MessageBus\Message\Message;
use Werkspot\MessageBus\Message\MetadataCollectionInterface;
use Werkspot\MessageBus\MessageQueue\Priority;

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

    public function dispatchSynchronousMessage(
        $payload,
        string $destination,
        MetadataCollectionInterface $metadataCollection = null
    ): void {
        $this->bus->deliver(new Message($payload, $destination, $metadataCollection));
    }

    public function dispatchQueuedMessage(
        $payload,
        string $destination,
        MetadataCollectionInterface $metadataCollection = null,
        DateTimeImmutable $deliverAt = null,
        Priority $priority = null
    ): void {
        $this->bus->deliver(new AsynchronousMessage($payload, $destination, $metadataCollection, $deliverAt, $priority));
    }
}
