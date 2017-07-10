<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus\DeliveryChain\Middleware\Validation;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\MessageViolationException;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\MessageViolationListException;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\ValidationMiddleware;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\ValidatorInterface;
use Werkspot\MessageBus\Message\Message;

final class ValidationMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ValidatorInterface|MockInterface
     */
    private $validator;

    /**
     * @before
     */
    public function createDependencies(): void
    {
        $this->validator = Mockery::mock(ValidatorInterface::class);
    }

    /**
     * @test
     */
    public function executeShouldProcessNextMiddlewareWhenValidatorDidNotFindAnyError(): void
    {
        $message = new Message('payload', 'dummy destination');
        $called = false;

        $next = function () use (&$called): void {
            $called = true;
        };

        $this->validator->shouldReceive('validate')
            ->once()
            ->with($message)
            ->andReturn(new MessageViolationListException());

        $middleware = new ValidationMiddleware($this->validator);
        $middleware->deliver($message, $next);

        self::assertTrue($called);
    }

    /**
     * @test
     *
     * @expectedException \Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\MessageViolationListException
     */
    public function executeShouldRaiseExceptionWhenValidatorFoundErrors(): void
    {
        $message = new Message('payload', 'dummy destination');

        $next = function (): void {
            self::fail('Next middleware should never be called');
        };

        $violationList = new MessageViolationListException();
        $violationList->add(new MessageViolationException('a', 'b', []));

        $this->validator->shouldReceive('validate')
            ->once()
            ->with($message)
            ->andReturn($violationList);

        $middleware = new ValidationMiddleware($this->validator);
        $middleware->deliver($message, $next);
    }
}
