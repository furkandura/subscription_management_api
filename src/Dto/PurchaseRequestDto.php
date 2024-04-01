<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequestDto
{
    #[Assert\NotBlank]
    public string $receipt;
}