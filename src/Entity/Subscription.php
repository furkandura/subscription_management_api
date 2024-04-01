<?php

namespace App\Entity;

use App\Entity\Trait\Timestamp;
use App\Enum\SubscriptionStateEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity]
#[HasLifecycleCallbacks]
class Subscription
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Application::class)]
    private Application $application;

    #[ORM\ManyToOne(targetEntity: Device::class)]
    private Device $device;

    #[ORM\Column(length: 255)]
    private string $receipt;

    #[ORM\Column(length: 255)]
    private string $state;

    #[ORM\Column]
    private DateTime $expiredAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDevice(): Device
    {
        return $this->device;
    }

    public function setDevice(Device $device): void
    {
        $this->device = $device;
    }

    public function getReceipt(): string
    {
        return $this->receipt;
    }

    public function setReceipt(string $receipt): void
    {
        $this->receipt = $receipt;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(SubscriptionStateEnum $state): void
    {
        $this->state = $state->value;
    }

    public function getExpiredAt(): DateTime
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(DateTime $expiredAt): void
    {
        $this->expiredAt = $expiredAt;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }
}
