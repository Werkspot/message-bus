<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\MessageQueue\WerkspotMessageQueue;

use Mockery;
use Werkspot\MessageBus\Message\AsynchronousMessage;
use Werkspot\MessageBus\MessageQueue\WerkspotMessageQueue\MessageQueueService;
use Werkspot\MessageQueue\MessageQueueServiceInterface as WerkspotMessageQueueServiceInterface;
use Werkspot\TestFramework\Unit\AbstractUnitTest;

final class MessageQueueServiceTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function enqueueMessage(): void
    {
        $message = new AsynchronousMessage('payload', 'destination', ['metadata']);
        $werkspotMessageQueueService = Mockery::mock(WerkspotMessageQueueServiceInterface::class);
        $werkspotMessageQueueService
            ->shouldReceive('enqueueMessage')
            ->once()
            ->with(
                $message->getPayload(),
                $message->getDestination(),
                $message->getDeliverAt(),
                $message->getPriority()->toInt(),
                $message->getMetadata()
            );

        $messageQueueService = new MessageQueueService($werkspotMessageQueueService);
        $messageQueueService->enqueueMessage($message);
    }
}
