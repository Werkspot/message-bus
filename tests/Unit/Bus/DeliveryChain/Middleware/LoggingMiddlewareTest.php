<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus\DeliveryChain\Middleware;

use Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use stdClass;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\LoggingMiddleware;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\MessageViolationException;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\MessageViolationListException;
use Werkspot\MessageBus\Message\AsynchronousMessage;
use Werkspot\MessageBus\Message\Message;

final class LoggingMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    const PAYLOAD = 'payload';

    /**
     * @test
     */
    public function executeLogsQueueingInfo(): void
    {
        $messageMock = new AsynchronousMessage(self::PAYLOAD, 'dummy destination');
        $loggerMock = $this->getLoggerMock('Queueing message "' . self::PAYLOAD . '"');

        $middleware = new LoggingMiddleware($loggerMock);

        $middleware->deliver($messageMock, function () {
        });
    }

    /**
     * @test
     */
    public function executeLogsExecutingInfo(): void
    {
        $messageMock = new Message(self::PAYLOAD, 'dummy destination');
        $loggerMock = $this->getLoggerMock('Executing message "' . self::PAYLOAD . '"');

        $middleware = new LoggingMiddleware($loggerMock);

        $middleware->deliver(
            $messageMock,
            function () {
            }
        );
    }

    /**
     * @test
     */
    public function executeCallsTheCallable(): void
    {
        $messageMock = new Message(self::PAYLOAD, 'dummy destination');
        $loggerMock = $this->getLoggerMock('Executing message "' . self::PAYLOAD. '"');

        $testClass = Mockery::mock(stdClass::class);
        $testClass->shouldReceive('itsCalled')->once();

        $middleware = new LoggingMiddleware($loggerMock);

        $middleware->deliver(
            $messageMock,
            function () use ($testClass) {
                $testClass->itsCalled();
            }
        );
    }

    /**
     * @test
     *
     * @expectedException \Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\MessageViolationListException
     */
    public function executeLogsValidationException(): void
    {
        $messageMock = new Message(self::PAYLOAD, 'dummy destination');

        $exceptionList = new MessageViolationListException();
        $exceptionList->add($exception = new MessageViolationException('dummy_field', 'dummy error message'));

        $loggerMock = $this->getLoggerMock('Executing message "' . self::PAYLOAD . '"');
        $loggerMock->shouldReceive('error')->once()->with('Error validating message "' . self::PAYLOAD . '"');
        $loggerMock->shouldReceive('debug')->once()->with(
            sprintf('Violation error %s: %s', 0, $exception->getMessage())
        );

        $middleware = new LoggingMiddleware($loggerMock);

        $middleware->deliver(
            $messageMock,
            function () use ($exceptionList) {
                throw $exceptionList;
            }
        );
    }

    /**
     * @test
     *
     * @expectedException \Exception
     */
    public function executeLogsException(): void
    {
        $messagePayload = 'payload';
        $message = new Message($messagePayload, 'dummy destination');

        $exceptionMessage = 'some exception message';
        $exception = new Exception($exceptionMessage);

        $loggerMock = $this->getLoggerMock('Executing message "' . $messagePayload . '"');
        $loggerMock->shouldReceive('error')->once()->with(get_class($exception) . ' while handling "' . $messagePayload . '": ' . $exceptionMessage);
        $loggerMock->shouldReceive('debug')->once()->with($exception->getTraceAsString());
        $loggerMock->shouldReceive('debug')->once()->with(serialize($message));

        $middleware = new LoggingMiddleware($loggerMock);

        $middleware->deliver(
            $message,
            function () use ($exception) {
                throw $exception;
            }
        );
    }

    /**
     * @return MockInterface|LoggerInterface
     */
    private function getLoggerMock(string $infoMessage): LoggerInterface
    {
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('info')->once()->with($infoMessage);

        return $loggerMock;
    }
}
