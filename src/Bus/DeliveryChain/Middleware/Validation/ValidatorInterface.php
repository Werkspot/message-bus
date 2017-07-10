<?php

namespace Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation;

use Werkspot\MessageBus\Message\MessageInterface;

interface ValidatorInterface
{
    public function validate(MessageInterface $message): MessageViolationListException;
}
