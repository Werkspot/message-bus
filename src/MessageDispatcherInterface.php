<?php

namespace Werkspot\MessageBus;

use DateTimeImmutable;
use Werkspot\MessageBus\Message\MetadataCollectionInterface;
use Werkspot\MessageBus\MessageQueue\Priority;

interface MessageDispatcherInterface
{
    public function dispatchSynchronousMessage(
        $payload,
        string $destination,
        MetadataCollectionInterface $metadataCollection = null
    ): void;

    public function dispatchQueuedMessage(
        $payload,
        string $destination,
        MetadataCollectionInterface $metadataCollection = null,
        DateTimeImmutable $deliverAt = null,
        Priority $priority = null
    ): void;
}
