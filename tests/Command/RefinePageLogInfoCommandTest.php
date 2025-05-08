<?php

namespace WechatMiniProgramTrackingBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use WechatMiniProgramTrackingBundle\Command\RefinePageLogInfoCommand;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;
use WechatMiniProgramTrackingBundle\Repository\PageVisitLogRepository;

class RefinePageLogInfoCommandTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private PageVisitLogRepository $repository;
    private RefinePageLogInfoCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // 创建模拟对象
        $this->repository = $this->createMock(PageVisitLogRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // 创建被测命令
        $this->command = new RefinePageLogInfoCommand(
            $this->repository,
            $this->entityManager
        );

        // 创建应用并注册命令
        $application = new Application();
        $application->add($this->command);

        // 创建命令测试器
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * 测试命令基本执行流程
     */
    public function testExecute(): void
    {
        // 创建模拟查询构建器
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        // 创建模拟日志数据
        $logItem1 = new PageVisitLog();
        $logItem1->setSessionId('session1');
        $logItem1->setPage('/pages/index');
        $logItem1->setRouteId(1);

        $logItem2 = new PageVisitLog();
        $logItem2->setSessionId('session1');
        $logItem2->setPage('/pages/detail');
        $logItem2->setRouteId(2);
        $logItem2->setCreatedBy('user1');

        // 配置 Repository->createQueryBuilder 返回模拟查询构建器
        $this->repository->expects($this->exactly(2))
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        // 配置查询构建器链式调用
        $queryBuilder->expects($this->exactly(2))
            ->method('where')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->exactly(3))
            ->method('setParameter')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->exactly(2))
            ->method('getQuery')
            ->willReturn($query);

        // 配置第一个查询结果为已有日志数组
        $query->expects($this->once())
            ->method('toIterable')
            ->willReturn([$logItem1]);

        // 配置第二个查询结果为带有 createdBy 的日志
        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn($logItem2);

        // 配置 EntityManager 方法
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($logItem1);

        $this->entityManager->expects($this->once())
            ->method('flush');

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证输出和返回值
        $output = $this->commandTester->getDisplay();
        $this->assertNotEmpty($output); // 应输出日志 ID
        $this->assertEquals(0, $exitCode); // 成功返回值为 0

        // 验证日志更新
        $this->assertEquals('user1', $logItem1->getCreatedBy());
    }

    /**
     * 测试当没有找到匹配的日志条目时的行为
     */
    public function testExecuteWithNoMatchingLog(): void
    {
        // 创建模拟查询构建器
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);

        // 创建模拟日志数据
        $logItem = new PageVisitLog();
        $logItem->setSessionId('session1');
        $logItem->setPage('/pages/index');
        $logItem->setRouteId(1);

        // 配置 Repository->createQueryBuilder 返回模拟查询构建器
        $this->repository->expects($this->exactly(2))
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        // 配置查询构建器链式调用
        $queryBuilder->expects($this->exactly(2))
            ->method('where')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->exactly(3))
            ->method('setParameter')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->exactly(2))
            ->method('getQuery')
            ->willReturn($query);

        // 配置第一个查询结果为已有日志数组
        $query->expects($this->once())
            ->method('toIterable')
            ->willReturn([$logItem]);

        // 配置第二个查询结果为 null（没有找到匹配的记录）
        $query->expects($this->once())
            ->method('getOneOrNullResult')
            ->willReturn(null);

        // EntityManager 不应调用 persist 和 flush
        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->entityManager->expects($this->never())
            ->method('flush');

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证输出和返回值
        $this->assertEquals(0, $exitCode); // 成功返回值为 0

        // 验证日志未更新
        $this->assertNull($logItem->getCreatedBy());
    }
}
