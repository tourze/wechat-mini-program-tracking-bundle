<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;

/**
 * @extends ServiceEntityRepository<JumpTrackingLog>
 */
#[AsRepository(entityClass: JumpTrackingLog::class)]
class JumpTrackingLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JumpTrackingLog::class);
    }

    public function save(JumpTrackingLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JumpTrackingLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
