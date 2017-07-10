<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation;

use Werkspot\MessageBus\Bus\DeliveryChain\MiddlewareInterface;
use Werkspot\MessageBus\Message\MessageInterface;

final class ValidationMiddleware implements MiddlewareInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function deliver(MessageInterface $message, callable $next): void
    {
        $messageViolationListException = $this->validator->validate($message);

        if (count($messageViolationListException) !== 0) {
            throw $messageViolationListException;
        }

        $next($message);
    }
}
