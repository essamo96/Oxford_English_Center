<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when a branching / enrollment operation violates a business rule
 * (duplicate membership, time conflict, outstanding balance, …).
 * Carries the human-readable reasons so controllers can surface them.
 */
class EnrollmentException extends Exception
{
    /** @var string[] */
    public array $errors;

    public function __construct(array $errors, string $message = '')
    {
        $this->errors = array_values($errors);
        parent::__construct($message !== '' ? $message : implode(' | ', $this->errors));
    }
}
