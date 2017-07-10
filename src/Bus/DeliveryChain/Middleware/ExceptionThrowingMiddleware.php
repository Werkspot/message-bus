<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Bus\DeliveryChain\Middleware;

use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Exception\MessageShouldHaveBeenDeliveredException;
use Werkspot\MessageBus\Bus\DeliveryChain\MiddlewareInterface;
use Werkspot\MessageBus\Message\MessageInterface;

/**
 * This middleware can be used to make sure every message we deliver is handled by a handler.
 *
 * During development I had a bug in the Async handler that would do some check on the message it got and skip it if
 * the deliver_at was in the past.
 *
 * So in the QueuedCommand table I put the deliver_at at now(), but then the async handler would skip it, and not
 * a single execution middleware would handle it. So without any error it was removed from the queue and never delivered.
 * This is obviously a very big problem.
 *
 * To prevent it I put this middleware at the end of the chain to prevent this.
 *
 * It's a bit defensive, but it's better then having messages not delivered without any log/error :(
 */
final class ExceptionThrowingMiddleware implements MiddlewareInterface
{
    public function deliver(MessageInterface $message, callable $next): void
    {
        throw new MessageShouldHaveBeenDeliveredException();
    }
}
