<?php

namespace WechatMiniProgramTrackingBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;
use WechatMiniProgramTrackingBundle\Repository\PageNotFoundLogRepository;

/**
 * @internal
 */
#[CoversClass(PageNotFoundLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class PageNotFoundLogRepositoryTest extends AbstractRepositoryTestCase
{
    private PageNotFoundLogRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(PageNotFoundLogRepository::class);
        $this->assertInstanceOf(PageNotFoundLogRepository::class, $repository);
        $this->repository = $repository;
    }

    private function setUpTestServices(): void
    {
        /** @var PageNotFoundLogRepository $repository */
        $repository = self::getContainer()->get(PageNotFoundLogRepository::class);
        $this->assertInstanceOf(PageNotFoundLogRepository::class, $repository);
        $this->repository = $repository;
    }

    #[TestWith([['path' => 'pages/notfound/notfound', 'openId' => 'test-open-id-123', 'unionId' => 'test-union-id-123']])]
    #[TestWith([['path' => 'pages/special/chars?query=value', 'openId' => 'special-open-id-456', 'unionId' => 'special-union-id-456']])]
    public function testRepositoryCanSaveAndFindEntity(array $data): void
    {
        $this->setUpTestServices();

        // 创建实体
        $entity = new PageNotFoundLog();
        $entity->setPath($data['path']);
        $entity->setOpenId($data['openId']);
        $entity->setUnionId($data['unionId']);

        // 保存实体
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 验证实体被保存
        $this->assertGreaterThan(0, $entity->getId());

        // 通过仓储查找实体
        $foundEntity = $this->repository->find($entity->getId());

        $this->assertNotNull($foundEntity);
        $this->assertEquals($entity->getId(), $foundEntity->getId());
        $this->assertEquals($data['path'], $foundEntity->getPath());
        $this->assertEquals($data['openId'], $foundEntity->getOpenId());
        $this->assertEquals($data['unionId'], $foundEntity->getUnionId());
    }

    public function testRepositoryFindAll(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/error1/error1');
        $entity1->setOpenId('open-id-1');
        $entity1->setUnionId('union-id-1');

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/error2/error2');
        $entity2->setOpenId('open-id-2');
        $entity2->setUnionId('union-id-2');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试 findAll
        $entities = $this->repository->findAll();

        $this->assertCount(2, $entities);
        $this->assertContainsOnlyInstancesOf(PageNotFoundLog::class, $entities);
    }

    #[TestWith([['openId' => 'same-open-id'], [
            [
                'path' => 'pages/error/error',
                'openId' => 'same-open-id',
                'unionId' => 'union-id-1',
            ],
            [
                'path' => 'pages/other/other',
                'openId' => 'same-open-id',
                'unionId' => 'union-id-2',
            ],
            [
                'path' => 'pages/different/different',
                'openId' => 'different-open-id',
                'unionId' => 'union-id-3',
            ],
        ], 2], 'find_by_openid_multiple_results')]
    #[TestWith([['unionId' => 'union-id-1'], [
            [
                'path' => 'pages/error/error',
                'openId' => 'same-open-id',
                'unionId' => 'union-id-1',
            ],
            [
                'path' => 'pages/other/other',
                'openId' => 'same-open-id',
                'unionId' => 'union-id-2',
            ],
        ], 1], 'find_by_unionid_single_result')]
    public function testRepositoryFindBy(array $criteria, array $entityData, int $expectedCount): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试数据
        $entities = [];
        foreach ($entityData as $data) {
            $entity = new PageNotFoundLog();
            $entity->setPath($data['path']);
            $entity->setOpenId($data['openId']);
            $entity->setUnionId($data['unionId']);

            self::getEntityManager()->persist($entity);
            $entities[] = $entity;
        }
        self::getEntityManager()->flush();

        // 测试查找
        $foundEntities = $this->repository->findBy($criteria);

        $this->assertCount($expectedCount, $foundEntities);
        foreach ($foundEntities as $entity) {
            if (isset($criteria['openId'])) {
                $this->assertEquals($criteria['openId'], $entity->getOpenId());
            }
            if (isset($criteria['unionId'])) {
                $this->assertEquals($criteria['unionId'], $entity->getUnionId());
            }
        }
    }

    public function testRepositoryFindOneBy(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试数据
        $entity = new PageNotFoundLog();
        $entity->setPath('pages/unique/unique');
        $entity->setOpenId('unique-open-id');
        $entity->setUnionId('unique-union-id');

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试根据唯一条件查找
        $foundEntity = $this->repository->findOneBy(['openId' => 'unique-open-id']);

        $this->assertNotNull($foundEntity);
        $this->assertEquals('unique-open-id', $foundEntity->getOpenId());
        $this->assertEquals('unique-union-id', $foundEntity->getUnionId());

        // 测试查找不存在的实体
        $notFoundEntity = $this->repository->findOneBy(['openId' => 'non-existent']);

        $this->assertNull($notFoundEntity);
    }

    public function testRepositoryWithOrderByAndLimit(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建多个测试数据
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new PageNotFoundLog();
            $entity->setPath("pages/error{$i}/error{$i}");
            $entity->setOpenId("open-id-{$i}");
            $entity->setUnionId("union-id-{$i}");

            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 测试带排序和限制的查询
        $entities = $this->repository->findBy(
            [],
            ['id' => 'DESC'],
            3
        );

        $this->assertCount(3, $entities);

        // 验证按 ID 降序排列
        $ids = array_map(fn ($entity) => $entity->getId(), $entities);
        $sortedIds = $ids;
        rsort($sortedIds);
        $this->assertEquals($sortedIds, $ids);
    }

    public function testRepositoryFindByPagePattern(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/api/user');
        $entity1->setOpenId('open-id-1');
        $entity1->setUnionId('union-id-1');

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/api/product');
        $entity2->setOpenId('open-id-2');
        $entity2->setUnionId('union-id-2');

        $entity3 = new PageNotFoundLog();
        $entity3->setPath('pages/web/home');
        $entity3->setOpenId('open-id-3');
        $entity3->setUnionId('union-id-3');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 使用查询构建器测试页面路径匹配
        /** @var PageNotFoundLog[] $apiPages */
        $apiPages = $this->repository->createQueryBuilder('p')
            ->where('p.path LIKE :pattern')
            ->setParameter('pattern', 'pages/api/%')
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(2, $apiPages);
        foreach ($apiPages as $page) {
            $this->assertStringStartsWith('pages/api/', (string) $page->getPath());
        }
    }

    public function testCountWithSpecificCriteriaShouldReturnFilteredNumber(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/count-filter/count-filter');
        $entity1->setOpenId('filter-openid');
        $entity1->setUnionId('filter-unionid-1');
        $entity1->setOpenType('navigate');

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/count-other/count-other');
        $entity2->setOpenId('filter-openid');
        $entity2->setUnionId('filter-unionid-2');
        $entity2->setOpenType('redirect');

        $entity3 = new PageNotFoundLog();
        $entity3->setPath('pages/count-different/count-different');
        $entity3->setOpenId('different-openid');
        $entity3->setUnionId('different-unionid');
        $entity3->setOpenType('navigate');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试按特定条件计数
        $count = $this->repository->count(['openId' => 'filter-openid']);
        $this->assertEquals(2, $count);

        $countByOpenType = $this->repository->count(['openType' => 'navigate']);
        $this->assertEquals(2, $countByOpenType);
    }

    public function testFindByWithValidCriteriaShouldReturnMatchingEntities(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/findby-test/findby-test');
        $entity1->setOpenId('findby-openid');
        $entity1->setUnionId('findby-unionid-1');
        $entity1->setOpenType('navigate');
        $entity1->setRawError('findby error 1');

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/findby-other/findby-other');
        $entity2->setOpenId('findby-openid');
        $entity2->setUnionId('findby-unionid-2');
        $entity2->setOpenType('redirect');
        $entity2->setRawError('findby error 2');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试按openId查找
        $entitiesByOpenId = $this->repository->findBy(['openId' => 'findby-openid']);
        $this->assertCount(2, $entitiesByOpenId);

        // 测试按openType查找
        $entitiesByOpenType = $this->repository->findBy(['openType' => 'navigate']);
        $this->assertCount(1, $entitiesByOpenType);
        $this->assertEquals('navigate', $entitiesByOpenType[0]->getOpenType());

        // 测试按unionId查找
        $entitiesByUnionId = $this->repository->findBy(['unionId' => 'findby-unionid-1']);
        $this->assertCount(1, $entitiesByUnionId);
        $this->assertEquals('findby-unionid-1', $entitiesByUnionId[0]->getUnionId());
    }

    public function testFindByWithNonExistentCriteriaShouldReturnEmptyArray(): void
    {
        $this->setUpTestServices();

        $entities = $this->repository->findBy(['openId' => 'non-existent-openid']);

        $this->assertIsArray($entities);
        $this->assertEmpty($entities);
    }

    public function testFindByWithNullValuesShouldReturnEntitiesWithNullFields(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建带null字段的测试数据
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/null-test/null-test');
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);
        $entity1->setOpenType(null);
        $entity1->setRawError(null);

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/not-null-test/not-null-test');
        $entity2->setOpenId('test-openid');
        $entity2->setUnionId('test-unionid');
        $entity2->setOpenType('navigate');
        $entity2->setRawError('test error');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试查找openId为null的实体
        $entitiesWithNullOpenId = $this->repository->findBy(['openId' => null]);
        $this->assertCount(1, $entitiesWithNullOpenId);
        $this->assertArrayHasKey(0, $entitiesWithNullOpenId);
        $this->assertNull($entitiesWithNullOpenId[0]->getOpenId());
        $this->assertEquals('pages/null-test/null-test', $entitiesWithNullOpenId[0]->getPath());

        // 测试查找unionId为null的实体
        $entitiesWithNullUnionId = $this->repository->findBy(['unionId' => null]);
        $this->assertCount(1, $entitiesWithNullUnionId);
        $this->assertArrayHasKey(0, $entitiesWithNullUnionId);
        $this->assertNull($entitiesWithNullUnionId[0]->getUnionId());

        // 测试查找openType为null的实体
        $entitiesWithNullOpenType = $this->repository->findBy(['openType' => null]);
        $this->assertCount(1, $entitiesWithNullOpenType);
        $this->assertArrayHasKey(0, $entitiesWithNullOpenType);
        $this->assertNull($entitiesWithNullOpenType[0]->getOpenType());

        // 测试查找rawError为null的实体
        $entitiesWithNullRawError = $this->repository->findBy(['rawError' => null]);
        $this->assertCount(1, $entitiesWithNullRawError);
        $this->assertArrayHasKey(0, $entitiesWithNullRawError);
        $this->assertNull($entitiesWithNullRawError[0]->getRawError());
    }

    public function testFindByWithMultipleCriteriaShouldReturnMatchingEntities(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/multi-criteria/multi-criteria');
        $entity1->setOpenId('multi-openid');
        $entity1->setUnionId('multi-unionid');
        $entity1->setOpenType('navigate');

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/multi-other/multi-other');
        $entity2->setOpenId('multi-openid');
        $entity2->setUnionId('other-unionid');
        $entity2->setOpenType('navigate');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试多条件查询
        $entities = $this->repository->findBy([
            'openId' => 'multi-openid',
            'openType' => 'navigate',
            'unionId' => 'multi-unionid',
        ]);

        $this->assertCount(1, $entities);
        $this->assertEquals('multi-unionid', $entities[0]->getUnionId());
        $this->assertEquals('multi-openid', $entities[0]->getOpenId());
        $this->assertEquals('navigate', $entities[0]->getOpenType());
    }

    public function testSaveEntityShouldPersistToDatabase(): void
    {
        $this->setUpTestServices();

        // 创建新实体
        $entity = new PageNotFoundLog();
        $entity->setPath('pages/save-test/save-test');
        $entity->setOpenId('save-test-openid');
        $entity->setUnionId('save-test-unionid');
        $entity->setOpenType('navigate');
        $entity->setRawError('save test error');
        $entity->setQuery(['key' => 'value', 'test' => 'data']);

        // 保存实体
        $this->repository->save($entity);

        // 验证实体被保存
        $this->assertGreaterThan(0, $entity->getId());

        // 从数据库重新查找验证
        $foundEntity = $this->repository->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertEquals('pages/save-test/save-test', $foundEntity->getPath());
        $this->assertEquals('save-test-openid', $foundEntity->getOpenId());
        $this->assertEquals('save-test-unionid', $foundEntity->getUnionId());
        $this->assertEquals('navigate', $foundEntity->getOpenType());
        $this->assertEquals('save test error', $foundEntity->getRawError());
        $this->assertEquals(['key' => 'value', 'test' => 'data'], $foundEntity->getQuery());
    }

    public function testSaveEntityWithoutFlushShouldNotPersistImmediately(): void
    {
        $this->setUpTestServices();

        // 创建新实体
        $entity = new PageNotFoundLog();
        $entity->setPath('pages/save-no-flush/save-no-flush');
        $entity->setOpenId('save-no-flush-openid');
        $entity->setUnionId('save-no-flush-unionid');

        // 获取当前实体总数
        $initialCount = $this->repository->count([]);

        // 保存但不刷新
        $this->repository->save($entity, false);

        // 验证实体已经获得了ID（因为使用Snowflake生成器）
        $this->assertGreaterThan(0, $entity->getId());

        // 验证实体计数没有立即增加（需要flush才会生效）
        $countAfterSave = $this->repository->count([]);
        $this->assertEquals($initialCount, $countAfterSave);

        // 手动刷新
        self::getEntityManager()->flush();

        // 现在计数应该增加了
        $countAfterFlush = $this->repository->count([]);
        $this->assertEquals($initialCount + 1, $countAfterFlush);

        // 验证能够查询到实体
        $foundEntity = $this->repository->find($entity->getId());
        $this->assertNotNull($foundEntity);
    }

    public function testRemoveEntityShouldDeleteFromDatabase(): void
    {
        $this->setUpTestServices();

        // 创建并保存实体
        $entity = new PageNotFoundLog();
        $entity->setPath('pages/remove-test/remove-test');
        $entity->setOpenId('remove-test-openid');
        $entity->setUnionId('remove-test-unionid');

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $entityId = $entity->getId();
        $this->assertGreaterThan(0, $entityId);

        // 验证实体存在
        $foundEntity = $this->repository->find($entityId);
        $this->assertNotNull($foundEntity);

        // 删除实体
        $this->repository->remove($entity);

        // 验证实体被删除
        $deletedEntity = $this->repository->find($entityId);
        $this->assertNull($deletedEntity);
    }

    public function testRemoveEntityWithoutFlushShouldNotDeleteImmediately(): void
    {
        $this->setUpTestServices();

        // 创建并保存实体
        $entity = new PageNotFoundLog();
        $entity->setPath('pages/remove-no-flush/remove-no-flush');
        $entity->setOpenId('remove-no-flush-openid');
        $entity->setUnionId('remove-no-flush-unionid');

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $entityId = $entity->getId();
        $this->assertGreaterThan(0, $entityId);

        // 删除但不刷新
        $this->repository->remove($entity, false);

        // 实体应该仍然存在
        $foundEntity = $this->repository->find($entityId);
        $this->assertNotNull($foundEntity);

        // 手动刷新
        self::getEntityManager()->flush();

        // 现在实体应该被删除
        $deletedEntity = $this->repository->find($entityId);
        $this->assertNull($deletedEntity);
    }

    public function testFindByWithNullableFieldsIsNullQuery(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建多个测试实体，有些字段为null，有些不为null
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/nullable1/nullable1');
        // 以下字段设为null
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);
        $entity1->setOpenType(null);
        $entity1->setRawError(null);

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/nullable2/nullable2');
        // 以下字段设为非null值
        $entity2->setOpenId('test-openid-2');
        $entity2->setUnionId('test-unionid-2');
        $entity2->setOpenType('navigate');
        $entity2->setRawError('test error message');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试查找openId为null的记录
        $entitiesWithNullOpenId = $this->repository->findBy(['openId' => null]);
        $this->assertCount(1, $entitiesWithNullOpenId);
        $this->assertNull($entitiesWithNullOpenId[0]->getOpenId());
        $this->assertEquals('pages/nullable1/nullable1', $entitiesWithNullOpenId[0]->getPath());

        // 测试查找unionId为null的记录
        $entitiesWithNullUnionId = $this->repository->findBy(['unionId' => null]);
        $this->assertCount(1, $entitiesWithNullUnionId);
        $this->assertNull($entitiesWithNullUnionId[0]->getUnionId());

        // 测试查找openType为null的记录
        $entitiesWithNullOpenType = $this->repository->findBy(['openType' => null]);
        $this->assertCount(1, $entitiesWithNullOpenType);
        $this->assertNull($entitiesWithNullOpenType[0]->getOpenType());

        // 测试查找rawError为null的记录
        $entitiesWithNullRawError = $this->repository->findBy(['rawError' => null]);
        $this->assertCount(1, $entitiesWithNullRawError);
        $this->assertNull($entitiesWithNullRawError[0]->getRawError());
    }

    public function testFindWithStringIdShouldReturnNull(): void
    {
        $this->setUpTestServices();

        // PHP会尝试将字符串转换为整数，'invalid-id-type'会转换为0
        $foundEntity = $this->repository->find('invalid-id-type');

        $this->assertNull($foundEntity);
    }

    public function testFindAllWithLargeDatasetShouldReturnAllEntities(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建大量测试数据
        for ($i = 1; $i <= 10; ++$i) {
            $entity = new PageNotFoundLog();
            $entity->setPath("pages/large{$i}/large{$i}");
            $entity->setOpenId("large-openid-{$i}");
            $entity->setUnionId("large-unionid-{$i}");

            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $entities = $this->repository->findAll();

        $this->assertCount(10, $entities);
        $this->assertContainsOnlyInstancesOf(PageNotFoundLog::class, $entities);
    }

    public function testFindByWithLargeResultSetShouldReturnAllMatches(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建大量相同条件的测试数据
        for ($i = 1; $i <= 15; ++$i) {
            $entity = new PageNotFoundLog();
            $entity->setPath("pages/large-result{$i}/large-result{$i}");
            $entity->setOpenId('large-result-openid');
            $entity->setUnionId("large-result-unionid-{$i}");

            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $entities = $this->repository->findBy(['openId' => 'large-result-openid']);

        $this->assertCount(15, $entities);
        $this->assertContainsOnlyInstancesOf(PageNotFoundLog::class, $entities);
        foreach ($entities as $entity) {
            $this->assertEquals('large-result-openid', $entity->getOpenId());
        }
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建多个相同条件的测试数据
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/order-findoneby1/order-findoneby1');
        $entity1->setOpenId('same-openid');
        $entity1->setUnionId('unionid-b');

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/order-findoneby2/order-findoneby2');
        $entity2->setOpenId('same-openid');
        $entity2->setUnionId('unionid-a');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试按unionId升序排序，应该返回第一个匹配的实体
        $foundEntity = $this->repository->findOneBy(['openId' => 'same-openid'], ['unionId' => 'ASC']);

        $this->assertNotNull($foundEntity);
        $this->assertEquals('unionid-a', $foundEntity->getUnionId());

        // 测试按unionId降序排序
        $foundEntityDesc = $this->repository->findOneBy(['openId' => 'same-openid'], ['unionId' => 'DESC']);

        $this->assertNotNull($foundEntityDesc);
        $this->assertEquals('unionid-b', $foundEntityDesc->getUnionId());
    }

    public function testFindOneByWithMultipleMatchesShouldReturnFirstEntity(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建多个匹配的实体
        for ($i = 1; $i <= 3; ++$i) {
            $entity = new PageNotFoundLog();
            $entity->setPath("pages/multiple{$i}/multiple{$i}");
            $entity->setOpenId('multiple-match-openid');
            $entity->setUnionId("multiple-unionid-{$i}");

            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // findOneBy应该返回第一个匹配的实体
        $foundEntity = $this->repository->findOneBy(['openId' => 'multiple-match-openid']);

        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(PageNotFoundLog::class, $foundEntity);
        $this->assertEquals('multiple-match-openid', $foundEntity->getOpenId());
        // 应该返回第一个创建的实体
        $this->assertEquals('multiple-unionid-1', $foundEntity->getUnionId());
    }

    public function testFindOneByWithNullableFieldsIsNullQuery(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/findoneby-null1/findoneby-null1');
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);
        $entity1->setOpenType(null);

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/findoneby-null2/findoneby-null2');
        $entity2->setOpenId('findoneby-openid');
        $entity2->setUnionId('findoneby-unionid');
        $entity2->setOpenType('navigate');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试findOneBy查找null值
        $entityWithNullOpenId = $this->repository->findOneBy(['openId' => null]);
        $this->assertNotNull($entityWithNullOpenId);
        $this->assertNull($entityWithNullOpenId->getOpenId());
        $this->assertEquals('pages/findoneby-null1/findoneby-null1', $entityWithNullOpenId->getPath());

        $entityWithNullUnionId = $this->repository->findOneBy(['unionId' => null]);
        $this->assertNotNull($entityWithNullUnionId);
        $this->assertNull($entityWithNullUnionId->getUnionId());

        $entityWithNullOpenType = $this->repository->findOneBy(['openType' => null]);
        $this->assertNotNull($entityWithNullOpenType);
        $this->assertNull($entityWithNullOpenType->getOpenType());

        // 测试findOneBy查找非null值
        $entityWithOpenId = $this->repository->findOneBy(['openId' => 'findoneby-openid']);
        $this->assertNotNull($entityWithOpenId);
        $this->assertEquals('findoneby-openid', $entityWithOpenId->getOpenId());
        $this->assertEquals('pages/findoneby-null2/findoneby-null2', $entityWithOpenId->getPath());
    }

    public function testFindByWithAccountAssociation(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 由于Account实体来自其他bundle，我们测试null关联
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/account-test1/account-test1');
        $entity1->setOpenId('account-test-openid-1');
        $entity1->setAccount(null);

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/account-test2/account-test2');
        $entity2->setOpenId('account-test-openid-2');
        $entity2->setAccount(null);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试查找account为null的实体
        $entitiesWithNullAccount = $this->repository->findBy(['account' => null]);
        $this->assertCount(2, $entitiesWithNullAccount);
        foreach ($entitiesWithNullAccount as $entity) {
            $this->assertNull($entity->getAccount());
        }
    }

    public function testCountWithAccountAssociation(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 由于Account实体来自其他bundle，我们测试null关联
        $entity = new PageNotFoundLog();
        $entity->setPath('pages/count-account/count-account');
        $entity->setOpenId('count-account-openid');
        $entity->setAccount(null);

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试count account为null的记录
        $countNullAccount = $this->repository->count(['account' => null]);
        $this->assertEquals(1, $countNullAccount);
    }

    public function testFindByWithAllQueryableNullFields(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试实体，测试实际可查询的可空字段
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/queryable-null1/queryable-null1');
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);
        $entity1->setOpenType(null);
        $entity1->setRawError(null);
        // query字段通过setQuery(null)会被设为空数组，不是null

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/queryable-null2/queryable-null2');
        $entity2->setOpenId('test-openid');
        $entity2->setUnionId('test-unionid');
        $entity2->setOpenType('navigate');
        $entity2->setRawError('test error');
        $entity2->setQuery(['key' => 'value']);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试查找各种可空字段为null的记录
        $this->assertCount(1, $this->repository->findBy(['openId' => null]));
        $this->assertCount(1, $this->repository->findBy(['unionId' => null]));
        $this->assertCount(1, $this->repository->findBy(['openType' => null]));
        $this->assertCount(1, $this->repository->findBy(['rawError' => null]));
        // JSON字段查询较为复杂，此处跳过query字段的测试
    }

    public function testCountWithAllQueryableNullFields(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建测试实体，测试实际可查询的可空字段
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/count-queryable-null1/count-queryable-null1');
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);
        $entity1->setOpenType(null);
        $entity1->setRawError(null);
        // query字段通过setQuery(null)会被设为空数组，不是null

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/count-queryable-null2/count-queryable-null2');
        $entity2->setOpenId('test-openid');
        $entity2->setUnionId('test-unionid');
        $entity2->setOpenType('navigate');
        $entity2->setRawError('test error');
        $entity2->setQuery(['key' => 'value']);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试count各种可空字段为null的记录
        $this->assertEquals(1, $this->repository->count(['openId' => null]));
        $this->assertEquals(1, $this->repository->count(['unionId' => null]));
        $this->assertEquals(1, $this->repository->count(['openType' => null]));
        $this->assertEquals(1, $this->repository->count(['rawError' => null]));
        // JSON字段查询较为复杂，此处跳过query字段的测试
    }

    public function testFindOneByAccountAssociation(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 由于Account实体来自其他bundle，我们测试null关联
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/findoneby-account-test1/findoneby-account-test1');
        $entity1->setOpenId('findoneby-account-test-openid-1');
        $entity1->setAccount(null);

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/findoneby-account-test2/findoneby-account-test2');
        $entity2->setOpenId('findoneby-account-test-openid-2');
        $entity2->setAccount(null);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试findOneBy查找account为null的实体
        $entityWithNullAccount = $this->repository->findOneBy(['account' => null]);
        $this->assertNotNull($entityWithNullAccount);
        $this->assertNull($entityWithNullAccount->getAccount());
        $this->assertContains($entityWithNullAccount->getPath(), [
            'pages/findoneby-account-test1/findoneby-account-test1',
            'pages/findoneby-account-test2/findoneby-account-test2',
        ]);
    }

    public function testFindByWithAllNullableFieldsCombinations(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 创建带有不同null字段组合的测试实体
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/combination-null1/combination-null1');
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);
        $entity1->setOpenType(null);
        $entity1->setRawError(null);
        $entity1->setAccount(null);

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/combination-null2/combination-null2');
        $entity2->setOpenId('test-openid-2');
        $entity2->setUnionId(null);
        $entity2->setOpenType('navigate');
        $entity2->setRawError(null);
        $entity2->setAccount(null);

        $entity3 = new PageNotFoundLog();
        $entity3->setPath('pages/combination-null3/combination-null3');
        $entity3->setOpenId(null);
        $entity3->setUnionId('test-unionid-3');
        $entity3->setOpenType(null);
        $entity3->setRawError('test error 3');
        $entity3->setAccount(null);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试多字段null组合查询
        $bothOpenIdAndUnionIdNull = $this->repository->findBy([
            'openId' => null,
            'unionId' => null,
        ]);
        $this->assertCount(1, $bothOpenIdAndUnionIdNull);
        $this->assertEquals('pages/combination-null1/combination-null1', $bothOpenIdAndUnionIdNull[0]->getPath());

        // 测试单个字段null查询
        $openIdNullEntities = $this->repository->findBy(['openId' => null]);
        $this->assertCount(2, $openIdNullEntities);

        $unionIdNullEntities = $this->repository->findBy(['unionId' => null]);
        $this->assertCount(2, $unionIdNullEntities);

        $openTypeNullEntities = $this->repository->findBy(['openType' => null]);
        $this->assertCount(2, $openTypeNullEntities);

        $rawErrorNullEntities = $this->repository->findBy(['rawError' => null]);
        $this->assertCount(2, $rawErrorNullEntities);

        $accountNullEntities = $this->repository->findBy(['account' => null]);
        $this->assertCount(3, $accountNullEntities);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 由于Account来自其他bundle，我们测试null关联
        $entity1 = new PageNotFoundLog();
        $entity1->setPath('pages/association-account1/association-account1');
        $entity1->setOpenId('association-openid-1');
        $entity1->setAccount(null);

        $entity2 = new PageNotFoundLog();
        $entity2->setPath('pages/association-account2/association-account2');
        $entity2->setOpenId('association-openid-2');
        $entity2->setAccount(null);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->findOneBy(['account' => null]);

        $this->assertNotNull($foundEntity);
        $this->assertNull($foundEntity->getAccount());
        $this->assertContains($foundEntity->getPath(), [
            'pages/association-account1/association-account1',
            'pages/association-account2/association-account2',
        ]);
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageNotFoundLog::class)->execute();

        // 由于Account来自其他bundle，我们测试null关联
        for ($i = 1; $i <= 6; ++$i) {
            $entity = new PageNotFoundLog();
            $entity->setPath("pages/count-association-account{$i}/count-association-account{$i}");
            $entity->setOpenId("count-association-openid-{$i}");
            $entity->setAccount(null);

            self::getEntityManager()->persist($entity);
        }

        self::getEntityManager()->flush();

        $count = $this->repository->count(['account' => null]);

        $this->assertEquals(6, $count);
    }

    protected function createNewEntity(): object
    {
        $entity = new PageNotFoundLog();

        // 设置必填字段
        $entity->setPath('/pages/notfound/notfound');

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<PageNotFoundLog>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
