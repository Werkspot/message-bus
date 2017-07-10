<?php

namespace Werkspot\MessageBus\Bus\DeliveryChain\Middleware\Validation;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use RuntimeException;
use Traversable;

class MessageViolationListException extends RuntimeException implements IteratorAggregate, Countable
{
    /**
     * @var MessageViolationException[]
     */
    private $violations;

    public function __construct()
    {
        $this->violations = [];
    }

    public function add(MessageViolationException $e): void
    {
        $this->violations[] = $e;

        $this->message .= $e->getMessage() . PHP_EOL;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     *
     * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->violations);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @see http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count(): int
    {
        return count($this->violations);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        $errors = [];

        foreach ($this->violations as $commandViolationException) {
            $fieldName = $commandViolationException->getField();

            if (preg_match('/\[(\w+)\]/', $fieldName, $extraQuestionsMatch)) {
                $rootFieldName = 'extraQuestions';
                $mainFieldName = $extraQuestionsMatch[1];

                $errors[$rootFieldName][$mainFieldName] = $commandViolationException->getError();
                continue;
            }

            if (isset($errors[$fieldName])) {
                // If we have multiple errors for the same field, merge them in an array
                if (!is_array($errors[$fieldName])) {
                    $errors[$fieldName] = [$errors[$fieldName]];
                }

                $errors[$fieldName][] = $commandViolationException->getError();
                continue;
            }

            $errors[$fieldName] = $commandViolationException->getError();
        }

        return $errors;
    }
}
