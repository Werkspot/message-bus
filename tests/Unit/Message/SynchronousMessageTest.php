<?php

namespace Werkspot\MessageBus\Test\Unit\Message;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Message\Message;

/**
 * @small
 */
final class SynchronousMessageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function getPayload_ShouldGetTheSameAsInjected(): void
    {
        $payload = 'foo';
        $message = new Message($payload, 'dummy destination');

        self::assertSame($payload, $message->getPayload());
    }
}
