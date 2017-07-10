<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus\DeliveryChain\Middleware;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Exception\MessageShouldHaveBeenDeliveredException;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\ExceptionThrowingMiddleware;
use Werkspot\MessageBus\Message\MessageInterface;

/**
 * @small
 */
final class ExceptionThrowingMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function exceptionIsAlwaysThrown(): void
    {
        $middleware = new ExceptionThrowingMiddleware();

        $next = function (): void {
            self::fail('Next middleware should never be called');
        };

        $this->expectException(MessageShouldHaveBeenDeliveredException::class);
        $middleware->deliver(Mockery::mock(MessageInterface::class), $next);
    }
}
