<?php

namespace Werkspot\MessageBus;

use DateTimeImmutable;
use Werkspot\MessageBus\MessageQueue\PriorityEnum;

interface MessageDispatcherInterface
{
    public function dispatchSynchronousMessage($payload, string $destination): void;

    public function dispatchQueuedMessage(
        $payload,
        string $destination,
        DateTimeImmutable $deliverAt = null,
        PriorityEnum $priority = null
    ): void;
}
