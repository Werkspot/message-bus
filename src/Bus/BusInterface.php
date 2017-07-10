<?php

namespace Werkspot\MessageBus\Bus;

use Werkspot\MessageBus\Message\MessageInterface;

interface BusInterface
{
    public function deliver(MessageInterface ...$messageList): void;
}
