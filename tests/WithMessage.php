<?php

namespace Werkspot\MessageBus\Test;

use Mockery;
use Mockery\Matcher\Closure;
use Werkspot\MessageBus\Message\AsynchronousMessage;
use Werkspot\MessageBus\Message\Message;
use Werkspot\MessageBus\Message\MessageInterface;
use Werkspot\MessageBus\MessageQueue\AsynchronousMessageInterface;

final class WithMessage
{
    public static function equalTo(MessageInterface $expectedMessage): Closure
    {
        return Mockery::on(
            function (MessageInterface $actualMessage) use ($expectedMessage) {
                if (
                    get_class($expectedMessage) !== get_class($actualMessage)
                    || $expectedMessage->getPayload() !== $actualMessage->getPayload()
                    || $expectedMessage->getDestination() !== $actualMessage->getDestination()
                    || $expectedMessage->getMetadata() !== $actualMessage->getMetadata()
                ) {
                    return false;
                }

                if ($actualMessage instanceof AsynchronousMessage) {
                    /** @var AsynchronousMessage $expectedMessage */
                    return $expectedMessage->getTries() === $actualMessage->getTries()
                    && $expectedMessage->getErrors() === $actualMessage->getErrors()
                    && $expectedMessage->getPriority()->toInt() === $actualMessage->getPriority()->toInt()
                    && $expectedMessage->getDeliverAt() === $actualMessage->getDeliverAt()
                    && $expectedMessage->getUpdatedAt() === $actualMessage->getUpdatedAt();
                }

                return true;
            }
        );
    }

    public static function equalToMessageWithoutComparingDatesNorErrors(AsynchronousMessageInterface $expectedMessage): Closure
    {
        return Mockery::on(
            function (AsynchronousMessageInterface $actualMessage) use ($expectedMessage) {
                return get_class($expectedMessage) === get_class($actualMessage)
                    && $expectedMessage->getPayload() === $actualMessage->getPayload()
                    && $expectedMessage->getTries() === $actualMessage->getTries()
                    && $expectedMessage->getDestination() === $actualMessage->getDestination()
                    && $expectedMessage->getPriority()->toInt() === $actualMessage->getPriority()->toInt();
            }
        );
    }

    public static function equalToSynchronousMessage(Message $expectedMessage): Closure
    {
        return Mockery::on(
            function (Message $actualMessage) use ($expectedMessage) {
                return get_class($expectedMessage) === get_class($actualMessage)
                    && $expectedMessage->getPayload() === $actualMessage->getPayload()
                    && $expectedMessage->getDestination() === $actualMessage->getDestination()
                    && $expectedMessage->getMetadata() === $actualMessage->getMetadata();
            }
        );
    }
}
