<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;
use WechatMiniProgramTrackingBundle\Repository\PageVisitLogRepository;

#[AsCronTask(expression: '*/10 * * * *')]
#[AsCommand(name: self::NAME, description: '定期修正页面访问日志的创建人信息')]
final class RefinePageLogInfoCommand extends Command
{
    public const NAME = 'wechat-mini-program:refine-page-log-info';

    public function __construct(
        private readonly PageVisitLogRepository $pageVisitLogRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 确保时间一致性，只调用一次 CarbonImmutable::now()
        $now = CarbonImmutable::now();
        $startOfDay = $now->startOfDay();
        $endOfDay = $now->endOfDay();

        /** @var iterable<PageVisitLog> $log */
        $log = $this->pageVisitLogRepository->createQueryBuilder('p')
            ->where("p.createTime between :start and :end and (p.createdBy is null or p.createdBy = '')")
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->getQuery()
            ->toIterable()
        ;

        // 收集所有需要处理的sessionId
        $sessionIds = [];
        $itemsToUpdate = [];

        foreach ($log as $item) {
            if (!$item instanceof PageVisitLog) {
                continue;
            }

            $sessionIds[] = $item->getSessionId();
            $itemsToUpdate[] = $item;
        }

        if ($sessionIds === []) {
            return Command::SUCCESS;
        }

        // 批量查询所有有createdBy的记录，按sessionId分组
        $sessionToUserMap = [];
        /** @var array<array{sessionId: string|null, createdBy: string|null}> $referenceLogs */
        $referenceLogs = $this->pageVisitLogRepository->createQueryBuilder('p')
            ->select('p.sessionId', 'p.createdBy')
            ->where('p.sessionId IN (:sessionIds)')
            ->andWhere('p.createdBy IS NOT NULL')
            ->andWhere('p.createdBy != :emptyCreatedBy')
            ->setParameter('sessionIds', array_unique($sessionIds))
            ->setParameter('emptyCreatedBy', '')
            ->groupBy('p.sessionId', 'p.createdBy')
            ->getQuery()
            ->getResult()
        ;

        foreach ($referenceLogs as $referenceLog) {
            $sessionToUserMap[$referenceLog['sessionId']] = $referenceLog['createdBy'];
        }

        // 批量更新记录
        $updatedCount = 0;
        foreach ($itemsToUpdate as $item) {
            $sessionId = $item->getSessionId();

            if (isset($sessionToUserMap[$sessionId])) {
                $output->writeln((string) $item->getId());
                $item->setCreatedBy($sessionToUserMap[$sessionId]);
                $this->entityManager->persist($item);
                $updatedCount++;
            }
        }

        // 只在最后flush一次
        if ($updatedCount > 0) {
            $this->entityManager->flush();
            $output->writeln("Updated {$updatedCount} records.");
        }

        return Command::SUCCESS;
    }
}
