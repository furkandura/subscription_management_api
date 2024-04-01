<?php

namespace App\Controller\Api;

use App\Dto\PurchaseRequestDto;
use App\Dto\RegisterRequestDto;
use App\Entity\Device;
use App\Repository\DeviceRepository;
use App\Security\TokenUser;
use App\Service\DeviceService;
use App\Service\SubscriptionService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/api/subscription', name: 'api_subscription_')]
#[OA\Tag(name: 'Subscription')]
#[Security(name: 'ClientToken')]
class SubscriptionController extends AbstractController
{
    #[Route(path: '/purchase', name: 'purchase', methods: [Request::METHOD_POST])]
    public function purchase(
        #[MapRequestPayload] PurchaseRequestDto $request,
        #[CurrentUser] TokenUser $tokenUser,
        DeviceRepository $deviceRepository,
        SubscriptionService $subscriptionService
    ): JsonResponse
    {
        $device = $deviceRepository->findByDeviceId($tokenUser->getDeviceId());
        $response = $subscriptionService->purchase($request, $device);
        return $this->json($response);
    }

    #[Route(path: '/check', name: 'check', methods: [Request::METHOD_POST])]
    public function check(
        #[CurrentUser] TokenUser $tokenUser,
        DeviceRepository $deviceRepository,
        SubscriptionService $subscriptionService
    ): JsonResponse
    {
        $device = $deviceRepository->findByDeviceId($tokenUser->getDeviceId());
        $response = $subscriptionService->check($device);
        return $this->json($response);
    }
}
