<?php

namespace App\EventListener;

use App\Entity\Subscription;
use App\Enum\SubscriptionCallbackEventEnum;
use App\Enum\SubscriptionStateEnum;
use App\Service\CallbackService;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class SubscriptionListener
{
    public function __construct(
        private  CallbackService $callbackService
    )
    {
    }

    public function prePersist(Subscription $subscription, PrePersistEventArgs $event): void
    {
        $this->callbackService->notify($subscription, SubscriptionCallbackEventEnum::STARTED);
    }

    public function preUpdate(Subscription $subscription, PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('expiredAt')) {
            $this->callbackService->notify($subscription, SubscriptionCallbackEventEnum::RENEWED);
        }

        if ($event->hasChangedField('state') && $subscription->getState() == SubscriptionStateEnum::PASSIVE) {
            $this->callbackService->notify($subscription, SubscriptionCallbackEventEnum::CANCELLED);
        }
    }

}