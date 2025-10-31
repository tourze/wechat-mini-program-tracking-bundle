<?php

namespace WechatMiniProgramTrackingBundle\Tests\Repository;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;
use WechatMiniProgramTrackingBundle\Repository\PageVisitLogRepository;

/**
 * @internal
 */
#[CoversClass(PageVisitLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class PageVisitLogRepositoryTest extends AbstractRepositoryTestCase
{
    private PageVisitLogRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(PageVisitLogRepository::class);
        $this->assertInstanceOf(PageVisitLogRepository::class, $repository);
        $this->repository = $repository;
    }

    private function setUpTestServices(): void
    {
        $repository = self::getContainer()->get(PageVisitLogRepository::class);
        $this->assertInstanceOf(PageVisitLogRepository::class, $repository);
        $this->repository = $repository;
    }

    public function testRepositoryCanSaveAndFindEntity(): void
    {
        $this->setUpTestServices();

        // 创建实体
        $entity = new PageVisitLog();
        $entity->setPage('pages/home/index');
        $entity->setRouteId(1001);
        $entity->setSessionId('test-session-123');
        $entity->setCreatedBy('test-user');
        $entity->setQuery(['param1' => 'value1', 'param2' => 'value2']);
        $entity->setCreateTime(CarbonImmutable::now());

        // 保存实体
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 验证实体被保存
        $this->assertGreaterThan(0, $entity->getId());

        // 通过仓储查找实体
        $foundEntity = $this->repository->find($entity->getId());

        $this->assertNotNull($foundEntity);
        $this->assertEquals($entity->getId(), $foundEntity->getId());
        $this->assertEquals('pages/home/index', $foundEntity->getPage());
        $this->assertEquals(1001, $foundEntity->getRouteId());
        $this->assertEquals('test-session-123', $foundEntity->getSessionId());
        $this->assertEquals('test-user', $foundEntity->getCreatedBy());
        $this->assertEquals(['param1' => 'value1', 'param2' => 'value2'], $foundEntity->getQuery());
    }

    public function testRepositoryFindAll(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/home/index');
        $entity1->setRouteId(1001);
        $entity1->setSessionId('test-session-1');
        $entity1->setCreatedBy('user1');

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/about/about');
        $entity2->setRouteId(1002);
        $entity2->setSessionId('test-session-2');
        $entity2->setCreatedBy('user2');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试 findAll
        $entities = $this->repository->findAll();

        $this->assertCount(2, $entities);
        $this->assertContainsOnlyInstancesOf(PageVisitLog::class, $entities);
    }

    public function testRepositoryFindBy(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/product/list');
        $entity1->setRouteId(2001);
        $entity1->setSessionId('same-session');
        $entity1->setCreatedBy('user1');

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/product/detail');
        $entity2->setRouteId(2002);
        $entity2->setSessionId('same-session');
        $entity2->setCreatedBy('user2');

        $entity3 = new PageVisitLog();
        $entity3->setPage('pages/cart/cart');
        $entity3->setRouteId(3001);
        $entity3->setSessionId('different-session');
        $entity3->setCreatedBy('user3');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试根据 sessionId 查找
        $entities = $this->repository->findBy(['sessionId' => 'same-session']);

        $this->assertCount(2, $entities);
        foreach ($entities as $entity) {
            $this->assertEquals('same-session', $entity->getSessionId());
        }

        // 测试根据 createdBy 查找
        $entities = $this->repository->findBy(['createdBy' => 'user1']);

        $this->assertCount(1, $entities);
        $this->assertEquals('user1', $entities[0]->getCreatedBy());

        // 测试根据 routeId 查找
        $entities = $this->repository->findBy(['routeId' => 2001]);

        $this->assertCount(1, $entities);
        $this->assertEquals(2001, $entities[0]->getRouteId());
    }

    public function testRepositoryFindOneBy(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建测试数据
        $entity = new PageVisitLog();
        $entity->setPage('pages/unique/unique');
        $entity->setRouteId(9999);
        $entity->setSessionId('unique-session');
        $entity->setCreatedBy('unique-user');

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试根据唯一条件查找
        $foundEntity = $this->repository->findOneBy(['sessionId' => 'unique-session', 'routeId' => 9999]);

        $this->assertNotNull($foundEntity);
        $this->assertEquals('unique-session', $foundEntity->getSessionId());
        $this->assertEquals(9999, $foundEntity->getRouteId());
        $this->assertEquals('unique-user', $foundEntity->getCreatedBy());

        // 测试查找不存在的实体
        $notFoundEntity = $this->repository->findOneBy(['sessionId' => 'non-existent']);

        $this->assertNull($notFoundEntity);
    }

    public function testRepositoryWithOrderByAndLimit(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建多个测试数据
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new PageVisitLog();
            $entity->setPage("pages/test{$i}/test{$i}");
            $entity->setRouteId(1000 + $i);
            $entity->setSessionId("session-{$i}");
            $entity->setCreatedBy("user{$i}");

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

    public function testRepositoryQueryBuilderForDateRange(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        $now = CarbonImmutable::now();
        $yesterday = $now->subDay();
        $tomorrow = $now->addDay();

        // 创建不同时间的测试数据
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/today/today');
        $entity1->setRouteId(1001);
        $entity1->setSessionId('session-today');
        $entity1->setCreatedBy('user-today');
        $entity1->setCreateTime($now);

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/yesterday/yesterday');
        $entity2->setRouteId(1002);
        $entity2->setSessionId('session-yesterday');
        $entity2->setCreatedBy('user-yesterday');
        $entity2->setCreateTime($yesterday);

        $entity3 = new PageVisitLog();
        $entity3->setPage('pages/tomorrow/tomorrow');
        $entity3->setRouteId(1003);
        $entity3->setSessionId('session-tomorrow');
        $entity3->setCreatedBy('user-tomorrow');
        $entity3->setCreateTime($tomorrow);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试查找今天的记录
        /** @var PageVisitLog[] $todayLogs */
        $todayLogs = $this->repository->createQueryBuilder('p')
            ->where('p.createTime BETWEEN :start AND :end')
            ->setParameter('start', $now->startOfDay())
            ->setParameter('end', $now->endOfDay())
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $todayLogs);
        $this->assertEquals('pages/today/today', $todayLogs[0]->getPage());

        // 测试查找有 createdBy 的记录
        /** @var PageVisitLog[] $withCreatedBy */
        $withCreatedBy = $this->repository->createQueryBuilder('p')
            ->where('p.createdBy IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(3, $withCreatedBy);
    }

    public function testRepositoryFindBySessionIdAndCreatedBy(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建测试数据，模拟 RefinePageLogInfoCommand 的使用场景
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/login/login');
        $entity1->setRouteId(1001);
        $entity1->setSessionId('test-session');
        $entity1->setCreatedBy('user123');

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/home/home');
        $entity2->setRouteId(1002);
        $entity2->setSessionId('test-session');
        $entity2->setCreatedBy(null); // 需要被修复的记录

        $entity3 = new PageVisitLog();
        $entity3->setPage('pages/profile/profile');
        $entity3->setRouteId(1003);
        $entity3->setSessionId('other-session');
        $entity3->setCreatedBy('other-user');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试查找同一会话中有 createdBy 的记录
        /** @var PageVisitLog|null $withCreatedBy */
        $withCreatedBy = $this->repository->createQueryBuilder('p')
            ->where('p.sessionId = :sessionId AND p.createdBy IS NOT NULL')
            ->setParameter('sessionId', 'test-session')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertNotNull($withCreatedBy);
        $this->assertEquals('user123', $withCreatedBy->getCreatedBy());

        // 测试查找需要修复的记录（createdBy 为空的记录）
        /** @var PageVisitLog[] $needsFix */
        $needsFix = $this->repository->createQueryBuilder('p')
            ->where('p.sessionId = :sessionId AND p.createdBy IS NULL')
            ->setParameter('sessionId', 'test-session')
            ->getQuery()
            ->getResult()
        ;

        $this->assertCount(1, $needsFix);
        $this->assertEquals('pages/home/home', $needsFix[0]->getPage());
    }

    public function testCountWithSpecificCriteriaShouldReturnFilteredNumber(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/home/index');
        $entity1->setRouteId(1001);
        $entity1->setSessionId('session-1');
        $entity1->setCreatedBy('user1');

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/about/about');
        $entity2->setRouteId(1002);
        $entity2->setSessionId('session-1');
        $entity2->setCreatedBy('user2');

        $entity3 = new PageVisitLog();
        $entity3->setPage('pages/contact/contact');
        $entity3->setRouteId(1003);
        $entity3->setSessionId('session-2');
        $entity3->setCreatedBy('user3');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试按 sessionId 计数
        $count = $this->repository->count(['sessionId' => 'session-1']);
        $this->assertEquals(2, $count);

        // 测试按 createdBy 计数
        $count = $this->repository->count(['createdBy' => 'user1']);
        $this->assertEquals(1, $count);
    }

    public function testFindByWithValidCriteriaShouldReturnMatchingEntities(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/test1/test1');
        $entity1->setRouteId(1001);
        $entity1->setSessionId('test-session');
        $entity1->setCreatedBy('user1');

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/test2/test2');
        $entity2->setRouteId(1002);
        $entity2->setSessionId('test-session');
        $entity2->setCreatedBy('user2');

        $entity3 = new PageVisitLog();
        $entity3->setPage('pages/test3/test3');
        $entity3->setRouteId(1003);
        $entity3->setSessionId('other-session');
        $entity3->setCreatedBy('user3');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试按 sessionId 查找
        $entities = $this->repository->findBy(['sessionId' => 'test-session']);

        $this->assertCount(2, $entities);
        $this->assertContainsOnlyInstancesOf(PageVisitLog::class, $entities);
        foreach ($entities as $entity) {
            $this->assertEquals('test-session', $entity->getSessionId());
        }
    }

    public function testFindByWithNonExistentCriteriaShouldReturnEmptyArray(): void
    {
        $this->setUpTestServices();

        $entities = $this->repository->findBy(['sessionId' => 'non-existent-session']);

        $this->assertIsArray($entities);
        $this->assertEmpty($entities);
    }

    public function testFindByWithNullValuesShouldReturnEntitiesWithNullFields(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建有 null 字段的实体
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/null/null');
        $entity1->setRouteId(1001);
        $entity1->setSessionId('test-session');
        $entity1->setCreatedBy(null); // null 字段
        $entity1->setQuery(null);

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/not-null/not-null');
        $entity2->setRouteId(1002);
        $entity2->setSessionId('test-session');
        $entity2->setCreatedBy('user2');
        $entity2->setQuery(['key' => 'value']);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试查找 createdBy 为 null 的实体
        $entities = $this->repository->findBy(['createdBy' => null]);

        $this->assertCount(1, $entities);
        $this->assertNull($entities[0]->getCreatedBy());
        $this->assertEquals('pages/null/null', $entities[0]->getPage());
    }

    public function testFindByWithMultipleCriteriaShouldReturnMatchingEntities(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建测试数据
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/match/match');
        $entity1->setRouteId(1001);
        $entity1->setSessionId('test-session');
        $entity1->setCreatedBy('user1');

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/no-match/no-match');
        $entity2->setRouteId(1002);
        $entity2->setSessionId('test-session');
        $entity2->setCreatedBy('user2');

        $entity3 = new PageVisitLog();
        $entity3->setPage('pages/match/match');
        $entity3->setRouteId(1003);
        $entity3->setSessionId('other-session');
        $entity3->setCreatedBy('user1');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试多重条件查找
        $entities = $this->repository->findBy([
            'sessionId' => 'test-session',
            'createdBy' => 'user1',
        ]);

        $this->assertCount(1, $entities);
        $this->assertEquals('pages/match/match', $entities[0]->getPage());
        $this->assertEquals('test-session', $entities[0]->getSessionId());
        $this->assertEquals('user1', $entities[0]->getCreatedBy());
    }

    public function testSaveEntityShouldPersistToDatabase(): void
    {
        $this->setUpTestServices();

        // 创建实体
        $entity = new PageVisitLog();
        $entity->setPage('pages/save/save');
        $entity->setRouteId(1001);
        $entity->setSessionId('save-session');
        $entity->setCreatedBy('save-user');

        // 保存并刷新
        $this->repository->save($entity, true);

        // 验证实体已保存
        $this->assertGreaterThan(0, $entity->getId());

        // 重新获取验证
        $foundEntity = $this->repository->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertEquals('pages/save/save', $foundEntity->getPage());
        $this->assertEquals('save-user', $foundEntity->getCreatedBy());
    }

    public function testSaveEntityWithoutFlushShouldNotPersistImmediately(): void
    {
        $this->setUpTestServices();

        // 创建实体
        $entity = new PageVisitLog();
        $entity->setPage('pages/no-flush/no-flush');
        $entity->setRouteId(1001);
        $entity->setSessionId('no-flush-session');
        $entity->setCreatedBy('no-flush-user');

        // 保存但不刷新
        $this->repository->save($entity, false);

        // 实体应该还没有 ID
        $this->assertEquals(0, $entity->getId());

        // 手动刷新
        self::getEntityManager()->flush();

        // 现在应该有 ID 了
        $this->assertGreaterThan(0, $entity->getId());
    }

    public function testRemoveEntityShouldDeleteFromDatabase(): void
    {
        $this->setUpTestServices();

        // 创建并保存实体
        $entity = new PageVisitLog();
        $entity->setPage('pages/remove/remove');
        $entity->setRouteId(1001);
        $entity->setSessionId('remove-session');
        $entity->setCreatedBy('remove-user');

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();
        $entityId = $entity->getId();

        // 删除实体
        $this->repository->remove($entity, true);

        // 验证实体已删除
        $foundEntity = $this->repository->find($entityId);
        $this->assertNull($foundEntity);
    }

    public function testRemoveEntityWithoutFlushShouldNotDeleteImmediately(): void
    {
        $this->setUpTestServices();

        // 创建并保存实体
        $entity = new PageVisitLog();
        $entity->setPage('pages/remove-no-flush/remove-no-flush');
        $entity->setRouteId(1001);
        $entity->setSessionId('remove-no-flush-session');
        $entity->setCreatedBy('remove-no-flush-user');

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

    public function testFindByWithNullableFieldsIsNullQuery(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建带 null 字段的实体
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/null1/null1');
        $entity1->setRouteId(1001);
        $entity1->setSessionId('session-null1');
        $entity1->setCreatedBy(null);
        $entity1->setQuery(null);
        $entity1->setCreateTime(null);
        $entity1->setCreatedFromIp(null);

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/null2/null2');
        $entity2->setRouteId(1002);
        $entity2->setSessionId('session-null2');
        $entity2->setCreatedBy('user2');
        $entity2->setQuery(['key' => 'value']);
        $entity2->setCreateTime(new \DateTimeImmutable());
        $entity2->setCreatedFromIp('192.168.1.1');

        $entity3 = new PageVisitLog();
        $entity3->setPage('pages/null3/null3');
        $entity3->setRouteId(1003);
        $entity3->setSessionId('session-null3');
        $entity3->setCreatedBy(null);
        $entity3->setQuery(['another' => 'value']);
        $entity3->setCreateTime(new \DateTimeImmutable());
        $entity3->setCreatedFromIp(null);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试 createdBy IS NULL
        $nullCreatedByEntities = $this->repository->findBy(['createdBy' => null]);
        $this->assertCount(2, $nullCreatedByEntities);
        foreach ($nullCreatedByEntities as $entity) {
            $this->assertNull($entity->getCreatedBy());
        }

        // 测试 query IS NULL
        $nullQueryEntities = $this->repository->findBy(['query' => null]);
        $this->assertCount(1, $nullQueryEntities);
        $this->assertNull($nullQueryEntities[0]->getQuery());
        $this->assertEquals('pages/null1/null1', $nullQueryEntities[0]->getPage());

        // 测试 createTime IS NULL
        $nullTimeEntities = $this->repository->findBy(['createTime' => null]);
        $this->assertCount(1, $nullTimeEntities);
        $this->assertEquals('pages/null1/null1', $nullTimeEntities[0]->getPage());

        // 测试 createdFromIp IS NULL
        $nullIpEntities = $this->repository->findBy(['createdFromIp' => null]);
        $this->assertCount(2, $nullIpEntities);
        foreach ($nullIpEntities as $entity) {
            $this->assertNull($entity->getCreatedFromIp());
        }
    }

    public function testFindWithStringIdShouldReturnNull(): void
    {
        $this->setUpTestServices();

        $result = $this->repository->find('invalid');

        $this->assertNull($result);
    }

    public function testFindAllWithLargeDatasetShouldReturnAllEntities(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建大量测试数据
        for ($i = 1; $i <= 50; ++$i) {
            $entity = new PageVisitLog();
            $entity->setPage("pages/large{$i}/large{$i}");
            $entity->setRouteId(2000 + $i);
            $entity->setSessionId("large-session-{$i}");
            $entity->setCreatedBy("large-user{$i}");

            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $entities = $this->repository->findAll();

        $this->assertCount(50, $entities);
        $this->assertContainsOnlyInstancesOf(PageVisitLog::class, $entities);
    }

    public function testFindByWithLargeResultSetShouldReturnAllMatches(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建大量匹配的数据
        for ($i = 1; $i <= 100; ++$i) {
            $entity = new PageVisitLog();
            $entity->setPage("pages/large{$i}/large{$i}");
            $entity->setRouteId(6000 + $i);
            $entity->setSessionId('large-result-session');
            $entity->setCreatedBy('large-result-user');

            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $entities = $this->repository->findBy(['createdBy' => 'large-result-user']);

        $this->assertCount(100, $entities);
        $this->assertContainsOnlyInstancesOf(PageVisitLog::class, $entities);
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建多个匹配的实体
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/first/first');
        $entity1->setRouteId(8001);
        $entity1->setSessionId('multiple-session');
        $entity1->setCreatedBy('multiple-user');

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/second/second');
        $entity2->setRouteId(8002);
        $entity2->setSessionId('multiple-session');
        $entity2->setCreatedBy('multiple-user');

        $entity3 = new PageVisitLog();
        $entity3->setPage('pages/third/third');
        $entity3->setRouteId(8003);
        $entity3->setSessionId('multiple-session');
        $entity3->setCreatedBy('multiple-user');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 使用排序获取第一个
        $foundEntity = $this->repository->findOneBy(
            ['createdBy' => 'multiple-user'],
            ['page' => 'ASC']
        );

        $this->assertNotNull($foundEntity);
        $this->assertEquals('pages/first/first', $foundEntity->getPage());
    }

    public function testFindOneByWithMultipleMatchesShouldReturnFirstEntity(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建多个匹配的实体
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/multi1/multi1');
        $entity1->setRouteId(12001);
        $entity1->setSessionId('multi-session');
        $entity1->setCreatedBy('multi-user');

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/multi2/multi2');
        $entity2->setRouteId(12002);
        $entity2->setSessionId('multi-session');
        $entity2->setCreatedBy('multi-user');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->findOneBy(['createdBy' => 'multi-user']);

        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(PageVisitLog::class, $foundEntity);
        $this->assertEquals('multi-user', $foundEntity->getCreatedBy());
        // 应该返回其中一个实体（通常是第一个）
        $this->assertContains($foundEntity->getPage(), ['pages/multi1/multi1', 'pages/multi2/multi2']);
    }

    public function testFindOneByWithNullableFieldsIsNullQuery(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建带 null 字段的实体
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/findone-null1/findone-null1');
        $entity1->setRouteId(13001);
        $entity1->setSessionId('findone-null-session1');
        $entity1->setCreatedBy(null);
        $entity1->setQuery(null);
        $entity1->setCreateTime(null);
        $entity1->setCreatedFromIp(null);

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/findone-null2/findone-null2');
        $entity2->setRouteId(13002);
        $entity2->setSessionId('findone-null-session2');
        $entity2->setCreatedBy('user2');
        $entity2->setQuery(['key' => 'value']);
        $entity2->setCreateTime(new \DateTimeImmutable());
        $entity2->setCreatedFromIp('192.168.1.1');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试各种 null 字段查找
        $nullCreatedByEntity = $this->repository->findOneBy(['createdBy' => null]);
        $this->assertNotNull($nullCreatedByEntity);
        $this->assertNull($nullCreatedByEntity->getCreatedBy());
        $this->assertEquals('pages/findone-null1/findone-null1', $nullCreatedByEntity->getPage());

        $nullQueryEntity = $this->repository->findOneBy(['query' => null]);
        $this->assertNotNull($nullQueryEntity);
        $this->assertNull($nullQueryEntity->getQuery());

        $nullTimeEntity = $this->repository->findOneBy(['createTime' => null]);
        $this->assertNotNull($nullTimeEntity);
        $this->assertNull($nullTimeEntity->getCreateTime());

        $nullIpEntity = $this->repository->findOneBy(['createdFromIp' => null]);
        $this->assertNotNull($nullIpEntity);
        $this->assertNull($nullIpEntity->getCreatedFromIp());
    }

    public function testFindByWithAllNullableFieldsCombinations(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建带有不同null字段组合的测试实体
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/combination-null1/combination-null1');
        $entity1->setRouteId(22001);
        $entity1->setSessionId('combination-null-session1');
        $entity1->setCreatedBy(null);
        $entity1->setQuery(null);
        $entity1->setCreateTime(null);
        $entity1->setCreatedFromIp(null);

        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/combination-null2/combination-null2');
        $entity2->setRouteId(22002);
        $entity2->setSessionId('combination-null-session2');
        $entity2->setCreatedBy('test-user-2');
        $entity2->setQuery(null);
        $entity2->setCreateTime(new \DateTimeImmutable());
        $entity2->setCreatedFromIp(null);

        $entity3 = new PageVisitLog();
        $entity3->setPage('pages/combination-null3/combination-null3');
        $entity3->setRouteId(22003);
        $entity3->setSessionId('combination-null-session3');
        $entity3->setCreatedBy(null);
        $entity3->setQuery(['key' => 'value']);
        $entity3->setCreateTime(null);
        $entity3->setCreatedFromIp('192.168.1.1');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试多字段null组合查询
        $bothCreatedByAndQueryNull = $this->repository->findBy([
            'createdBy' => null,
            'query' => null,
        ]);
        $this->assertCount(1, $bothCreatedByAndQueryNull);
        $this->assertEquals('pages/combination-null1/combination-null1', $bothCreatedByAndQueryNull[0]->getPage());

        // 测试单个字段null查询
        $createdByNullEntities = $this->repository->findBy(['createdBy' => null]);
        $this->assertCount(2, $createdByNullEntities);

        $queryNullEntities = $this->repository->findBy(['query' => null]);
        $this->assertCount(2, $queryNullEntities);

        $createTimeNullEntities = $this->repository->findBy(['createTime' => null]);
        $this->assertCount(2, $createTimeNullEntities);

        $createdFromIpNullEntities = $this->repository->findBy(['createdFromIp' => null]);
        $this->assertCount(2, $createdFromIpNullEntities);
    }

    public function testFindOneByWithAllPossibleNullableFields(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建一个所有可空字段都为null的实体
        $entity1 = new PageVisitLog();
        $entity1->setPage('pages/all-nullable-null/all-nullable-null');
        $entity1->setRouteId(23001);
        $entity1->setSessionId('all-nullable-null-session');
        $entity1->setCreatedBy(null);
        $entity1->setQuery(null);
        $entity1->setCreateTime(null);
        $entity1->setCreatedFromIp(null);

        // 创建一个部分可空字段为null的实体
        $entity2 = new PageVisitLog();
        $entity2->setPage('pages/partial-nullable-null/partial-nullable-null');
        $entity2->setRouteId(23002);
        $entity2->setSessionId('partial-nullable-null-session');
        $entity2->setCreatedBy('test-user');
        $entity2->setQuery(null);
        $entity2->setCreateTime(new \DateTimeImmutable());
        $entity2->setCreatedFromIp(null);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试各种可空字段的findOneBy查询
        $nullCreatedByEntity = $this->repository->findOneBy(['createdBy' => null]);
        $this->assertNotNull($nullCreatedByEntity);
        $this->assertNull($nullCreatedByEntity->getCreatedBy());
        $this->assertEquals('pages/all-nullable-null/all-nullable-null', $nullCreatedByEntity->getPage());

        $nullQueryEntity = $this->repository->findOneBy(['query' => null]);
        $this->assertNotNull($nullQueryEntity);
        $this->assertNull($nullQueryEntity->getQuery());

        $nullCreateTimeEntity = $this->repository->findOneBy(['createTime' => null]);
        $this->assertNotNull($nullCreateTimeEntity);
        $this->assertNull($nullCreateTimeEntity->getCreateTime());

        $nullCreatedFromIpEntity = $this->repository->findOneBy(['createdFromIp' => null]);
        $this->assertNotNull($nullCreatedFromIpEntity);
        $this->assertNull($nullCreatedFromIpEntity->getCreatedFromIp());
    }

    public function testFindByCreatedByNullFieldShouldReturnAllMatchingEntities(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建多个createdBy为null的实体
        for ($i = 1; $i <= 3; ++$i) {
            $entity = new PageVisitLog();
            $entity->setPage("pages/findby-createdby-null{$i}/findby-createdby-null{$i}");
            $entity->setRouteId(25000 + $i);
            $entity->setSessionId("findby-createdby-null-session{$i}");
            $entity->setCreatedBy(null);

            self::getEntityManager()->persist($entity);
        }

        // 创建一个createdBy不为null的实体
        $entity = new PageVisitLog();
        $entity->setPage('pages/findby-createdby-not-null/findby-createdby-not-null');
        $entity->setRouteId(25100);
        $entity->setSessionId('findby-createdby-not-null-session');
        $entity->setCreatedBy('test-user');

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $entities = $this->repository->findBy(['createdBy' => null]);

        $this->assertCount(3, $entities);
        foreach ($entities as $entityItem) {
            $this->assertNull($entityItem->getCreatedBy());
        }
    }

    public function testCountByCreatedByNullFieldShouldReturnCorrectNumber(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . PageVisitLog::class)->execute();

        // 创建3个createdBy为null的实体
        for ($i = 1; $i <= 3; ++$i) {
            $entity = new PageVisitLog();
            $entity->setPage("pages/count-createdby-null{$i}/count-createdby-null{$i}");
            $entity->setRouteId(26000 + $i);
            $entity->setSessionId("count-createdby-null-session{$i}");
            $entity->setCreatedBy(null);

            self::getEntityManager()->persist($entity);
        }

        // 创建2个createdBy不为null的实体
        for ($i = 1; $i <= 2; ++$i) {
            $entity = new PageVisitLog();
            $entity->setPage("pages/count-createdby-not-null{$i}/count-createdby-not-null{$i}");
            $entity->setRouteId(26100 + $i);
            $entity->setSessionId("count-createdby-not-null-session{$i}");
            $entity->setCreatedBy("test-user-{$i}");

            self::getEntityManager()->persist($entity);
        }

        self::getEntityManager()->flush();

        $count = $this->repository->count(['createdBy' => null]);

        $this->assertEquals(3, $count);
    }

    protected function createNewEntity(): object
    {
        $entity = new PageVisitLog();

        // 设置必填字段
        $entity->setPage('/pages/test/test');
        $entity->setRouteId(1);
        $entity->setSessionId('test-session-' . uniqid());

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<PageVisitLog>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
