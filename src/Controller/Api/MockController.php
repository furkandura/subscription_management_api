<?php

namespace App\Controller\Api;

use App\Dto\MockRequestDto;
use App\Dto\PurchaseRequestDto;
use App\Dto\RegisterRequestDto;
use App\Exception\RateLimitExceededHttpException;
use App\Service\DeviceService;
use DateTime;
use DateTimeZone;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/mock', name: 'api_mock_')]
#[OA\Tag(name: 'Mock Data')]
class MockController extends AbstractController
{
    #[Route(path: '/callback', name: 'callback', methods: [Request::METHOD_POST])]
    public function callback(Request $request): JsonResponse
    {
        return $this->json($request->request->all());
    }

    #[Route(path: '/apple/receipt/information', name: 'apple_receipt_information', methods: [Request::METHOD_POST])]
    public function appleReceiptInformation(
        #[MapRequestPayload] MockRequestDto $request,
    ): JsonResponse
    {
        $response = $this->handle($request);
        return $this->json($response);
    }

    #[Route(path: '/google/receipt/information', name: 'google_receipt_information', methods: [Request::METHOD_POST])]
    public function googleReceiptInformation(
        #[MapRequestPayload] MockRequestDto $request,
    ): JsonResponse
    {
        $response = $this->handle($request);
        return $this->json($response);
    }

    private function handle($request): array
    {
        $lastCharacter = substr($request->receipt, -1);
        $lastTwoCharacter = substr($request->receipt, -2);

        if ($lastTwoCharacter % 6 == 0){
            throw new RateLimitExceededHttpException('RATE_LIMIT_EXCEEDED');
        }

        $timezone = new DateTimeZone('America/Mexico_City');
        $expireDate = (new DateTime('now'))->setTimezone($timezone);

        return [
            'status' => is_numeric($lastCharacter) && $lastCharacter % 2 == 1,
            'receipt' => $request->receipt,
            'expire-date' => $expireDate->format('Y-m-d H:i:s'),
        ];
    }
}
