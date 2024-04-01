<?php

namespace App\Service;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

class QueService
{
    public function __construct(
        private Producer $subscriptionValidateProducer,
        private Producer $callbackRequestProducer
    )
    {
    }

    public function dispatchSubscriptionValidate(array $data): void
    {
        $this->subscriptionValidateProducer->publish(json_encode($data));
    }

    public function dispatchCallbackRequest(array $data): void
    {
        $this->callbackRequestProducer->publish(json_encode($data));
    }
}