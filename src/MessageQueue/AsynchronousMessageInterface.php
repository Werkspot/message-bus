<?php

namespace Werkspot\MessageBus\MessageQueue;

use DateTimeImmutable;
use Throwable;

interface AsynchronousMessageInterface
{
    public function getDestination(): string;

    /**
     * @return mixed
     */
    public function getPayload();

    public function getPriority(): Priority;

    public function getDeliverAt(): DateTimeImmutable;

    public function getTries(): int;

    public function getErrors(): ?string;

    public function fail(Throwable $error): void;
}
