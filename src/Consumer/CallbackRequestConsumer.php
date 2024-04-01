<?php

namespace App\Consumer;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Service\CallbackService;
use App\Service\SubscriptionService;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * php bin/console rabbitmq:consumer callback_request
 */
class CallbackRequestConsumer implements ConsumerInterface
{
    public function __construct(
        private CallbackService $callbackService
    )
    {
    }

    public function execute(AMQPMessage $msg): bool|int
    {
        try {
            echo "---------------- NEW --------------".PHP_EOL;
            $message = json_decode($msg->getBody(), true);
            $this->callbackService->request($message['endpoint'], $message['data'], requeue: true);
            echo '[SUCCESS] '. 'Callback request completed successfully.' . PHP_EOL;
            return self::MSG_ACK;
        } catch (\Exception $e) {
            echo '[ERROR] '.$e->getMessage() . PHP_EOL;
            return self::MSG_REJECT;
        }
    }
}