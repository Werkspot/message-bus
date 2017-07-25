<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Bus\DeliveryChain\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Werkspot\MessageBus\Bus\DeliveryChain\MiddlewareInterface;
use Werkspot\MessageBus\Message\MessageInterface;

final class OrmTransactionMiddleware implements MiddlewareInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function deliver(MessageInterface $message, callable $next): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $next($message);

            return;
        }

        try {
            $this->entityManager->beginTransaction();
            $next($message);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
