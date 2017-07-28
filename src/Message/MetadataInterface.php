<?php

declare(strict_types=1);

namespace Werkspot\MessageBus\Message;

interface MetadataInterface
{
    /**
     * @return array - A serializable array
     */
    public function getData(): array;
}
