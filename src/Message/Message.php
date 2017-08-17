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

    /**
     * @var array
     */
    private $metadata;

    public function __construct($payload, string $destination, array $metadata = [])
    {
        $this->payload = $payload;
        $this->destination = $destination;
        $this->metadata = $metadata;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
