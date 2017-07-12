<?php

namespace Werkspot\MessageBus\MessageQueue\WerkspotMessageQueue;

use Werkspot\MessageBus\MessageQueue\AsynchronousMessageInterface;
use Werkspot\MessageBus\MessageQueue\MessageQueueServiceInterface;
use Werkspot\MessageQueue\MessageQueueServiceInterface as WerkspotMessageQueueServiceInterface;

final class MessageQueueService implements MessageQueueServiceInterface
{
    /**
     * @var WerkspotMessageQueueServiceInterface
     */
    private $werkspotMessageQueueService;

    public function __construct(WerkspotMessageQueueServiceInterface $werkspotMessageQueueService)
    {
        $this->werkspotMessageQueueService = $werkspotMessageQueueService;
    }

    public function enqueueMessage(AsynchronousMessageInterface $message): void
    {
        $this->werkspotMessageQueueService->enqueueMessage(
            $message->getPayload(),
            $message->getDestination(),
            $message->getDeliverAt(),
            $message->getPriority()->toInt()
        );
    }
}
