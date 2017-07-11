<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Werkspot\MessageBus\Bus\Bus;
use Werkspot\MessageBus\Bus\DeliveryChain\MiddlewareInterface;
use Werkspot\MessageBus\Message\Message;
use Werkspot\MessageBus\Message\MessageInterface;

final class BusTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function deliverShouldDeliverAllItemsInTheMiddlewareChain(): void
    {
        $destination = 'testing';
        $this->expectOutputString($destination . '1' . PHP_EOL . $destination . '2' . PHP_EOL);

        $middleware1 = $this->createMiddleware(1);
        $middleware2 = $this->createMiddleware(2);
        $command = $this->createCommand();

        $message = new Message(serialize($command), $destination);

        $bus = Bus::fromMiddlewareList($middleware1, $middleware2);
        $bus->deliver($message);
    }

    /**
     * @test
     */
    public function deliverShouldNotGroupMultipleCommands(): void
    {
        $destination1 = 'destination1_';
        $destination2 = 'destination2_';
        $this->expectOutputString($destination1 . '1' . PHP_EOL . $destination2 . '1' . PHP_EOL);

        $middleware = $this->createMiddleware();
        $command = $this->createCommand();

        $message1 = new Message(serialize($command), $destination1);
        $message2 = new Message(serialize($command), $destination2);

        $bus = Bus::fromMiddlewareList($middleware);
        $bus->deliver($message1, $message2);
    }

    private function createMiddleware(int $id = 1): MiddlewareInterface
    {
        return new class($id) implements MiddlewareInterface {
            /**
             * @var int
             */
            private $id;

            public function __construct(int $id)
            {
                $this->id = $id;
            }

            public function deliver(MessageInterface $message, callable $next): void
            {
                echo $message->getDestination(), $this->id, PHP_EOL;

                $next($message);
            }
        };
    }

    private function createCommand(): MockInterface
    {
        $command = Mockery::mock(stdClass::class);
        $command->shouldReceive('getCommandName')->andReturn('testing');

        return $command;
    }
}
