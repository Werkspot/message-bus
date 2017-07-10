<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus\DeliveryChain\Middleware;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\LockingMiddleware;
use Werkspot\MessageBus\Message\MessageInterface;

final class LockingMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function executeShouldEnsureThatMessagesAreStackedAndProcessedInTheRightOrder(): void
    {
        $middleware = new LockingMiddleware();
        $processSecondMessage = false;
        $secondMessageProcessed = false;

        $second = function () use (&$secondMessageProcessed, &$processSecondMessage) {
            if (!$processSecondMessage) {
                self::fail('Second message executed before the first completed');
            }

            $secondMessageProcessed = true;
        };

        $first = function () use (&$processSecondMessage, $second, $middleware) {
            $middleware->deliver(Mockery::mock(MessageInterface::class), $second);
            $processSecondMessage = true;
        };

        $middleware->deliver(Mockery::mock(MessageInterface::class), $first);

        self::assertTrue($secondMessageProcessed);
    }
}
