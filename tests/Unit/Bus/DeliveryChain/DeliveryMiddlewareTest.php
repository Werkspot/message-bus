<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus\DeliveryChain;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Werkspot\MessageBus\Bus\DeliveryChain\DeliveryMiddleware;
use Werkspot\MessageBus\Bus\Handler\MessageHandlerFactoryInterface;
use Werkspot\MessageBus\Message\Message;
use Werkspot\MessageBus\Message\MetadataCollectionInterface;

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
     * @dataProvider getMetadata
     */
    public function executeShouldFindTheCorrectHandlerProcessTheCommandAndExitTheBus(
        ?MetadataCollectionInterface $collection
    ): void {
        $command = $this->createCommand();
        $message = new Message($command, 'dummy destination', $collection);

        $next = function (): void {
            self::fail('Next middleware should never be called');
        };

        $handler = Mockery::mock(stdClass::class);
        $handler->shouldReceive('handle')
            ->once()
            ->with($command, $message->getMetadata());

        $this->handlerFactory->shouldReceive('getHandler')
            ->once()
            ->with($message)
            ->andReturn([$handler, 'handle']);

        $middleware = new DeliveryMiddleware($this->handlerFactory);
        $middleware->deliver($message, $next);
    }

    private function createCommand(): MockInterface
    {
        $command = Mockery::mock(stdClass::class);
        $command->shouldReceive('getCommandName')->andReturn('testing');

        return $command;
    }

    public function getMetadata(): array
    {
        return [
            [null],
            [Mockery::mock(MetadataCollectionInterface::class)],
        ];
    }
}
