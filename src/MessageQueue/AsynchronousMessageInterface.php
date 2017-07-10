<?php

namespace Werkspot\MessageBus\MessageQueue;

use DateTimeInterface;
use Throwable;

interface AsynchronousMessageInterface
{
    const PRIORITY_LOWEST = 1;
    const PRIORITY_HIGHEST = 10;

    public function getDestination(): string;

    /**
     * @return mixed
     */
    public function getPayload();

    public function getPriority(): int;

    public function getDeliverAt(): DateTimeInterface;

    public function getTries(): int;

    public function getErrors(): ?string;

    public function fail(Throwable $error): void;
}
