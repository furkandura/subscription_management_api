<?php

namespace App\Service;

use App\Dto\PurchaseRequestDto;
use App\Dto\VerifyReceiptResponseDto;
use App\Entity\Device;
use App\Entity\Subscription;
use App\Enum\SubscriptionStateEnum;
use App\Repository\SubscriptionRepository;
use App\Service\Platform\PlatformServiceFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriptionService
{
    public function __construct(
        private PlatformServiceFactory $platformServiceFactory,
        private EntityManagerInterface $em,
        private SubscriptionRepository $subscriptionRepository
    )
    {
    }

    public function purchase(PurchaseRequestDto $request, Device $device): Subscription
    {
        $verifyReceiptResponse = $this->platformServiceFactory
            ->create($device->getOperatingSystem())
            ->verifyReceipt($request->receipt);

        if (!$verifyReceiptResponse->getStatus()) {
            throw new BadRequestHttpException('PURCHASE_INVALID');
        }

        $subscription = $this->subscriptionRepository->findSubscription($device->getId(), $device->getApplication()->getId());

        if ($subscription) {
            if ($subscription->getState() == SubscriptionStateEnum::ACTIVE->value) {
                throw new BadRequestHttpException('ALREADY_HAVE_ACTIVE_SUBSCRIPTION');
            } else {
                return $this->refreshSubscription($subscription, $verifyReceiptResponse);
            }
        }

        return $this->createSubscription($device, $verifyReceiptResponse);
    }

    private function createSubscription(Device $device, VerifyReceiptResponseDto $verifyReceiptResponse): Subscription
    {
        $subscription = new Subscription();
        $subscription->setState(SubscriptionStateEnum::ACTIVE);
        $subscription->setDevice($device);
        $subscription->setApplication($device->getApplication());
        $subscription->setExpiredAt($verifyReceiptResponse->getExpiredAt());
        $subscription->setReceipt($verifyReceiptResponse->getReceipt());

        $this->em->persist($subscription);
        $this->em->flush();

        return $subscription;
    }

    private function refreshSubscription(Subscription $subscription, VerifyReceiptResponseDto $verifyReceiptResponse): Subscription
    {
        $subscription->setReceipt($verifyReceiptResponse->getReceipt());
        $subscription->setExpiredAt($verifyReceiptResponse->getExpiredAt());
        $subscription->setState($verifyReceiptResponse->getStatus() ? SubscriptionStateEnum::ACTIVE : SubscriptionStateEnum::PASSIVE);
        $this->em->flush();

        return $subscription;
    }

    public function validate(Subscription $subscription): Subscription
    {
        $verifyReceiptResponse = $this->platformServiceFactory
            ->create($subscription->getDevice()->getOperatingSystem())
            ->verifyReceipt($subscription->getReceipt());

        return $this->refreshSubscription($subscription, $verifyReceiptResponse);
    }

    public function check(Device $device): Subscription
    {
        $subscription = $this->subscriptionRepository->findActiveSubscription($device->getId(), $device->getApplication()->getId());

        if (!$subscription) {
            throw new NotFoundHttpException('NOT_FOUND_SUBSCRIPTION');
        }

        return $subscription;
    }


    /**
     * @return Subscription[]
     */
    public function getExpiredSubscriptions(): array
    {
        return $this->subscriptionRepository->findExpiredSubscriptions();
    }
}