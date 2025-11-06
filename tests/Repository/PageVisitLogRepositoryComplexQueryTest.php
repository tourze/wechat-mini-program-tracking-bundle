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
 * PageVisitLog 复杂查询测试
 *
 * @internal
 */
#[CoversClass(PageVisitLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class PageVisitLogRepositoryComplexQueryTest extends AbstractRepositoryTestCase
{
    private PageVisitLogRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(PageVisitLogRepository::class);
        $this->assertInstanceOf(PageVisitLogRepository::class, $repository);
        $this->repository = $repository;
    }

    /**
     * 测试日期范围查询
     */
    public function testQueryBuilderForDateRange(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建日期范围测试数据
        $dateRangeData = PageVisitLogFactory::createDateRangeData();
        foreach ($dateRangeData as $data) {
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $now = new \DateTimeImmutable();

        // 测试查找今天的记录
        /** @var PageVisitLog[] $todayLogs */
        $todayLogs = $this->repository->createQueryBuilder('p')
            ->where('p.createTime BETWEEN :start AND :end')
            ->setParameter('start', $now->setTime(0, 0, 0))
            ->setParameter('end', $now->setTime(23, 59, 59))
            ->getQuery()
            ->getResult();

        $this->assertCount(1, $todayLogs);
        $this->assertEquals('pages/today/test', $todayLogs[0]->getPage());

        // 测试查找有 createdBy 的记录
        /** @var PageVisitLog[] $withCreatedBy */
        $withCreatedBy = $this->repository->createQueryBuilder('p')
            ->where('p.createdBy IS NOT NULL')
            ->getQuery()
            ->getResult();

        $this->assertCount(3, $withCreatedBy);
    }

    /**
     * 测试会话修复场景查询（模拟 RefinePageLogInfoCommand 的使用场景）
     */
    public function testRepositoryFindBySessionIdAndCreatedBy(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建会话修复场景测试数据
        $sessionFixData = PageVisitLogFactory::createSessionFixData();
        $testSessionId = null;
        foreach ($sessionFixData as $data) {
            if ($testSessionId === null) {
                $testSessionId = $data['sessionId']; // 获取实际的sessionId
            }
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 测试查找同一会话中有 createdBy 的记录
        /** @var PageVisitLog|null $withCreatedBy */
        $withCreatedBy = $this->repository->createQueryBuilder('p')
            ->where('p.sessionId = :sessionId AND p.createdBy IS NOT NULL')
            ->setParameter('sessionId', $testSessionId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNotNull($withCreatedBy);
        $this->assertEquals('user123', $withCreatedBy->getCreatedBy());

        // 测试查找需要修复的记录（createdBy 为空的记录）
        /** @var PageVisitLog[] $needsFix */
        $needsFix = $this->repository->createQueryBuilder('p')
            ->where('p.sessionId = :sessionId AND p.createdBy IS NULL')
            ->setParameter('sessionId', $testSessionId)
            ->getQuery()
            ->getResult();

        $this->assertCount(1, $needsFix);
        $this->assertEquals('pages/home/home', $needsFix[0]->getPage());
    }

    /**
     * 测试 null 字段查询
     */
    public function testFindByWithNullableFieldsIsNullQuery(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建带 null 字段的实体
        $nullData = PageVisitLogFactory::createNullableCombinations();
        foreach ($nullData as $data) {
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 测试 createdBy IS NULL
        $nullCreatedByEntities = $this->repository->findBy(['createdBy' => null]);
        $this->assertCount(2, $nullCreatedByEntities);
        foreach ($nullCreatedByEntities as $entity) {
            $this->assertNull($entity->getCreatedBy());
        }

        // 测试 query IS NULL
        $nullQueryEntities = $this->repository->findBy(['query' => null]);
        $this->assertCount(2, $nullQueryEntities);
        foreach ($nullQueryEntities as $entity) {
            $this->assertNull($entity->getQuery());
        }

        // 测试 createTime IS NULL
        $nullTimeEntities = $this->repository->findBy(['createTime' => null]);
        $this->assertCount(2, $nullTimeEntities);
        foreach ($nullTimeEntities as $entity) {
            $this->assertNull($entity->getCreateTime());
        }

        // 测试 createdFromIp IS NULL
        $nullIpEntities = $this->repository->findBy(['createdFromIp' => null]);
        $this->assertCount(2, $nullIpEntities);
        foreach ($nullIpEntities as $entity) {
            $this->assertNull($entity->getCreatedFromIp());
        }
    }

    /**
     * 测试多条件 null 字段查询
     */
    public function testFindByWithAllNullableFieldsCombinations(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建带有不同null字段组合的测试实体
        $combinationsData = PageVisitLogFactory::createNullableCombinations();
        foreach ($combinationsData as $data) {
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 测试多字段null组合查询
        $bothCreatedByAndQueryNull = $this->repository->findBy([
            'createdBy' => null,
            'query' => null,
        ]);
        $this->assertCount(1, $bothCreatedByAndQueryNull);
        $this->assertEquals('pages/all-null/test', $bothCreatedByAndQueryNull[0]->getPage());

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

    /**
     * 测试 findOneBy 的 null 字段查询
     */
    public function testFindOneByWithNullableFieldsIsNullQuery(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建带 null 字段的实体
        $data = PageVisitLogFactory::createWithNullFields();
        $entity = $this->createEntityFromData($data);
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试各种 null 字段查找
        $nullCreatedByEntity = $this->repository->findOneBy(['createdBy' => null]);
        $this->assertNotNull($nullCreatedByEntity);
        $this->assertNull($nullCreatedByEntity->getCreatedBy());
        $this->assertEquals($data['page'], $nullCreatedByEntity->getPage());

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

    /**
     * 测试 findOneBy 带排序
     */
    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建多个匹配的实体
        $pages = ['pages/first/first', 'pages/second/second', 'pages/third/third'];
        foreach ($pages as $index => $page) {
            $data = [
                'page' => $page,
                'routeId' => 8001 + $index,
                'sessionId' => 'multiple-session',
                'createdBy' => 'multiple-user',
            ];
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 使用排序获取第一个
        $foundEntity = $this->repository->findOneBy(
            ['createdBy' => 'multiple-user'],
            ['page' => 'ASC']
        );

        $this->assertNotNull($foundEntity);
        $this->assertEquals('pages/first/first', $foundEntity->getPage());
    }

    /**
     * 测试 findOneBy 多个匹配时返回第一个
     */
    public function testFindOneByWithMultipleMatchesShouldReturnFirstEntity(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建多个匹配的实体
        $pages = ['pages/multi1/multi1', 'pages/multi2/multi2'];
        foreach ($pages as $index => $page) {
            $data = [
                'page' => $page,
                'routeId' => 12001 + $index,
                'sessionId' => 'multi-session',
                'createdBy' => 'multi-user',
            ];
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $foundEntity = $this->repository->findOneBy(['createdBy' => 'multi-user']);

        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(PageVisitLog::class, $foundEntity);
        $this->assertEquals('multi-user', $foundEntity->getCreatedBy());
        // 应该返回其中一个实体（通常是第一个）
        $this->assertContains($foundEntity->getPage(), $pages);
    }

    /**
     * 测试多条件查找
     */
    public function testFindByWithMultipleCriteriaShouldReturnMatchingEntities(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建测试数据
        $dataList = [
            [
                'page' => 'pages/match/match',
                'routeId' => 1001,
                'sessionId' => 'test-session',
                'createdBy' => 'user1',
            ],
            [
                'page' => 'pages/no-match/no-match',
                'routeId' => 1002,
                'sessionId' => 'test-session',
                'createdBy' => 'user2',
            ],
            [
                'page' => 'pages/match/match',
                'routeId' => 1003,
                'sessionId' => 'other-session',
                'createdBy' => 'user1',
            ],
        ];

        foreach ($dataList as $data) {
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
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

    /**
     * 测试特定条件计数
     */
    public function testCountWithSpecificCriteriaShouldReturnFilteredNumber(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建测试数据
        $dataList = [
            [
                'page' => 'pages/home/index',
                'routeId' => 1001,
                'sessionId' => 'session-1',
                'createdBy' => 'user1',
            ],
            [
                'page' => 'pages/about/about',
                'routeId' => 1002,
                'sessionId' => 'session-1',
                'createdBy' => 'user2',
            ],
            [
                'page' => 'pages/contact/contact',
                'routeId' => 1003,
                'sessionId' => 'session-2',
                'createdBy' => 'user3',
            ],
        ];

        foreach ($dataList as $data) {
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // 测试按 sessionId 计数
        $count = $this->repository->count(['sessionId' => 'session-1']);
        $this->assertEquals(2, $count);

        // 测试按 createdBy 计数
        $count = $this->repository->count(['createdBy' => 'user1']);
        $this->assertEquals(1, $count);
    }

    /**
     * 测试按 null 字段计数
     */
    public function testCountByCreatedByNullFieldShouldReturnCorrectNumber(): void
    {
        // 清空数据库
        $this->clearDatabase();

        // 创建3个createdBy为null的实体
        for ($i = 1; $i <= 3; ++$i) {
            $data = [
                'page' => "pages/count-createdby-null{$i}/count-createdby-null{$i}",
                'routeId' => 26000 + $i,
                'sessionId' => "count-createdby-null-session{$i}",
                'createdBy' => null,
            ];
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }

        // 创建2个createdBy不为null的实体
        for ($i = 1; $i <= 2; ++$i) {
            $data = [
                'page' => "pages/count-createdby-not-null{$i}/count-createdby-not-null{$i}",
                'routeId' => 26100 + $i,
                'sessionId' => "count-createdby-not-null-session{$i}",
                'createdBy' => "test-user-{$i}",
            ];
            $entity = $this->createEntityFromData($data);
            self::getEntityManager()->persist($entity);
        }

        self::getEntityManager()->flush();

        $count = $this->repository->count(['createdBy' => null]);
        $this->assertEquals(3, $count);
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