<?php

namespace WechatMiniProgramTrackingBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;

/**
 * @method JumpTrackingLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method JumpTrackingLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method JumpTrackingLog[] findAll()
 * @method JumpTrackingLog[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JumpTrackingLogRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JumpTrackingLog::class);
    }
}
