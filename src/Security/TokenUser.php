<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class TokenUser implements UserInterface
{
    private string $token;
    private array $roles = [];
    private int $deviceId;
    private string $deviceOperatingSystem;
    private int $applicationId;

    public function getDeviceUid(): string
    {
        return $this->deviceUid;
    }

    public function getDeviceOperatingSystem(): string
    {
        return $this->deviceOperatingSystem;
    }

    public function setDeviceOperatingSystem(string $deviceOperatingSystem): TokenUser
    {
        $this->deviceOperatingSystem = $deviceOperatingSystem;
        return $this;
    }

    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    public function setDeviceId(int $deviceId): TokenUser
    {
        $this->deviceId = $deviceId;
        return $this;
    }

    public function getApplicationId(): int
    {
        return $this->applicationId;
    }

    public function setApplicationId(int $applicationId): TokenUser
    {
        $this->applicationId = $applicationId;
        return $this;
    }


    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): TokenUser
    {
        $this->token = $token;
        return $this;
    }


    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->token;
    }
}