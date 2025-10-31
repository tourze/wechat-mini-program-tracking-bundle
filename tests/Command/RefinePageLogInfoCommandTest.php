<?php

namespace WechatMiniProgramTrackingBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatMiniProgramTrackingBundle\Command\RefinePageLogInfoCommand;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;

/**
 * @internal
 */
#[CoversClass(RefinePageLogInfoCommand::class)]
#[RunTestsInSeparateProcesses]
final class RefinePageLogInfoCommandTest extends AbstractCommandTestCase
{
    private RefinePageLogInfoCommand $command;

    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        $this->setUpTestServices();
    }

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    public function testExecuteWithNoDataReturnsSuccess(): void
    {
        // 清空数据库，确保没有需要更新的记录
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        $result = $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    private function setUpTestServices(): void
    {
        $this->command = self::getService(RefinePageLogInfoCommand::class);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecuteWithDataButNoSessionMatchReturnsSuccess(): void
    {
        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建测试数据：一个没有 createdBy 的记录
        $pageVisitLog = new PageVisitLog();
        $pageVisitLog->setPage('pages/test/test');
        $pageVisitLog->setRouteId(1);
        $pageVisitLog->setSessionId('test-session-id');
        $pageVisitLog->setCreateTime(new \DateTimeImmutable());
        // 不设置 createdBy，保持为 null

        self::getEntityManager()->persist($pageVisitLog);
        self::getEntityManager()->flush();

        $result = $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    public function testExecuteWithDataAndSessionMatchUpdatesCreatedBy(): void
    {
        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建测试数据：
        // 1. 一个有 createdBy 的记录（作为参考）
        $referenceLog = new PageVisitLog();
        $referenceLog->setPage('pages/reference/reference');
        $referenceLog->setRouteId(1);
        $referenceLog->setSessionId('test-session-id');
        $referenceLog->setCreatedBy('test-user');
        $referenceLog->setCreateTime(new \DateTimeImmutable());

        // 2. 一个没有 createdBy 的记录（需要更新）
        $logToUpdate = new PageVisitLog();
        $logToUpdate->setPage('pages/update/update');
        $logToUpdate->setRouteId(2);
        $logToUpdate->setSessionId('test-session-id');
        $logToUpdate->setCreateTime(new \DateTimeImmutable());
        // 不设置 createdBy，保持为 null

        self::getEntityManager()->persist($referenceLog);
        self::getEntityManager()->persist($logToUpdate);
        self::getEntityManager()->flush();

        $result = $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $result);

        // 验证更新后的记录有了 createdBy
        self::getEntityManager()->refresh($logToUpdate);
        $this->assertEquals('test-user', $logToUpdate->getCreatedBy());
    }

    public function testCommandNameIsCorrect(): void
    {
        $this->assertEquals('wechat-mini-program:refine-page-log-info', RefinePageLogInfoCommand::NAME);
        $this->assertEquals('wechat-mini-program:refine-page-log-info', $this->command->getName());
    }
}
