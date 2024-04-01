<?php

namespace App\Service\Platform;

use App\Enum\OperationSystemEnum;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PlatformServiceFactory
{
    public function __construct(
        private ParameterBagInterface $param
    )
    {
    }

    public function create(string $os): AbstractPlatform
    {
        return match ($os) {
            OperationSystemEnum::ANDROID->value => new GooglePlatform(
                $this->param->get('google_api_url'),
                [$this->param->get('google_api_username'), $this->param->get('google_api_password')]
            ),
            OperationSystemEnum::IOS->value => new ApplePlatform(
                $this->param->get('apple_api_url'),
                [$this->param->get('apple_api_username'), $this->param->get('apple_api_password')]
            ),

            default => throw new InvalidArgumentException('INVALID_PLATFORM_SERVICE'),
        };
    }
}