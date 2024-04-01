<?php

namespace App\Dto;

use DateTime;

class VerifyReceiptResponseDto
{
    private bool $status;
    private DateTime $expiredAt;
    private string $receipt;

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): VerifyReceiptResponseDto
    {
        $this->status = $status;
        return $this;
    }

    public function getExpiredAt(): DateTime
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(DateTime $expiredAt): VerifyReceiptResponseDto
    {
        $this->expiredAt = $expiredAt;
        return $this;
    }

    public function getReceipt(): string
    {
        return $this->receipt;
    }

    public function setReceipt(string $receipt): VerifyReceiptResponseDto
    {
        $this->receipt = $receipt;
        return $this;
    }

}