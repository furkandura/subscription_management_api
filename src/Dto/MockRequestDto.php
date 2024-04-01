<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class MockRequestDto
{
    #[Assert\NotBlank]
    public string $receipt;
}