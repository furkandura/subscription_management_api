<?php

namespace App\Repository;

use App\Entity\Subscription;
use App\Enum\SubscriptionStateEnum;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findActiveSubscription(int $deviceId, int $applicationId): ?Subscription
    {
        return $this->findOneBy([
            'device' => $deviceId,
            'application' => $applicationId,
            'state' => SubscriptionStateEnum::ACTIVE
        ]);
    }

    public function findSubscription(int $deviceId, int $applicationId): ?Subscription
    {
        return $this->findOneBy([
            'device' => $deviceId,
            'application' => $applicationId
        ]);
    }

    /**
     * @return Subscription[]
     */
    public function findExpiredSubscriptions(): array
    {
        $now = new DateTime();

        return $this->createQueryBuilder('s')
            ->select('s, d')
            ->leftJoin('s.device', 'd')
            ->where('s.expiredAt < :now')
            ->setParameter('now', $now)
            ->andWhere('s.state = :activeState')
            ->setParameter('activeState', SubscriptionStateEnum::ACTIVE)
            ->getQuery()
            ->getResult();
    }
}
