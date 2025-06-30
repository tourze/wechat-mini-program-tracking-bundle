<?php

namespace WechatMiniProgramTrackingBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatMiniProgramTrackingBundle\Repository\PageVisitLogRepository;

#[AsCronTask(expression: '*/10 * * * *')]
#[AsCommand(name: self::NAME, description: '定期修正页面访问日志的创建人信息')]
class RefinePageLogInfoCommand extends Command
{
    public const NAME = 'wechat-mini-program:refine-page-log-info';
    
public function __construct(
        private readonly PageVisitLogRepository $pageVisitLogRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $log = $this->pageVisitLogRepository->createQueryBuilder('p')
            ->where("p.createTime between :start and :end and (p.createdBy is null or p.createdBy = '')")
            ->setParameter('start', CarbonImmutable::now()->startOfDay())
            ->setParameter('end', CarbonImmutable::now()->endOfDay())
            ->getQuery()
            ->toIterable();

        foreach ($log as $item) {
            $output->writeln($item->getId());
            $res = $this->pageVisitLogRepository->createQueryBuilder('p')
                ->where('p.sessionId = :sessionId and p.createdBy is not null')
                ->setParameter('sessionId', $item->getSessionId())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            if ((bool) empty($res)) {
                continue;
            }

            $item->setCreatedBy($res->getCreatedBy());
            $this->entityManager->persist($item);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
