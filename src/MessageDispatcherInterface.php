<?php

namespace Werkspot\MessageBus;

use DateTimeInterface;

interface MessageDispatcherInterface
{
    public function dispatchSynchronousMessage($payload, string $destination): void;

    public function dispatchQueuedMessage(
        $payload,
        string $destination,
        DateTimeInterface $deliverAt = null,
        int $priority = 0
    ): void;
}
