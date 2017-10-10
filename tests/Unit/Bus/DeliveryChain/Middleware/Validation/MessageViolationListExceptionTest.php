<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Test\Unit\Bus\DeliveryChain\Middleware\Validation;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\MessageViolationException;
use Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation\MessageViolationListException;

final class MessageViolationListExceptionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function getErrors(): void
    {
        $violations = [
            new MessageViolationException('a', 'b', []),
            new MessageViolationException('a', 'b2', ['some parameters']),
            new MessageViolationException('c', 'd', []),
            new MessageViolationException('d[e]', 'f', []),
            new MessageViolationException('d[g]', 'h', [])
        ];

        $violationList = new MessageViolationListException();
        foreach ($violations as $violation) {
            $violationList->add($violation);
        }

        self::assertSame(5, $violationList->count());
        self::assertSame(
            [
                'a' => ['b', 'b2'],
                'c' => 'd',
                'd' => [
                    'e' => 'f',
                    'g' => 'h'
                ]
            ],
            $violationList->getErrors()
        );

        foreach ($violationList as $key => $value) {
            self::assertSame($violations[$key], $value);
            self::assertSame($violations[$key]->getParameters(), $value->getParameters());
        }
    }
}
