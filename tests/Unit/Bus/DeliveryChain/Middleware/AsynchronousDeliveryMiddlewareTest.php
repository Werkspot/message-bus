<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus\DeliveryChain\Middleware;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\AsynchronousDeliveryMiddleware;
use Werkspot\MessageBus\Message\AsynchronousMessage;
use Werkspot\MessageBus\Message\Message;
use Werkspot\MessageBus\Message\MessageInterface;
use Werkspot\MessageBus\MessageQueue\MessageQueueServiceInterface;

final class AsynchronousDeliveryMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface|MessageQueueServiceInterface
     */
    private $messageQueueService;

    /**
     * @before
     */
    public function createDependencies(): void
    {
        $this->messageQueueService = Mockery::mock(MessageQueueServiceInterface::class);
    }

    /**
     * @test
     */
    public function executeShouldProcessNonQueueableMessages(): void
    {
        $called = false;
        $next = function () use (&$called): void {
            $called = true;
        };

        $middleware = new AsynchronousDeliveryMiddleware($this->messageQueueService);
        $middleware->deliver(Mockery::mock(MessageInterface::class), $next);

        self::assertTrue($called);
    }

    /**
     * @test
     */
    public function executeShouldProcessMessagesMarkedAsImmediateDelivery(): void
    {
        $called = false;
        $next = function () use (&$called): void {
            $called = true;
        };

        $middleware = new AsynchronousDeliveryMiddleware($this->messageQueueService);
        $middleware->deliver(new Message('payload', 'dummy destination'), $next);

        self::assertTrue($called);
    }

    /**
     * @test
     */
    public function executeShouldUseTheMessageQueueServiceToAddMessagesToTheQueue(): void
    {
        $next = function (): void {
            self::fail('Next middleware should never be called');
        };

        $message = new AsynchronousMessage('payload', 'dummy destination');

        $this->messageQueueService->shouldReceive('enqueueMessage')->once()->with($message);

        $middleware = new AsynchronousDeliveryMiddleware($this->messageQueueService);
        $middleware->deliver($message, $next);
    }
}
