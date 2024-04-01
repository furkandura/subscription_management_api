<?php

namespace App\Service;

use App\Dto\CallbackRequestDto;
use App\Entity\Subscription;
use App\Enum\SubscriptionCallbackEventEnum;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

class CallbackService
{
    private const SUCCESS_STATUS_CODES = [200, 201];

    public function __construct(
        private LoggerInterface $logger,
        private QueService $queService
    )
    {
    }

    public function notify(Subscription $subscription, SubscriptionCallbackEventEnum $callbackEventEnum): void
    {
        $application = $subscription->getApplication();

        $request = (new CallbackRequestDto())
            ->setDeviceId($subscription->getDevice()->getUid())
            ->setAppId($application->getAppId())
            ->setEvent($callbackEventEnum);

        $endpoint = $application->getCallbackUrl();

        $this->request($endpoint, $request->toArray());
    }

    public function request(string $endpoint, array $data, bool $requeue = false): void
    {
        $response = HttpClient::create()->request(Request::METHOD_POST, $endpoint, [
            'json' => $data
        ]);

        if (in_array($response->getStatusCode(), self::SUCCESS_STATUS_CODES)) {
            $this->logger->info('Callback request successful. Response: ' . $response->getContent());
        } else {

            if ($requeue) {
                throw new Exception('REQUEUE');
            } else {
                $this->queService->dispatchCallbackRequest(['endpoint' => $endpoint, 'data' => $data]);
            }
        }
    }
}