<?php

namespace Werkspot\MessageBus\Bus\Handler;

use Werkspot\MessageBus\Message\MessageInterface;

interface MessageHandlerFactoryInterface
{
    public function getHandler(MessageInterface $message): array;
}
