<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;
use WechatMiniProgramTrackingBundle\Repository\PageVisitLogRepository;

/**
 * @internal
 *
 * @extends AbstractRepositoryTestCase<PageVisitLog>
 */
#[CoversClass(PageVisitLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class PageVisitLogRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // 无需额外初始化
    }

    protected function getRepository(): ServiceEntityRepository
    {
        return self::getService(PageVisitLogRepository::class);
    }

    protected function createNewEntity(): object
    {
        $log = new PageVisitLog();
        $log->setPage('pages/test/index');
        $log->setSessionId('test-session-' . uniqid());
        $log->setRouteId(rand(1, 1000000));

        return $log;
    }

    /**
     * 测试 save 方法
     */
    public function testSave(): void
    {
        $log = new PageVisitLog();
        $log->setPage('pages/test/index');
        $log->setSessionId('test-session-123');
        $log->setRouteId(456);

        /** @var PageVisitLogRepository $repository */
        $repository = $this->getRepository();
        $repository->save($log);

        // 验证保存成功（有 ID）
        $this->assertNotNull($log->getId());
    }

    /**
     * 测试 remove 方法
     */
    public function testRemove(): void
    {
        // 先创建一个实体
        $log = new PageVisitLog();
        $log->setPage('pages/remove/index');
        $log->setSessionId('remove-session');
        $log->setRouteId(111);

        /** @var PageVisitLogRepository $repository */
        $repository = $this->getRepository();
        $repository->save($log);
        $id = $log->getId();

        // 删除实体
        $repository->remove($log);

        // 验证删除成功
        $found = $repository->find($id);
        $this->assertNull($found);
    }

    /**
     * 测试保存带查询参数的日志
     */
    public function testSaveWithQuery(): void
    {
        $log = new PageVisitLog();
        $log->setPage('pages/query/index');
        $log->setQuery(['id' => '123', 'tab' => 'info']);
        $log->setSessionId('query-session');
        $log->setRouteId(222);

        /** @var PageVisitLogRepository $repository */
        $repository = $this->getRepository();
        $repository->save($log);

        $this->assertNotNull($log->getId());
        $this->assertSame(['id' => '123', 'tab' => 'info'], $log->getQuery());
    }

    /**
     * 测试保存带创建者的日志
     */
    public function testSaveWithCreatedBy(): void
    {
        $log = new PageVisitLog();
        $log->setPage('pages/creator/index');
        $log->setSessionId('creator-session');
        $log->setRouteId(333);
        $log->setCreatedBy('test-user-identifier');

        /** @var PageVisitLogRepository $repository */
        $repository = $this->getRepository();
        $repository->save($log);

        $this->assertNotNull($log->getId());
        $this->assertSame('test-user-identifier', $log->getCreatedBy());
    }
}
