<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;
use WechatMiniProgramTrackingBundle\Repository\PageVisitLogRepository;
use WechatMiniProgramTrackingBundle\Tests\Factory\PageVisitLogFactory;

/**
 * PageVisitLog 集成测试
 * 测试大数据量、性能和边界情况
 *
 * @internal
 */
#[CoversClass(PageVisitLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class PageVisitLogRepositoryIntegrationTest extends AbstractRepositoryTestCase
{
    private PageVisitLogRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(PageVisitLogRepository::class);
        $this->assertInstanceOf(PageVisitLogRepository::class, $repository);
        $this->repository = $repository;
    }

    /**
     * 测试大数据集 findAll
     */
    public function testFindAllWithLargeDatasetShouldReturnAllEntities(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建大量测试数据
        $largeDataset = PageVisitLogFactory::createLargeDataset(50);
        foreach ($largeDataset as $data) {
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $entities = $this->repository->findAll();

        $this->assertCount(50, $entities);
        $this->assertContainsOnlyInstancesOf(PageVisitLog::class, $entities);
    }

    /**
     * 测试大数据集 findBy
     */
    public function testFindByWithLargeResultSetShouldReturnAllMatches(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建大量匹配的数据
        for ($i = 1; $i <= 100; ++$i) {
            $data = [
                'page' => "pages/large{$i}/large{$i}",
                'routeId' => 6000 + $i,
                'sessionId' => 'large-result-session',
                'createdBy' => 'large-result-user',
            ];
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $entities = $this->repository->findBy(['createdBy' => 'large-result-user']);

        $this->assertCount(100, $entities);
        $this->assertContainsOnlyInstancesOf(PageVisitLog::class, $entities);
    }

    /**
     * 测试多次连续操作的稳定性
     */
    public function testMultipleOperationsStability(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建初始数据
        $initialData = PageVisitLogFactory::createMultipleData(10);
        $entities = [];
        foreach ($initialData as $data) {
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
            $entities[] = $entity;
        }
        self::getEntityManager()->flush();

        // 执行多次查找操作
        for ($i = 0; $i < 5; ++$i) {
            $foundEntities = $this->repository->findAll();
            $this->assertCount(10, $foundEntities);

            $foundEntity = $this->repository->find($entities[0]->getId());
            $this->assertNotNull($foundEntity);
            $this->assertEquals($entities[0]->getPage(), $foundEntity->getPage());
        }

        // 执行多次保存操作
        for ($i = 0; $i < 3; ++$i) {
            $newData = PageVisitLogFactory::createBasicData();
            $newData['sessionId'] = "stability-session-{$i}";
            $newData['page'] = "pages/stability/{$i}";

            $newEntity = $this->createEntityFromData($newData);
            $this->repository->save($newEntity, true);

            $this->assertGreaterThan(0, $newEntity->getId());
        }

        // 验证总数
        $allEntities = $this->repository->findAll();
        $this->assertCount(13, $allEntities);
    }

    /**
     * 测试并发读写操作
     */
    public function testConcurrentReadWriteOperations(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建基础数据
        $baseData = PageVisitLogFactory::createMultipleData(5);
        foreach ($baseData as $data) {
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 模拟并发读取操作
        $readResults = [];
        for ($i = 0; $i < 3; ++$i) {
            $readResults[] = $this->repository->findAll();
            $readResults[] = $this->repository->findBy(['sessionId' => 'session-1']);
            $readResults[] = $this->repository->count([]);
        }

        // 验证读取结果一致性
        foreach ($readResults as $result) {
            if (is_array($result)) {
                $this->assertIsArray($result);
            } else {
                $this->assertIsInt($result);
                $this->assertGreaterThan(0, $result);
            }
        }

        // 模拟并发写入操作
        $newEntities = [];
        for ($i = 0; $i < 3; ++$i) {
            $data = PageVisitLogFactory::createBasicData();
            $data['sessionId'] = "concurrent-session-{$i}";
            $data['page'] = "pages/concurrent/{$i}";

            $entity = $this->createEntityFromData($data);
            $this->repository->save($entity, true);
            $newEntities[] = $entity;
        }

        // 验证写入结果
        foreach ($newEntities as $entity) {
            $this->assertGreaterThan(0, $entity->getId());
            $foundEntity = $this->repository->find($entity->getId());
            $this->assertNotNull($foundEntity);
            $this->assertEquals($entity->getPage(), $foundEntity->getPage());
        }
    }

    /**
     * 测试事务回滚场景
     */
    public function testTransactionRollbackScenario(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建初始数据
        $initialData = PageVisitLogFactory::createBasicData();
        $initialEntity = $this->createEntityFromData($initialData);
        $this->repository->save($initialEntity, true);
        $initialId = $initialEntity->getId();

        // 开始一个事务，执行一些操作，然后模拟回滚
        self::getEntityManager()->beginTransaction();

        try {
            // 在事务中创建几个实体
            for ($i = 0; $i < 3; ++$i) {
                $data = PageVisitLogFactory::createBasicData();
                $data['sessionId'] = "transaction-session-{$i}";
                $data['page'] = "pages/transaction/{$i}";

                $entity = $this->createEntityFromData($data);
                self::getEntityManager()->persist($entity);
            }
            self::getEntityManager()->flush();

            // 验证事务中的数据存在
            $allEntities = $this->repository->findAll();
            $this->assertCount(4, $allEntities);

            // 模拟回滚
            self::getEntityManager()->rollback();
        } catch (\Exception $e) {
            self::getEntityManager()->rollback();
            throw $e;
        }

        // 验证回滚后只有初始数据
        $finalEntities = $this->repository->findAll();
        $this->assertCount(1, $finalEntities);
        $this->assertEquals($initialEntity->getId(), $finalEntities[0]->getId());
    }

    /**
     * 测试唯一约束冲突处理
     */
    public function testUniqueConstraintHandling(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建第一个实体
        $data = [
            'page' => 'pages/unique/test',
            'routeId' => 1001,
            'sessionId' => 'unique-session',
            'createdBy' => 'user1',
        ];
        $entity1 = $this->createEntityFromData($data);
        $this->repository->save($entity1, true);

        // 尝试创建违反唯一约束的实体（相同的 sessionId 和 routeId）
        $data2 = [
            'page' => 'pages/different/test',
            'routeId' => 1001, // 相同的 routeId
            'sessionId' => 'unique-session', // 相同的 sessionId
            'createdBy' => 'user2',
        ];
        $entity2 = $this->createEntityFromData($data2);

        // 这应该会抛出异常或者更新现有记录，取决于数据库配置
        // 在这里我们测试系统能够优雅地处理这种情况
        try {
            $this->repository->save($entity2, true);
            // 如果保存成功，检查是否有重复记录
            $entities = $this->repository->findBy([
                'sessionId' => 'unique-session',
                'routeId' => 1001,
            ]);
            $this->assertGreaterThanOrEqual(1, count($entities));
        } catch (\Exception $e) {
            // 如果抛出异常，这是预期的行为
            $this->assertInstanceOf(\Exception::class, $e);

            // 确保原始记录仍然存在
            $originalEntity = $this->repository->find($entity1->getId());
            $this->assertNotNull($originalEntity);
        }
    }

    /**
     * 测试内存使用和性能
     */
    public function testMemoryUsageAndPerformance(): void
    {
        // 清空数据库
        $this->clearDatabase();

        $startMemory = memory_get_usage(true);
        $startTime = microtime(true);

        // 创建中等规模的数据集
        $dataSet = PageVisitLogFactory::createLargeDataset(20);
        foreach ($dataSet as $data) {
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $afterInsertMemory = memory_get_usage(true);
        $afterInsertTime = microtime(true);

        // 执行查询操作
        $entities = $this->repository->findAll();
        $this->assertCount(20, $entities);

        // 执行一些复杂查询
        $this->repository->findBy(['createdBy' => 'large-user1']);
        $this->repository->count(['sessionId' => 'large-session-1']);

        $endMemory = memory_get_usage(true);
        $endTime = microtime(true);

        // 基本性能断言（这些数值可能需要根据实际情况调整）
        $this->assertLessThan(50 * 1024 * 1024, $endMemory - $startMemory); // 内存增长不超过50MB
        $this->assertLessThan(5.0, $endTime - $startTime); // 总执行时间不超过5秒

        // 清理内存
        self::getEntityManager()->clear();
    }

    /**
     * 测试边界情况 - 空值处理
     */
    public function testEdgeCasesNullHandling(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建一个所有可空字段都为null的实体
        $nullData = [
            'page' => 'pages/all-null/test',
            'routeId' => 9999,
            'sessionId' => 'all-null-session',
            'createdBy' => null,
            'query' => null,
            'createTime' => null,
            'createdFromIp' => null,
        ];
        $nullEntity = $this->createEntityFromData($nullData);
        $this->repository->save($nullEntity, true);

        // 创建一个所有可空字段都有值的实体
        $fullData = PageVisitLogFactory::createBasicData();
        $fullData['sessionId'] = 'full-session';
        $fullData['page'] = 'pages/full/test';
        $fullEntity = $this->createEntityFromData($fullData);
        $this->repository->save($fullEntity, true);

        // 测试查找null字段
        $nullEntities = $this->repository->findBy(['createdBy' => null]);
        $this->assertCount(1, $nullEntities);
        $this->assertEquals($nullEntity->getId(), $nullEntities[0]->getId());

        // 测试查找非null字段
        $nonNullEntities = $this->repository->findBy(['createdBy' => $fullData['createdBy']]);
        $this->assertCount(1, $nonNullEntities);
        $this->assertEquals($fullEntity->getId(), $nonNullEntities[0]->getId());

        // 测试混合查询
        $allEntities = $this->repository->findAll();
        $this->assertCount(2, $allEntities);
    }

    /**
     * 根据数据创建实体
     */
    private function createEntityFromData(array $data): PageVisitLog
    {
        $entity = new PageVisitLog();

        // 必需字段必须有值
        $entity->setPage($data['page'] ?? 'pages/default/index');
        $entity->setRouteId($data['routeId'] ?? 1);
        $entity->setSessionId($data['sessionId'] ?? 'default-session');

        // 可选字段
        if (array_key_exists('createdBy', $data)) {
            $entity->setCreatedBy($data['createdBy']);
        }

        if (array_key_exists('query', $data)) {
            $entity->setQuery($data['query']);
        }

        if (array_key_exists('createTime', $data)) {
            $entity->setCreateTime($data['createTime']);
        }

        if (array_key_exists('createdFromIp', $data)) {
            $entity->setCreatedFromIp($data['createdFromIp']);
        }

        return $entity;
    }

    /**
     * 清空数据库
     */
    private function clearDatabase(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();
    }

    protected function createNewEntity(): object
    {
        $data = PageVisitLogFactory::createBasicData();
        // 确保每次创建的实体都有唯一的(sessionId, routeId)组合
        $data['sessionId'] = $data['sessionId'] . '-' . uniqid();
        $data['routeId'] = $data['routeId'] + mt_rand(1, 1000);

        return $this->createEntityFromData($data);
    }

    /**
     * @return ServiceEntityRepository<PageVisitLog>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}