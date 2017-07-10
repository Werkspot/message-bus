<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Bus\DeliveryChain;

use Werkspot\MessageBus\Bus\Handler\MessageHandlerFactoryInterface;
use Werkspot\MessageBus\Message\MessageInterface;

final class DeliveryMiddleware implements MiddlewareInterface
{
    /**
     * @var MessageHandlerFactoryInterface
     */
    private $handlerFactory;

    public function __construct(MessageHandlerFactoryInterface $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    public function deliver(MessageInterface $message, callable $next): void
    {
        $handler = $this->handlerFactory->getHandler($message);

        $handler($message->getPayload());
    }
}
