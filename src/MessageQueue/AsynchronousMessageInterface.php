<?php

namespace Werkspot\MessageBus\MessageQueue;

use DateTimeImmutable;
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

    public function getDeliverAt(): DateTimeImmutable;

    public function getTries(): int;

    public function getErrors(): ?string;

    public function fail(Throwable $error): void;
}
