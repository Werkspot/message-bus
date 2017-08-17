<?php

namespace Werkspot\MessageBus\Message;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Throwable;
use Werkspot\MessageBus\MessageQueue\AsynchronousMessageInterface;
use Werkspot\MessageBus\MessageQueue\Priority;

final class AsynchronousMessage implements AsynchronousMessageInterface, MessageInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $destination;

    /**
     * @var mixed
     */
    private $payload;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var DateTimeImmutable
     */
    private $deliverAt;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable|null
     */
    private $updatedAt;

    /**
     * @var int
     */
    private $tries = 0;

    /**
     * @var string|null
     */
    private $errors;
    /**
     * @var array
     */
    private $metadata;

    public function __construct(
        $payload,
        string $destination,
        array $metadata = [],
        DateTimeImmutable $deliverAt = null,
        Priority $priority = null
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->payload = $payload;
        $this->destination = $destination;
        $this->metadata = $metadata;
        $this->deliverAt = $deliverAt ?? $this->defineDeliveryDate();
        $this->priority = $priority ?? new Priority(Priority::PRIORITY_LOWEST);
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    public function getPriority(): Priority
    {
        return $this->priority;
    }

    public function getDeliverAt(): DateTimeImmutable
    {
        return $this->deliverAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getTries(): int
    {
        return $this->tries;
    }

    public function getErrors(): ?string
    {
        return $this->errors;
    }

    public function fail(Throwable $error): void
    {
        $now = new DateTimeImmutable();

        $errorMessage = sprintf(
            "[%s] '%s': '%s'\n%s",
            $now->format(DateTime::ATOM),
            get_class($error),
            $error->getMessage(),
            $error->getTraceAsString()
        );

        $this->errors .= $errorMessage . "\n\n";

        $this->tries++;
        $this->updateDeliveryDate();
    }

    private function updateDeliveryDate(): void
    {
        $this->deliverAt = $this->defineDeliveryDate();
    }

    private function defineDeliveryDate(): DateTimeImmutable
    {
        $interval = $this->getDateTimeIntervalForTry($this->tries + 1);

        return (new DateTimeImmutable())->add($interval);
    }

    /**
     * By default we try the command in:
     *  - try 1: 0 minutes
     *  - try 2: 1 minutes
     *  - try 3: 4 minutes
     *  - try 4: 9 minutes
     *
     * @param int $try The try of the command, try 1 is the first time the message is delivered
     */
    private function getDateTimeIntervalForTry(int $try): DateInterval
    {
        $waitingTimeInMinutes = ($try - 1) * ($try - 1);

        return new DateInterval(sprintf('PT%dM', $waitingTimeInMinutes));
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
