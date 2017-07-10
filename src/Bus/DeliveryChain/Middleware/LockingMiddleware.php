<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Bus\DeliveryChain\Middleware;

use Exception;
use Werkspot\MessageBus\Bus\DeliveryChain\MiddlewareInterface;
use Werkspot\MessageBus\Message\MessageInterface;

/**
 * This middleware isolates the execution of subsequent commands
 * so we that they don't interfere on the execution of the
 * current command
 */
final class LockingMiddleware implements MiddlewareInterface
{
    /**
     * @var bool
     */
    private $executing = false;

    /**
     * @var callable[]
     */
    private $queue = [];

    public function deliver(MessageInterface $message, callable $next): void
    {
        $this->enqueue($message, $next);

        if ($this->executing) {
            return;
        }

        try {
            $this->executing = true;
            $this->processQueue();
        } catch (Exception $e) {
            $this->queue = [];
            throw $e;
        } finally {
            $this->executing = false;
        }
    }

    /**
     * Queues the execution of the next message instead of executing it
     * when it's added to the message bus
     */
    private function enqueue(MessageInterface $message, callable $next): void
    {
        $this->queue[] = function () use ($message, $next): void {
            $next($message);
        };
    }

    /**
     * Process one message at time so we can isolate their execution
     */
    private function processQueue(): void
    {
        while ($resumeMessage = array_shift($this->queue)) {
            $resumeMessage();
        }
    }
}
