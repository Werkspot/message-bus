<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus\DeliveryChain\Middleware;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\OrmTransactionMiddleware;
use Werkspot\MessageBus\Message\MessageInterface;

final class OrmTransactionMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var EntityManagerInterface|Mockery\MockInterface
     */
    private $entityManager;

    /**
     * @var Connection|Mockery\MockInterface
     */
    private $connection;

    /**
     * @before
     */
    public function createDependencies(): void
    {
        $this->connection = Mockery::mock(Connection::class);
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);

        $this->entityManager->shouldReceive('getConnection')->andReturn($this->connection);
    }

    /**
     * @test
     */
    public function executeShouldCommitTheTransactionWhenNoExceptionWasRaised(): void
    {
        $called = false;
        $next = function () use (&$called): void {
            $called = true;
        };

        $this->connection->shouldReceive('isTransactionActive')
            ->once()
            ->andReturn(false);

        $this->entityManager->shouldReceive('beginTransaction')->once();
        $this->entityManager->shouldReceive('flush')->once();
        $this->entityManager->shouldReceive('commit')->once();
        $this->entityManager->shouldReceive('rollback')->never();
        $this->entityManager->shouldReceive('clear')->once();

        $middleware = new OrmTransactionMiddleware($this->entityManager);
        $middleware->deliver(Mockery::mock(MessageInterface::class), $next);

        self::assertTrue($called);
    }

    /**
     * @test
     */
    public function executeShouldJustHandleTheTransactionForTheFirstCommand(): void
    {
        $called = false;
        $middleware = new OrmTransactionMiddleware($this->entityManager);

        $second = function () use (&$called): void {
            $called = true;
        };

        $first = function () use ($second, $middleware): void {
            $middleware->deliver(Mockery::mock(MessageInterface::class), $second);
        };

        $this->connection->shouldReceive('isTransactionActive')
            ->times(2)
            ->andReturnValues([false, true]);

        $this->entityManager->shouldReceive('beginTransaction')->once();
        $this->entityManager->shouldReceive('flush')->once();
        $this->entityManager->shouldReceive('commit')->once();
        $this->entityManager->shouldReceive('rollback')->never();
        $this->entityManager->shouldReceive('clear')->once();

        $middleware->deliver(Mockery::mock(MessageInterface::class), $first);

        self::assertTrue($called);
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function executeShouldRollbackTheTransactionWhenAnExceptionWasRaised(): void
    {
        $middleware = new OrmTransactionMiddleware($this->entityManager);

        $second = function (): void {
            throw new InvalidArgumentException();
        };

        $first = function () use ($second, $middleware): void {
            $middleware->deliver(Mockery::mock(MessageInterface::class), $second);
        };

        $this->connection->shouldReceive('isTransactionActive')
            ->times(2)
            ->andReturnValues([false, true]);

        $this->entityManager->shouldReceive('beginTransaction')->once();
        $this->entityManager->shouldReceive('flush')->never();
        $this->entityManager->shouldReceive('commit')->never();
        $this->entityManager->shouldReceive('rollback')->once();
        $this->entityManager->shouldReceive('clear')->once();

        $middleware->deliver(Mockery::mock(MessageInterface::class), $first);
    }
}
