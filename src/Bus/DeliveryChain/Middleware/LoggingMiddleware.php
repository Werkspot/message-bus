<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Bus\DeliveryChain\Middleware;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Werkspot\Command\Exception\CommandViolationException;
use Werkspot\Command\Exception\CommandViolationListException;
use Werkspot\MessageBus\Bus\DeliveryChain\MiddlewareInterface;
use Werkspot\MessageBus\Message\AsynchronousMessage;
use Werkspot\MessageBus\Message\MessageInterface;

final class LoggingMiddleware implements MiddlewareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function deliver(MessageInterface $message, callable $next): void
    {
        $this->logger->info(
            sprintf(
                '%s message "%s"',
                $message instanceof AsynchronousMessage ? 'Queueing' : 'Executing',
                $this->getPayloadType($message)
            )
        );

        try {
            $next($message);
        } catch (CommandViolationListException $exceptionList) {
            $this->logValidationException($message, $exceptionList);
            throw $exceptionList;
        } catch (Exception $exception) {
            $this->logException($message, $exception);
            throw $exception;
        }
    }

    private function logValidationException(
        MessageInterface $message,
        CommandViolationListException $exceptionList
    ): void {
        $this->logger->error('Error validating message ' . json_encode($message->getPayload()));

        /** @var CommandViolationException $exception */
        foreach ($exceptionList as $key => $exception) {
            $this->logger->debug(sprintf('Violation error %s: %s', $key, $exception->getMessage()));
        }
    }

    private function logException(MessageInterface $message, Exception $exception): void
    {
        $this->logger->error(
            sprintf(
                '%s while handling "%s": %s',
                get_class($exception),
                $this->getPayloadType($message),
                trim($exception->getMessage())
            )
        );

        $this->logger->debug($exception->getTraceAsString());
        $this->logger->debug(serialize($message));
    }

    private function getPayloadType(MessageInterface $message): string
    {
        if (is_object($message->getPayload())) {
            return get_class($message->getPayload());
        }

        if (is_array($message->getPayload())) {
            return 'array';
        }

        return $message->getPayload();
    }
}
