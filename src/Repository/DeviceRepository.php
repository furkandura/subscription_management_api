<?php

namespace App\Repository;

use App\Entity\Application;
use App\Entity\Device;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }

    public function findByDevice(string $uid, string $appId): ?Device
    {
        $qb = $this->createQueryBuilder('d')
            ->where('d.uid = :uid')
            ->setParameter('uid', $uid)
            ->leftJoin('d.application', 'a')
            ->andWhere('a.appId = :appId')
            ->setParameter('appId', $appId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByDeviceId(string $id): ?Device
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d, a')
            ->leftJoin('d.application', 'a')
            ->where('d.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
