<?php

namespace App\Exception;

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RateLimitExceededHttpException extends HttpException
{
    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(429, $message, $previous, $headers, $code);
    }
}