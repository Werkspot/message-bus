<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus\DeliveryChain;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Werkspot\Command\Commands\CommandInterface;
use Werkspot\Command\Handler\CommandHandlerInterface;
use Werkspot\MessageBus\Bus\DeliveryChain\DeliveryMiddleware;
use Werkspot\MessageBus\Bus\Handler\MessageHandlerFactoryInterface;
use Werkspot\MessageBus\Message\Message;

final class DeliveryMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var MockInterface|MessageHandlerFactoryInterface
     */
    private $handlerFactory;

    /**
     * @before
     */
    public function createDependencies(): void
    {
        $this->handlerFactory = Mockery::mock(MessageHandlerFactoryInterface::class);
    }

    /**
     * @test
     */
    public function executeShouldFindTheCorrectHandlerProcessTheCommandAndExitTheBus(): void
    {
        $command = $this->createCommand();
        $message = new Message($command, 'dummy destination');

        $next = function (): void {
            self::fail('Next middleware should never be called');
        };

        $handler = Mockery::mock(CommandHandlerInterface::class);
        $handler->shouldReceive('handle')
            ->once()
            ->with($command);

        $this->handlerFactory->shouldReceive('getHandler')
            ->once()
            ->with($message)
            ->andReturn([$handler, 'handle']);

        $middleware = new DeliveryMiddleware($this->handlerFactory);
        $middleware->deliver($message, $next);
    }

    private function createCommand(): CommandInterface
    {
        $command = Mockery::mock(CommandInterface::class);
        $command->shouldReceive('getCommandName')->andReturn('testing');

        return $command;
    }
}
