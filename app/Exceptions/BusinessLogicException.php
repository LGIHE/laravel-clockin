<?php

namespace App\Exceptions;

use Exception;

class BusinessLogicException extends Exception
{
    protected $code = 'BUSINESS_LOGIC_ERROR';
    protected $statusCode = 400;

    public function __construct(string $message = 'Business logic error occurred', int $statusCode = 400, string $code = 'BUSINESS_LOGIC_ERROR')
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
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
