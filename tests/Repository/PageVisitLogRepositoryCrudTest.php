<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;
use WechatMiniProgramTrackingBundle\Repository\PageVisitLogRepository;
use WechatMiniProgramTrackingBundle\Tests\Factory\PageVisitLogFactory;

/**
 * PageVisitLog 基础 CRUD 操作测试
 *
 * @internal
 */
#[CoversClass(PageVisitLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class PageVisitLogRepositoryCrudTest extends AbstractRepositoryTestCase
{
    private PageVisitLogRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(PageVisitLogRepository::class);
        $this->assertInstanceOf(PageVisitLogRepository::class, $repository);
        $this->repository = $repository;
    }

    /**
     * 测试仓储可以保存和查找实体
     */
    #[DataProvider('basicCrudProvider')]
    public function testRepositoryCanSaveAndFindEntity(array $data): void
    {
        $entity = $this->createEntityFromData($data);

        // 保存实体
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 验证实体被保存
        $this->assertGreaterThan(0, $entity->getId());

        // 通过仓储查找实体
        $foundEntity = $this->repository->find($entity->getId());

        $this->assertNotNull($foundEntity);
        $this->assertEquals($entity->getId(), $foundEntity->getId());
        $this->assertEquals($data['page'], $foundEntity->getPage());
        $this->assertEquals($data['routeId'], $foundEntity->getRouteId());
        $this->assertEquals($data['sessionId'], $foundEntity->getSessionId());
        $this->assertEquals($data['createdBy'], $foundEntity->getCreatedBy());
        $this->assertEquals($data['query'], $foundEntity->getQuery());
        $this->assertEquals($data['createdFromIp'], $foundEntity->getCreatedFromIp());
    }

    /**
     * 测试 findAll 方法
     */
    public function testRepositoryFindAll(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建测试数据
        $entities = $this->createMultipleEntities(3);
        foreach ($entities as $entity) {
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 测试 findAll
        $foundEntities = $this->repository->findAll();

        $this->assertCount(3, $foundEntities);
        $this->assertContainsOnlyInstancesOf(PageVisitLog::class, $foundEntities);
    }

    /**
     * 测试 findBy 方法
     */
    #[DataProvider('findByProvider')]
    public function testRepositoryFindBy(array $criteria, array $entityData): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建测试数据
        $entity = $this->createEntityFromData($entityData);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试查找
        $foundEntities = $this->repository->findBy($criteria);

        $this->assertCount(1, $foundEntities);
        $this->assertEquals($entityData['page'], $foundEntities[0]->getPage());
    }

    /**
     * 测试查找不存在的记录返回空数组
     */
    public function testRepositoryFindByWithNonExistentCriteriaShouldReturnEmptyArray(): void
    {
        $entities = $this->repository->findBy(['sessionId' => 'non-existent-session']);

        $this->assertIsArray($entities);
        $this->assertEmpty($entities);
    }

    /**
     * 测试 findOneBy 方法
     */
    public function testRepositoryFindOneBy(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建测试数据
        $entity = $this->createEntityFromData(PageVisitLogFactory::createBasicData());
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试根据唯一条件查找
        $foundEntity = $this->repository->findOneBy([
            'sessionId' => $entity->getSessionId(),
            'routeId' => $entity->getRouteId(),
        ]);

        $this->assertNotNull($foundEntity);
        $this->assertEquals($entity->getSessionId(), $foundEntity->getSessionId());
        $this->assertEquals($entity->getRouteId(), $foundEntity->getRouteId());
        $this->assertEquals($entity->getCreatedBy(), $foundEntity->getCreatedBy());

        // 测试查找不存在的实体
        $notFoundEntity = $this->repository->findOneBy(['sessionId' => 'non-existent']);
        $this->assertNull($notFoundEntity);
    }

    /**
     * 测试带排序和限制的查询
     */
    public function testRepositoryWithOrderByAndLimit(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建多个测试数据
        $entities = $this->createMultipleEntities(5);
        foreach ($entities as $entity) {
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 测试带排序和限制的查询
        $foundEntities = $this->repository->findBy(
            [],
            ['id' => 'DESC'],
            3
        );

        $this->assertCount(3, $foundEntities);

        // 验证按 ID 降序排列
        $ids = array_map(fn ($entity) => $entity->getId(), $foundEntities);
        $sortedIds = $ids;
        rsort($sortedIds);
        $this->assertEquals($sortedIds, $ids);
    }

    /**
     * 测试 count 方法
     */
    public function testRepositoryCount(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建测试数据
        $entities = $this->createMultipleEntities(3);
        foreach ($entities as $entity) {
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 测试总数计数
        $count = $this->repository->count([]);
        $this->assertEquals(3, $count);

        // 测试按条件计数
        $count = $this->repository->count(['sessionId' => $entities[0]->getSessionId()]);
        $this->assertEquals(1, $count);
    }

    /**
     * 测试 save 方法
     */
    public function testRepositorySave(): void
    {
        $entity = $this->createEntityFromData(PageVisitLogFactory::createBasicData());

        // 保存并刷新
        $this->repository->save($entity, true);

        // 验证实体已保存
        $this->assertGreaterThan(0, $entity->getId());

        // 重新获取验证
        $foundEntity = $this->repository->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertEquals($entity->getPage(), $foundEntity->getPage());
    }

    /**
     * 测试 save 方法不立即刷新
     */
    public function testRepositorySaveWithoutFlush(): void
    {
        $entity = $this->createEntityFromData(PageVisitLogFactory::createBasicData());

        // 保存但不刷新
        $this->repository->save($entity, false);

        // 实体应该还没有 ID
        $this->assertEquals(0, $entity->getId());

        // 手动刷新
        self::getEntityManager()->flush();

        // 现在应该有 ID 了
        $this->assertGreaterThan(0, $entity->getId());
    }

    /**
     * 测试 remove 方法
     */
    public function testRepositoryRemove(): void
    {
        // 创建并保存实体
        $entity = $this->createEntityFromData(PageVisitLogFactory::createBasicData());
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();
        $entityId = $entity->getId();

        // 删除实体
        $this->repository->remove($entity, true);

        // 验证实体已删除
        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    /**
     * 测试 remove 方法不立即刷新
     */
    public function testRepositoryRemoveWithoutFlush(): void
    {
        // 创建并保存实体
        $entity = $this->createEntityFromData(PageVisitLogFactory::createBasicData());
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();
        $entityId = $entity->getId();

        // 删除但不刷新
        $this->repository->remove($entity, false);

        // 实体应该还存在
        $foundEntity = $this->repository->find($entityId);
        $this->assertNotNull($foundEntity);

        // 手动刷新
        self::getEntityManager()->flush();

        // 现在应该已删除
        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    /**
     * 测试使用字符串ID查找返回null
     */
    public function testFindWithStringIdShouldReturnNull(): void
    {
        $result = $this->repository->find('invalid');
        $this->assertNull($result);
    }

    /**
     * 根据数据创建实体
     *
     * @param array<string, mixed> $data
     */
    private function createEntityFromData(array $data): PageVisitLog
    {
        $entity = new PageVisitLog();

        // 必需字段必须有值
        if (array_key_exists('page', $data)) {
            $this->assertTrue(isset($data['page']), 'page must be set when key exists');
        }
        if (array_key_exists('routeId', $data)) {
            $this->assertTrue(isset($data['routeId']), 'routeId must be set when key exists');
        }
        if (array_key_exists('sessionId', $data)) {
            $this->assertTrue(isset($data['sessionId']), 'sessionId must be set when key exists');
        }

        $page = $data['page'] ?? 'pages/default/index';
        $routeId = $data['routeId'] ?? 1;
        $sessionId = $data['sessionId'] ?? 'default-session';

        $this->assertIsString($page, 'page must be string');
        $this->assertIsInt($routeId, 'routeId must be int');
        $this->assertIsString($sessionId, 'sessionId must be string');

        $entity->setPage($page);
        $entity->setRouteId($routeId);
        $entity->setSessionId($sessionId);

        // 可选字段
        if (array_key_exists('createdBy', $data)) {
            $this->assertTrue($data['createdBy'] === null || is_string($data['createdBy']), 'createdBy must be null or string');
            $entity->setCreatedBy($data['createdBy']);
        }

        if (array_key_exists('query', $data)) {
            $this->assertTrue($data['query'] === null || is_array($data['query']), 'query must be null or array');
            if ($data['query'] !== null) {
                // 确保数组的所有键都是字符串类型
                $data['query'] = array_combine(
                    array_map('strval', array_keys($data['query'])),
                    array_values($data['query'])
                );
            }
            $entity->setQuery($data['query']);
        }

        if (array_key_exists('createTime', $data)) {
            $this->assertTrue($data['createTime'] === null || $data['createTime'] instanceof \DateTimeImmutable, 'createTime must be null or DateTimeImmutable');
            $entity->setCreateTime($data['createTime']);
        }

        if (array_key_exists('createdFromIp', $data)) {
            $this->assertTrue($data['createdFromIp'] === null || is_string($data['createdFromIp']), 'createdFromIp must be null or string');
            $entity->setCreatedFromIp($data['createdFromIp']);
        }

        return $entity;
    }

    /**
     * 创建多个测试实体
     *
     * @return PageVisitLog[]
     */
    private function createMultipleEntities(int $count): array
    {
        $entities = [];
        $dataList = PageVisitLogFactory::createMultipleData($count);

        foreach ($dataList as $data) {
            // 确保sessionId唯一
            $data['sessionId'] = $data['sessionId'] . '-' . uniqid();
            $entities[] = $this->createEntityFromData($data);
        }

        return $entities;
    }

    /**
     * 清空数据库
     */
    private function clearDatabase(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();
    }

    /**
     * 数据提供器 - 基础CRUD测试
     *
     * @return array<string, array{0: array<string, mixed>}>
     */
    public static function basicCrudProvider(): array
    {
        return PageVisitLogFactory::basicCrudProvider();
    }

    /**
     * 数据提供器 - 查找测试
     *
     * @return array<string, array{0: array<string, mixed>, 1: array<string, mixed>}>
     */
    public static function findByProvider(): array
    {
        return PageVisitLogFactory::findByProvider();
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