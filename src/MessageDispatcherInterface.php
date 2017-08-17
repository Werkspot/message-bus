<?php

namespace Werkspot\MessageBus;

use DateTimeImmutable;
use Werkspot\MessageBus\MessageQueue\Priority;

interface MessageDispatcherInterface
{
    public function dispatchSynchronousMessage(
        $payload,
        string $destination,
        array $metadataCollection = []
    ): void;

    public function dispatchQueuedMessage(
        $payload,
        string $destination,
        array $metadataCollection = [],
        DateTimeImmutable $deliverAt = null,
        Priority $priority = null
    ): void;
}
