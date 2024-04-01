<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\DeviceToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeviceTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceToken::class);
    }

    public function findByDevice(Device $device): ?DeviceToken
    {
        return $this->findOneBy(['device' => $device]);
    }

    public function findByToken(string $token): ?DeviceToken
    {
        return $this->findOneBy(['token' => $token]);
    }
}
