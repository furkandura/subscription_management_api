<?php

namespace App\Dto;

use App\Enum\SubscriptionCallbackEventEnum;

class CallbackRequestDto
{
    private string $appId;
    private string $deviceId;
    private string $event;

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): CallbackRequestDto
    {
        $this->appId = $appId;
        return $this;
    }

    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    public function setDeviceId(string $deviceId): CallbackRequestDto
    {
        $this->deviceId = $deviceId;
        return $this;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function setEvent(SubscriptionCallbackEventEnum $event): CallbackRequestDto
    {
        $this->event = $event->value;
        return $this;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

}