<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Bus\DeliveryChain;

use Werkspot\MessageBus\Message\MessageInterface;

interface MiddlewareInterface
{
    public function deliver(MessageInterface $message, callable $next): void;
}
