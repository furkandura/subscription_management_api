<?php

namespace App\Service\Platform;

use App\Dto\VerifyReceiptResponseDto;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractPlatform
{
    private HttpClientInterface $client;
    public function __construct(string $apiUrl, array $credentials)
    {
        $this->client = HttpClient::createForBaseUri($apiUrl, [
            'auth_basic' => $credentials
        ]);
    }

    abstract public function verifyReceipt(string $receipt): VerifyReceiptResponseDto;

    protected function request(string $method, string $endpoint, array $data): array
    {
        return $this->client->request($method, $endpoint, [
            'json' => $data
        ])->toArray(false);
    }
}