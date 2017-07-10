<?php

namespace Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation;

use RuntimeException;

class MessageViolationException extends RuntimeException
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $error;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(string $field, string $error, array $parameters = [])
    {
        $this->field = $field;
        $this->error = $error;
        $this->parameters = $parameters;

        parent::__construct($field . ': ' . $error);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
