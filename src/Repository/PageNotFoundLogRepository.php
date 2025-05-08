<?php

namespace WechatMiniProgramTrackingBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;

/**
 * @method PageNotFoundLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageNotFoundLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageNotFoundLog[]    findAll()
 * @method PageNotFoundLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageNotFoundLogRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageNotFoundLog::class);
    }
}
