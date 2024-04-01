<?php

namespace App\Controller\Api;

use App\Dto\RegisterRequestDto;
use App\Service\DeviceService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/device', name: 'api_device_')]
#[OA\Tag(name: 'Device')]
class DeviceController extends AbstractController
{
    #[Route(path: '/register', name: 'register', methods: [Request::METHOD_POST])]
    public function register(
        #[MapRequestPayload] RegisterRequestDto $request,
        DeviceService $deviceService
    ): JsonResponse
    {
        $response = $deviceService->register($request);
        return $this->json($response);
    }
}
