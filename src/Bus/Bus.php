<?php

namespace Werkspot\MessageBus\Bus;

use Werkspot\MessageBus\Bus\DeliveryChain\MiddlewareInterface;
use Werkspot\MessageBus\Message\MessageInterface;

final class Bus implements BusInterface
{
    /**
     * @var callable
     */
    private $deliveryChain;

    public static function fromMiddlewareList(MiddlewareInterface ...$middlewareList): self
    {
        $deliveryChain = function (): void {
        };

        while ($middleware = array_pop($middlewareList)) {
            $deliveryChain = function (MessageInterface $message) use ($middleware, $deliveryChain): void {
                $middleware->deliver($message, $deliveryChain);
            };
        }

        return new self($deliveryChain);
    }

    private function __construct(callable $deliveryChain)
    {
        $this->deliveryChain = $deliveryChain;
    }

    public function deliver(MessageInterface...$messageList): void
    {
        $chain = $this->deliveryChain;
        foreach ($messageList as $message) {
            $chain($message);
        }
    }
}
