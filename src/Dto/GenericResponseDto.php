<?php

namespace App\Dto;

class GenericResponseDto
{
    private ?int $code = null;
    private ?string $message = null;
    private ?array $trace = null;
    private ?array $errors = null;

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): GenericResponseDto
    {
        $this->code = $code;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): GenericResponseDto
    {
        $this->message = $message;
        return $this;
    }

    public function getTrace(): ?array
    {
        return $this->trace;
    }

    public function setTrace(?array $trace): GenericResponseDto
    {
        $this->trace = $trace;
        return $this;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function setErrors(?array $errors): GenericResponseDto
    {
        $this->errors = $errors;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), function ($i) {
            return !is_null($i);
        });
    }
}