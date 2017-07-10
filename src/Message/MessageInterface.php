<?php

namespace Werkspot\MessageBus\Message;

interface MessageInterface
{
    public function getPayload();

    public function getDestination(): string;
}
