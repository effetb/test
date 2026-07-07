<?php

declare(strict_types=1);

namespace App\Exception;

class EventValidationException extends \RuntimeException
{
    /**
     * @param array<int, array{field: string, message: string}> $errors
     */
    public function __construct(private readonly array $errors)
    {
        parent::__construct('Event validation failed.');
    }

    /**
     * @return array<int, array{field: string, message: string}>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
