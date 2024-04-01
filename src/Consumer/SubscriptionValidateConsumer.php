<?php

namespace App\Consumer;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Service\SubscriptionService;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * php bin/console rabbitmq:consumer subscription_validate
 */
class SubscriptionValidateConsumer implements ConsumerInterface
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private SubscriptionService $subscriptionService
    )
    {
    }

    public function execute(AMQPMessage $msg): bool|int
    {
        try {
            echo "---------------- NEW --------------".PHP_EOL;
            $message = json_decode($msg->getBody(), true);
            /** @var Subscription $subscription */
            $subscription = $this->subscriptionRepository->find($message['id'], LockMode::NONE);

            $this->subscriptionService->validate($subscription);
            echo '[SUCCESS] Subscription('.$subscription->getId().') processed.' . PHP_EOL;
            return self::MSG_ACK;
        } catch (\Exception $e) {
            echo '[ERROR] '.$e->getMessage() . PHP_EOL;
            return self::MSG_REJECT;
        }
    }
}