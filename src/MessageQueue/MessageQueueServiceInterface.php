<?php

namespace Werkspot\MessageBus\MessageQueue;

interface MessageQueueServiceInterface
{
    public function enqueueMessage(AsynchronousMessageInterface $message): void;
}
