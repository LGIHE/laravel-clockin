<?php

namespace App\Exceptions;

use Exception;

class ResourceNotFoundException extends Exception
{
    protected $code = 'RESOURCE_NOT_FOUND';
    protected $statusCode = 404;

    public function __construct(string $message = 'Resource not found', string $code = 'RESOURCE_NOT_FOUND')
    {
        parent::__construct($message);
        $this->code = $code;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->code;
    }
}
