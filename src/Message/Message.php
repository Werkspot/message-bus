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
     * @var MetadataCollectionInterface
     */
    private $metadata;

    public function __construct($payload, string $destination, MetadataCollectionInterface $metadata = null)
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

    public function getMetadata(): ?MetadataCollectionInterface
    {
        return $this->metadata;
    }
}
