<?php

namespace App\Dto;

use App\Enum\OperationSystemEnum;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequestDto
{
    #[Assert\NotBlank]
    public string $uid;

    #[Assert\NotBlank]
    public string $appId;

    #[Assert\NotBlank]
    public string $language;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [OperationSystemEnum::class, 'values'])]
    public string $operatingSystem;
}