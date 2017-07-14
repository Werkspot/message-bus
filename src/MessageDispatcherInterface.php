<?php

namespace Werkspot\MessageBus;

use DateTimeImmutable;
use Werkspot\MessageBus\MessageQueue\Priority;

interface MessageDispatcherInterface
{
    public function dispatchSynchronousMessage($payload, string $destination): void;

    public function dispatchQueuedMessage(
        $payload,
        string $destination,
        DateTimeImmutable $deliverAt = null,
        Priority $priority = null
    ): void;
}
