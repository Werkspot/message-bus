<?php

namespace Werkspot\MessageBus\Message;

final class Message implements MessageInterface
{
    /**
     * @var string
     */
    private $destination;

    /**
     * @var mixed
     */
    private $payload;

    public function __construct(
        $payload,
        string $destination
    ) {
        $this->payload = $payload;
        $this->destination = $destination;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}
