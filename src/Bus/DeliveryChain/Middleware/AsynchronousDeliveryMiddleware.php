<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Bus\DeliveryChain\Middleware;

use Werkspot\MessageBus\Bus\DeliveryChain\MiddlewareInterface;
use Werkspot\MessageBus\Message\MessageInterface;
use Werkspot\MessageBus\MessageQueue\AsynchronousMessageInterface;
use Werkspot\MessageBus\MessageQueue\MessageQueueServiceInterface;

final class AsynchronousDeliveryMiddleware implements MiddlewareInterface
{
    /**
     * @var MessageQueueServiceInterface
     */
    private $messageQueueService;

    public function __construct(MessageQueueServiceInterface $messageQueueService)
    {
        $this->messageQueueService = $messageQueueService;
    }

    public function deliver(MessageInterface $message, callable $next): void
    {
        if ($message instanceof AsynchronousMessageInterface) {
            // By default we 'queue' all QueuedMessage, even if the execute_at is in the past.
            // We do this  so that we support transactions and can rollback the execution if something goes wrong in
            // another part of the transaction.
            // If we were to send it to rabbit in the Async middleware, and further on an exception occurs, if would
            // already have been processed using rabbit, and there's no way to roll it back.
            $this->messageQueueService->enqueueMessage($message);
            return;
        }

        $next($message);
    }
}
