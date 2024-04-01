<?php

namespace App\Service\Platform;

use App\Dto\VerifyReceiptResponseDto;
use DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

class GooglePlatform extends AbstractPlatform
{
    public function __construct(
        string $apiUrl,
        array $credentials
    )
    {
        parent::__construct($apiUrl, $credentials);
    }

    public function verifyReceipt(string $receipt): VerifyReceiptResponseDto
    {
        $response = $this->request(
            Request::METHOD_POST,
            'receipt/information',
            ['receipt' => $receipt]
        );

        return (new VerifyReceiptResponseDto())
            ->setStatus($response['status'])
            ->setExpiredAt(new DateTime($response['expire-date']))
            ->setReceipt($response['receipt']);
    }
}