<?php

namespace App\Dto;

use App\Enum\OperationSystemEnum;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterResponseDto
{
    private string $clientToken;


    public function getClientToken(): string
    {
        return $this->clientToken;
    }

    public function setClientToken(string $clientToken): RegisterResponseDto
    {
        $this->clientToken = $clientToken;
        return $this;
    }


}