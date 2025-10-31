<?php

namespace WechatMiniProgramTrackingBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;
use WechatMiniProgramTrackingBundle\Repository\JumpTrackingLogRepository;

/**
 * @internal
 */
#[CoversClass(JumpTrackingLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class JumpTrackingLogRepositoryTest extends AbstractRepositoryTestCase
{
    private JumpTrackingLogRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(JumpTrackingLogRepository::class);
        $this->assertInstanceOf(JumpTrackingLogRepository::class, $repository);
        $this->repository = $repository;
    }

    private function setUpTestServices(): void
    {
        $repository = self::getContainer()->get(JumpTrackingLogRepository::class);
        $this->assertInstanceOf(JumpTrackingLogRepository::class, $repository);
        $this->repository = $repository;
    }

    public function testRepositoryCanSaveAndFindEntity(): void
    {
        $this->setUpTestServices();

        // 创建实体
        $entity = new JumpTrackingLog();
        $entity->setPage('pages/test/test');
        $entity->setSessionId('test-session-123');
        $entity->setCreatedBy('test-user');
        $entity->setJumpResult(true);

        // 保存实体
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 验证实体被保存
        $this->assertGreaterThan(0, $entity->getId());

        // 通过仓储查找实体
        $foundEntity = $this->repository->find($entity->getId());

        $this->assertNotNull($foundEntity);
        $this->assertEquals($entity->getId(), $foundEntity->getId());
        $this->assertEquals('pages/test/test', $foundEntity->getPage());
        $this->assertEquals('test-session-123', $foundEntity->getSessionId());
        $this->assertEquals('test-user', $foundEntity->getCreatedBy());
    }

    public function testRepositoryFindAll(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试数据
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/test1/test1');
        $entity1->setSessionId('test-session-1');
        $entity1->setCreatedBy('user1');
        $entity1->setJumpResult(true);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/test2/test2');
        $entity2->setSessionId('test-session-2');
        $entity2->setCreatedBy('user2');
        $entity2->setJumpResult(false);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试 findAll
        $entities = $this->repository->findAll();

        $this->assertCount(2, $entities);
        $this->assertContainsOnlyInstancesOf(JumpTrackingLog::class, $entities);
    }

    public function testRepositoryFindBy(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试数据
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/test/test');
        $entity1->setSessionId('same-session');
        $entity1->setCreatedBy('user1');
        $entity1->setJumpResult(true);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/other/other');
        $entity2->setSessionId('same-session');
        $entity2->setCreatedBy('user2');
        $entity2->setJumpResult(false);

        $entity3 = new JumpTrackingLog();
        $entity3->setPage('pages/different/different');
        $entity3->setSessionId('different-session');
        $entity3->setCreatedBy('user3');
        $entity3->setJumpResult(true);

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
    }

    public function testRepositoryFindOneBy(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试数据
        $entity = new JumpTrackingLog();
        $entity->setPage('pages/unique/unique');
        $entity->setSessionId('unique-session');
        $entity->setCreatedBy('unique-user');
        $entity->setJumpResult(true);

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试根据唯一条件查找
        $foundEntity = $this->repository->findOneBy(['sessionId' => 'unique-session']);

        $this->assertNotNull($foundEntity);
        $this->assertEquals('unique-session', $foundEntity->getSessionId());
        $this->assertEquals('unique-user', $foundEntity->getCreatedBy());

        // 测试查找不存在的实体
        $notFoundEntity = $this->repository->findOneBy(['sessionId' => 'non-existent']);

        $this->assertNull($notFoundEntity);
    }

    public function testRepositoryWithOrderByAndLimit(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建多个测试数据
        for ($i = 1; $i <= 5; ++$i) {
            $entity = new JumpTrackingLog();
            $entity->setPage("pages/test{$i}/test{$i}");
            $entity->setSessionId("session-{$i}");
            $entity->setCreatedBy("user{$i}");
            $entity->setJumpResult(1 === $i % 2); // 奇数为 true，偶数为 false

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

    public function testCountWithSpecificCriteriaShouldReturnFilteredNumber(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试数据
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/count-filter/count-filter');
        $entity1->setSessionId('filter-session-1');
        $entity1->setCreatedBy('filter-user');
        $entity1->setJumpResult(true);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/count-other/count-other');
        $entity2->setSessionId('filter-session-2');
        $entity2->setCreatedBy('filter-user');
        $entity2->setJumpResult(false);

        $entity3 = new JumpTrackingLog();
        $entity3->setPage('pages/count-different/count-different');
        $entity3->setSessionId('filter-session-3');
        $entity3->setCreatedBy('different-user');
        $entity3->setJumpResult(true);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试按特定条件计数
        $count = $this->repository->count(['createdBy' => 'filter-user']);
        $this->assertEquals(2, $count);

        $countByResult = $this->repository->count(['jumpResult' => true]);
        $this->assertEquals(2, $countByResult);
    }

    public function testFindByWithValidCriteriaShouldReturnMatchingEntities(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试数据
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/findby-test/findby-test');
        $entity1->setSessionId('findby-session');
        $entity1->setCreatedBy('findby-user');
        $entity1->setJumpResult(true);
        $entity1->setOpenId('test-openid-1');

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/findby-other/findby-other');
        $entity2->setSessionId('findby-session');
        $entity2->setCreatedBy('other-user');
        $entity2->setJumpResult(false);
        $entity2->setOpenId('test-openid-2');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试按会话ID查找
        $entitiesBySession = $this->repository->findBy(['sessionId' => 'findby-session']);
        $this->assertCount(2, $entitiesBySession);

        // 测试按创建者查找
        $entitiesByCreator = $this->repository->findBy(['createdBy' => 'findby-user']);
        $this->assertCount(1, $entitiesByCreator);
        $this->assertEquals('findby-user', $entitiesByCreator[0]->getCreatedBy());

        // 测试按跳转结果查找
        $entitiesByResult = $this->repository->findBy(['jumpResult' => true]);
        $this->assertCount(1, $entitiesByResult);
        $this->assertTrue($entitiesByResult[0]->isJumpResult());
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
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建带null字段的测试数据
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/null-test/null-test');
        $entity1->setSessionId('null-test-session');
        $entity1->setCreatedBy('null-test-user');
        $entity1->setJumpResult(true);
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/not-null-test/not-null-test');
        $entity2->setSessionId('not-null-test-session');
        $entity2->setCreatedBy('not-null-test-user');
        $entity2->setJumpResult(false);
        $entity2->setOpenId('test-openid');
        $entity2->setUnionId('test-unionid');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试查找openId为null的实体
        $entitiesWithNullOpenId = $this->repository->findBy(['openId' => null]);
        $this->assertCount(1, $entitiesWithNullOpenId);
        $this->assertNull($entitiesWithNullOpenId[0]->getOpenId());
        $this->assertEquals('null-test-session', $entitiesWithNullOpenId[0]->getSessionId());

        // 测试查找unionId为null的实体
        $entitiesWithNullUnionId = $this->repository->findBy(['unionId' => null]);
        $this->assertCount(1, $entitiesWithNullUnionId);
        $this->assertNull($entitiesWithNullUnionId[0]->getUnionId());
    }

    public function testFindByWithMultipleCriteriaShouldReturnMatchingEntities(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试数据
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/multi-criteria/multi-criteria');
        $entity1->setSessionId('multi-session');
        $entity1->setCreatedBy('multi-user');
        $entity1->setJumpResult(true);
        $entity1->setOpenId('multi-openid');

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/multi-other/multi-other');
        $entity2->setSessionId('multi-session');
        $entity2->setCreatedBy('other-user');
        $entity2->setJumpResult(true);
        $entity2->setOpenId('other-openid');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试多条件查询
        $entities = $this->repository->findBy([
            'sessionId' => 'multi-session',
            'jumpResult' => true,
            'createdBy' => 'multi-user',
        ]);

        $this->assertCount(1, $entities);
        $this->assertEquals('multi-user', $entities[0]->getCreatedBy());
        $this->assertEquals('multi-session', $entities[0]->getSessionId());
        $this->assertTrue($entities[0]->isJumpResult());
    }

    public function testSaveEntityShouldPersistToDatabase(): void
    {
        $this->setUpTestServices();

        // 创建新实体
        $entity = new JumpTrackingLog();
        $entity->setPage('pages/save-test/save-test');
        $entity->setSessionId('save-test-session');
        $entity->setCreatedBy('save-test-user');
        $entity->setJumpResult(true);
        $entity->setOpenId('save-test-openid');

        // 保存实体
        $this->repository->save($entity);

        // 验证实体被保存
        $this->assertGreaterThan(0, $entity->getId());

        // 从数据库重新查找验证
        $foundEntity = $this->repository->find($entity->getId());
        $this->assertNotNull($foundEntity);
        $this->assertEquals('pages/save-test/save-test', $foundEntity->getPage());
        $this->assertEquals('save-test-session', $foundEntity->getSessionId());
        $this->assertEquals('save-test-user', $foundEntity->getCreatedBy());
        $this->assertTrue($foundEntity->isJumpResult());
        $this->assertEquals('save-test-openid', $foundEntity->getOpenId());
    }

    public function testSaveEntityWithoutFlushShouldNotPersistImmediately(): void
    {
        $this->setUpTestServices();

        // 创建新实体
        $entity = new JumpTrackingLog();
        $entity->setPage('pages/save-no-flush/save-no-flush');
        $entity->setSessionId('save-no-flush-session');
        $entity->setCreatedBy('save-no-flush-user');
        $entity->setJumpResult(false);

        // 保存但不刷新
        $this->repository->save($entity, false);

        // 获取实体ID (应该仍然为0，因为还没有flush)
        $entityId = $entity->getId();

        // 手动刷新
        self::getEntityManager()->flush();

        // 验证现在有了ID
        $this->assertGreaterThan(0, $entity->getId());
        $this->assertNotEquals($entityId, $entity->getId());
    }

    public function testRemoveEntityShouldDeleteFromDatabase(): void
    {
        $this->setUpTestServices();

        // 创建并保存实体
        $entity = new JumpTrackingLog();
        $entity->setPage('pages/remove-test/remove-test');
        $entity->setSessionId('remove-test-session');
        $entity->setCreatedBy('remove-test-user');
        $entity->setJumpResult(true);

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
        $entity = new JumpTrackingLog();
        $entity->setPage('pages/remove-no-flush/remove-no-flush');
        $entity->setSessionId('remove-no-flush-session');
        $entity->setCreatedBy('remove-no-flush-user');
        $entity->setJumpResult(false);

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
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建多个测试实体，有些字段为null，有些不为null
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/nullable1/nullable1');
        $entity1->setSessionId('nullable-session-1');
        $entity1->setCreatedBy('nullable-user-1');
        $entity1->setJumpResult(true);
        // 以下字段设为null
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);
        $entity1->setAppKey(null);
        $entity1->setBusinessChannel(null);
        $entity1->setDeviceBrand(null);
        $entity1->setEventName(null);
        $entity1->setNetworkType(null);
        $entity1->setPlatform(null);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/nullable2/nullable2');
        $entity2->setSessionId('nullable-session-2');
        $entity2->setCreatedBy('nullable-user-2');
        $entity2->setJumpResult(false);
        // 以下字段设为非null值
        $entity2->setOpenId('test-openid-2');
        $entity2->setUnionId('test-unionid-2');
        $entity2->setAppKey('test-appkey-2');
        $entity2->setBusinessChannel('test-channel-2');
        $entity2->setDeviceBrand('test-brand-2');
        $entity2->setEventName('test-event-2');
        $entity2->setNetworkType('test-network-2');
        $entity2->setPlatform('test-platform-2');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试查找openId为null的记录
        $entitiesWithNullOpenId = $this->repository->findBy(['openId' => null]);
        $this->assertCount(1, $entitiesWithNullOpenId);
        $this->assertNull($entitiesWithNullOpenId[0]->getOpenId());
        $this->assertEquals('nullable-session-1', $entitiesWithNullOpenId[0]->getSessionId());

        // 测试查找unionId为null的记录
        $entitiesWithNullUnionId = $this->repository->findBy(['unionId' => null]);
        $this->assertCount(1, $entitiesWithNullUnionId);
        $this->assertNull($entitiesWithNullUnionId[0]->getUnionId());

        // 测试查找appKey为null的记录
        $entitiesWithNullAppKey = $this->repository->findBy(['appKey' => null]);
        $this->assertCount(1, $entitiesWithNullAppKey);
        $this->assertNull($entitiesWithNullAppKey[0]->getAppKey());

        // 测试查找businessChannel为null的记录
        $entitiesWithNullBusinessChannel = $this->repository->findBy(['businessChannel' => null]);
        $this->assertCount(1, $entitiesWithNullBusinessChannel);
        $this->assertNull($entitiesWithNullBusinessChannel[0]->getBusinessChannel());

        // 测试查找deviceBrand为null的记录
        $entitiesWithNullDeviceBrand = $this->repository->findBy(['deviceBrand' => null]);
        $this->assertCount(1, $entitiesWithNullDeviceBrand);
        $this->assertNull($entitiesWithNullDeviceBrand[0]->getDeviceBrand());

        // 测试查找eventName为null的记录
        $entitiesWithNullEventName = $this->repository->findBy(['eventName' => null]);
        $this->assertCount(1, $entitiesWithNullEventName);
        $this->assertNull($entitiesWithNullEventName[0]->getEventName());

        // 测试查找networkType为null的记录
        $entitiesWithNullNetworkType = $this->repository->findBy(['networkType' => null]);
        $this->assertCount(1, $entitiesWithNullNetworkType);
        $this->assertNull($entitiesWithNullNetworkType[0]->getNetworkType());

        // 测试查找platform为null的记录
        $entitiesWithNullPlatform = $this->repository->findBy(['platform' => null]);
        $this->assertCount(1, $entitiesWithNullPlatform);
        $this->assertNull($entitiesWithNullPlatform[0]->getPlatform());
    }

    public function testFindByWithAdditionalNullableFields(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试实体，测试更多可空字段
        $entity = new JumpTrackingLog();
        $entity->setPage('pages/more-nullable/more-nullable');
        $entity->setSessionId('more-nullable-session');
        $entity->setCreatedBy('more-nullable-user');
        $entity->setJumpResult(true);
        // 设置更多可空字段为null
        $entity->setDeviceId(null);
        $entity->setDeviceModel(null);
        $entity->setDeviceSystem(null);
        $entity->setDeviceSystemVersion(null);
        $entity->setPageName(null);
        $entity->setPageQuery(null);
        $entity->setPageTitle(null);
        $entity->setPageUrl(null);
        $entity->setPrevPath(null);
        $entity->setPrevSessionId(null);
        $entity->setScene(null);
        $entity->setSdkName(null);
        $entity->setSdkType(null);
        $entity->setSdkVersion(null);
        $entity->setQuery(null);
        $entity->setEventParam(null);

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试各种可空字段的IS NULL查询
        $this->assertCount(1, $this->repository->findBy(['deviceId' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceModel' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceSystem' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceSystemVersion' => null]));
        $this->assertCount(1, $this->repository->findBy(['pageName' => null]));
        $this->assertCount(1, $this->repository->findBy(['pageQuery' => null]));
        $this->assertCount(1, $this->repository->findBy(['pageTitle' => null]));
        $this->assertCount(1, $this->repository->findBy(['pageUrl' => null]));
        $this->assertCount(1, $this->repository->findBy(['prevPath' => null]));
        $this->assertCount(1, $this->repository->findBy(['prevSessionId' => null]));
        $this->assertCount(1, $this->repository->findBy(['scene' => null]));
        $this->assertCount(1, $this->repository->findBy(['sdkName' => null]));
        $this->assertCount(1, $this->repository->findBy(['sdkType' => null]));
        $this->assertCount(1, $this->repository->findBy(['sdkVersion' => null]));
        $this->assertCount(1, $this->repository->findBy(['query' => null]));
        $this->assertCount(1, $this->repository->findBy(['eventParam' => null]));
    }

    public function testFindByWithIntegerNullableFields(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试实体，测试整数类型可空字段
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/int-nullable1/int-nullable1');
        $entity1->setSessionId('int-nullable-session-1');
        $entity1->setCreatedBy('int-nullable-user-1');
        $entity1->setJumpResult(true);
        $entity1->setDeviceScreenHeight(null);
        $entity1->setDeviceScreenWidth(null);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/int-nullable2/int-nullable2');
        $entity2->setSessionId('int-nullable-session-2');
        $entity2->setCreatedBy('int-nullable-user-2');
        $entity2->setJumpResult(false);
        $entity2->setDeviceScreenHeight(1920);
        $entity2->setDeviceScreenWidth(1080);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试整数类型可空字段的IS NULL查询
        $entitiesWithNullHeight = $this->repository->findBy(['deviceScreenHeight' => null]);
        $this->assertCount(1, $entitiesWithNullHeight);
        $this->assertNull($entitiesWithNullHeight[0]->getDeviceScreenHeight());

        $entitiesWithNullWidth = $this->repository->findBy(['deviceScreenWidth' => null]);
        $this->assertCount(1, $entitiesWithNullWidth);
        $this->assertNull($entitiesWithNullWidth[0]->getDeviceScreenWidth());

        // 测试非null值查询
        $entitiesWithHeight = $this->repository->findBy(['deviceScreenHeight' => 1920]);
        $this->assertCount(1, $entitiesWithHeight);
        $this->assertEquals(1920, $entitiesWithHeight[0]->getDeviceScreenHeight());

        $entitiesWithWidth = $this->repository->findBy(['deviceScreenWidth' => 1080]);
        $this->assertCount(1, $entitiesWithWidth);
        $this->assertEquals(1080, $entitiesWithWidth[0]->getDeviceScreenWidth());
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
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建大量测试数据
        for ($i = 1; $i <= 10; ++$i) {
            $entity = new JumpTrackingLog();
            $entity->setPage("pages/large{$i}/large{$i}");
            $entity->setSessionId("large-session-{$i}");
            $entity->setCreatedBy("large-user-{$i}");
            $entity->setJumpResult(0 === $i % 2);

            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $entities = $this->repository->findAll();

        $this->assertCount(10, $entities);
        $this->assertContainsOnlyInstancesOf(JumpTrackingLog::class, $entities);
    }

    public function testFindByWithLargeResultSetShouldReturnAllMatches(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建大量相同条件的测试数据
        for ($i = 1; $i <= 15; ++$i) {
            $entity = new JumpTrackingLog();
            $entity->setPage("pages/large-result{$i}/large-result{$i}");
            $entity->setSessionId('large-result-session');
            $entity->setCreatedBy("large-result-user-{$i}");
            $entity->setJumpResult(true);

            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        $entities = $this->repository->findBy(['sessionId' => 'large-result-session']);

        $this->assertCount(15, $entities);
        $this->assertContainsOnlyInstancesOf(JumpTrackingLog::class, $entities);
        foreach ($entities as $entity) {
            $this->assertEquals('large-result-session', $entity->getSessionId());
        }
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建多个相同条件的测试数据
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/order-findoneby1/order-findoneby1');
        $entity1->setSessionId('same-session');
        $entity1->setCreatedBy('user-b');
        $entity1->setJumpResult(true);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/order-findoneby2/order-findoneby2');
        $entity2->setSessionId('same-session');
        $entity2->setCreatedBy('user-a');
        $entity2->setJumpResult(true);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试按createdBy升序排序，应该返回第一个匹配的实体
        $foundEntity = $this->repository->findOneBy(['sessionId' => 'same-session'], ['createdBy' => 'ASC']);

        $this->assertNotNull($foundEntity);
        $this->assertEquals('user-a', $foundEntity->getCreatedBy());

        // 测试按createdBy降序排序
        $foundEntityDesc = $this->repository->findOneBy(['sessionId' => 'same-session'], ['createdBy' => 'DESC']);

        $this->assertNotNull($foundEntityDesc);
        $this->assertEquals('user-b', $foundEntityDesc->getCreatedBy());
    }

    public function testFindOneByWithMultipleMatchesShouldReturnFirstEntity(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建多个匹配的实体
        for ($i = 1; $i <= 3; ++$i) {
            $entity = new JumpTrackingLog();
            $entity->setPage("pages/multiple{$i}/multiple{$i}");
            $entity->setSessionId('multiple-match-session');
            $entity->setCreatedBy("multiple-user-{$i}");
            $entity->setJumpResult(true);

            self::getEntityManager()->persist($entity);
        }
        self::getEntityManager()->flush();

        // findOneBy应该返回第一个匹配的实体
        $foundEntity = $this->repository->findOneBy(['sessionId' => 'multiple-match-session']);

        $this->assertNotNull($foundEntity);
        $this->assertInstanceOf(JumpTrackingLog::class, $foundEntity);
        $this->assertEquals('multiple-match-session', $foundEntity->getSessionId());
        // 应该返回第一个创建的实体
        $this->assertEquals('multiple-user-1', $foundEntity->getCreatedBy());
    }

    public function testFindByWithAllNullableFields(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建一个实体，所有可空字段都设为null
        $entity = new JumpTrackingLog();
        $entity->setPage('pages/all-null/all-null');
        $entity->setSessionId(null);
        $entity->setCreatedBy('all-null-user');
        $entity->setJumpResult(true);

        // 设置所有其他可空字段为null
        $entity->setOpenId(null);
        $entity->setUnionId(null);
        $entity->setQuery(null);
        $entity->setAppKey(null);
        $entity->setBusinessChannel(null);
        $entity->setDeviceBrand(null);
        $entity->setDeviceId(null);
        $entity->setDeviceModel(null);
        $entity->setDeviceScreenHeight(null);
        $entity->setDeviceScreenWidth(null);
        $entity->setDeviceSystem(null);
        $entity->setDeviceSystemVersion(null);
        $entity->setEventName(null);
        $entity->setEventParam(null);
        $entity->setNetworkType(null);
        $entity->setPageName(null);
        $entity->setPageQuery(null);
        $entity->setPageTitle(null);
        $entity->setPageUrl(null);
        $entity->setPlatform(null);
        $entity->setPrevPath(null);
        $entity->setPrevSessionId(null);
        $entity->setScene(null);
        $entity->setSdkName(null);
        $entity->setSdkType(null);
        $entity->setSdkVersion(null);
        $entity->setCreateTime(null);

        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        // 测试所有可空字段的 IS NULL 查询
        $this->assertCount(1, $this->repository->findBy(['sessionId' => null]));
        $this->assertCount(1, $this->repository->findBy(['openId' => null]));
        $this->assertCount(1, $this->repository->findBy(['unionId' => null]));
        $this->assertCount(1, $this->repository->findBy(['query' => null]));
        $this->assertCount(1, $this->repository->findBy(['appKey' => null]));
        $this->assertCount(1, $this->repository->findBy(['businessChannel' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceBrand' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceId' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceModel' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceScreenHeight' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceScreenWidth' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceSystem' => null]));
        $this->assertCount(1, $this->repository->findBy(['deviceSystemVersion' => null]));
        $this->assertCount(1, $this->repository->findBy(['eventName' => null]));
        $this->assertCount(1, $this->repository->findBy(['eventParam' => null]));
        $this->assertCount(1, $this->repository->findBy(['networkType' => null]));
        $this->assertCount(1, $this->repository->findBy(['pageName' => null]));
        $this->assertCount(1, $this->repository->findBy(['pageQuery' => null]));
        $this->assertCount(1, $this->repository->findBy(['pageTitle' => null]));
        $this->assertCount(1, $this->repository->findBy(['pageUrl' => null]));
        $this->assertCount(1, $this->repository->findBy(['platform' => null]));
        $this->assertCount(1, $this->repository->findBy(['prevPath' => null]));
        $this->assertCount(1, $this->repository->findBy(['prevSessionId' => null]));
        $this->assertCount(1, $this->repository->findBy(['scene' => null]));
        $this->assertCount(1, $this->repository->findBy(['sdkName' => null]));
        $this->assertCount(1, $this->repository->findBy(['sdkType' => null]));
        $this->assertCount(1, $this->repository->findBy(['sdkVersion' => null]));
        $this->assertCount(1, $this->repository->findBy(['createTime' => null]));
    }

    public function testCountWithAllNullableFieldsIsNullQueries(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试数据，包含多个null和非null的组合
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/count-all-null1/count-all-null1');
        $entity1->setSessionId(null);
        $entity1->setCreatedBy('count-all-null-user-1');
        $entity1->setJumpResult(true);
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);
        $entity1->setQuery(null);
        $entity1->setAppKey(null);
        $entity1->setBusinessChannel(null);
        $entity1->setDeviceBrand(null);
        $entity1->setEventName(null);
        $entity1->setNetworkType(null);
        $entity1->setPlatform(null);
        $entity1->setSdkName(null);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/count-all-null2/count-all-null2');
        $entity2->setSessionId('some-session');
        $entity2->setCreatedBy('count-all-null-user-2');
        $entity2->setJumpResult(false);
        $entity2->setOpenId('some-openid');
        $entity2->setUnionId('some-unionid');
        $entity2->setQuery(['key' => 'value']);
        $entity2->setAppKey('some-appkey');
        $entity2->setBusinessChannel('some-channel');
        $entity2->setDeviceBrand('some-brand');
        $entity2->setEventName('some-event');
        $entity2->setNetworkType('some-network');
        $entity2->setPlatform('some-platform');
        $entity2->setSdkName('some-sdk');

        $entity3 = new JumpTrackingLog();
        $entity3->setPage('pages/count-all-null3/count-all-null3');
        $entity3->setSessionId(null);
        $entity3->setCreatedBy('count-all-null-user-3');
        $entity3->setJumpResult(true);
        $entity3->setOpenId(null);
        $entity3->setUnionId('different-unionid');
        $entity3->setQuery(null);
        $entity3->setAppKey(null);
        $entity3->setBusinessChannel(null);
        $entity3->setDeviceBrand('different-brand');
        $entity3->setEventName(null);
        $entity3->setNetworkType(null);
        $entity3->setPlatform(null);
        $entity3->setSdkName(null);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试各种可空字段的count IS NULL查询
        $this->assertEquals(2, $this->repository->count(['sessionId' => null]));
        $this->assertEquals(2, $this->repository->count(['openId' => null]));
        $this->assertEquals(1, $this->repository->count(['unionId' => null]));
        $this->assertEquals(2, $this->repository->count(['query' => null]));
        $this->assertEquals(2, $this->repository->count(['appKey' => null]));
        $this->assertEquals(2, $this->repository->count(['businessChannel' => null]));
        $this->assertEquals(1, $this->repository->count(['deviceBrand' => null]));
        $this->assertEquals(2, $this->repository->count(['eventName' => null]));
        $this->assertEquals(2, $this->repository->count(['networkType' => null]));
        $this->assertEquals(2, $this->repository->count(['platform' => null]));
        $this->assertEquals(2, $this->repository->count(['sdkName' => null]));

        // 测试非null值的count查询
        $this->assertEquals(1, $this->repository->count(['sessionId' => 'some-session']));
        $this->assertEquals(1, $this->repository->count(['openId' => 'some-openid']));
        $this->assertEquals(1, $this->repository->count(['unionId' => 'some-unionid']));
        $this->assertEquals(1, $this->repository->count(['deviceBrand' => 'different-brand']));
    }

    public function testFindOneByWithNullableFieldsIsNullQuery(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建测试数据
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/findoneby-null1/findoneby-null1');
        $entity1->setSessionId('findoneby-null-session-1');
        $entity1->setCreatedBy('findoneby-null-user-1');
        $entity1->setJumpResult(true);
        $entity1->setOpenId(null);
        $entity1->setUnionId(null);
        $entity1->setAppKey(null);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/findoneby-null2/findoneby-null2');
        $entity2->setSessionId('findoneby-null-session-2');
        $entity2->setCreatedBy('findoneby-null-user-2');
        $entity2->setJumpResult(false);
        $entity2->setOpenId('findoneby-openid');
        $entity2->setUnionId('findoneby-unionid');
        $entity2->setAppKey('findoneby-appkey');

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->flush();

        // 测试findOneBy查找null值
        $entityWithNullOpenId = $this->repository->findOneBy(['openId' => null]);
        $this->assertNotNull($entityWithNullOpenId);
        $this->assertNull($entityWithNullOpenId->getOpenId());
        $this->assertEquals('findoneby-null-session-1', $entityWithNullOpenId->getSessionId());

        $entityWithNullUnionId = $this->repository->findOneBy(['unionId' => null]);
        $this->assertNotNull($entityWithNullUnionId);
        $this->assertNull($entityWithNullUnionId->getUnionId());

        $entityWithNullAppKey = $this->repository->findOneBy(['appKey' => null]);
        $this->assertNotNull($entityWithNullAppKey);
        $this->assertNull($entityWithNullAppKey->getAppKey());

        // 测试findOneBy查找非null值
        $entityWithOpenId = $this->repository->findOneBy(['openId' => 'findoneby-openid']);
        $this->assertNotNull($entityWithOpenId);
        $this->assertEquals('findoneby-openid', $entityWithOpenId->getOpenId());
        $this->assertEquals('findoneby-null-session-2', $entityWithOpenId->getSessionId());
    }

    public function testFindOneByWithOrderByWhenEntityMatchesMultipleCriteriaShouldReturnFirstByOrder(): void
    {
        $this->setUpTestServices();

        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . JumpTrackingLog::class)->execute();

        // 创建多个相同条件的测试数据
        $entity1 = new JumpTrackingLog();
        $entity1->setPage('pages/order-test1/order-test1');
        $entity1->setSessionId('same-session-order');
        $entity1->setCreatedBy('user-c');
        $entity1->setJumpResult(true);

        $entity2 = new JumpTrackingLog();
        $entity2->setPage('pages/order-test2/order-test2');
        $entity2->setSessionId('same-session-order');
        $entity2->setCreatedBy('user-a');
        $entity2->setJumpResult(true);

        $entity3 = new JumpTrackingLog();
        $entity3->setPage('pages/order-test3/order-test3');
        $entity3->setSessionId('same-session-order');
        $entity3->setCreatedBy('user-b');
        $entity3->setJumpResult(true);

        self::getEntityManager()->persist($entity1);
        self::getEntityManager()->persist($entity2);
        self::getEntityManager()->persist($entity3);
        self::getEntityManager()->flush();

        // 测试按createdBy升序排序，应该返回第一个匹配的实体
        $foundEntity = $this->repository->findOneBy(['sessionId' => 'same-session-order'], ['createdBy' => 'ASC']);

        $this->assertNotNull($foundEntity);
        $this->assertEquals('user-a', $foundEntity->getCreatedBy());

        // 测试按createdBy降序排序
        $foundEntityDesc = $this->repository->findOneBy(['sessionId' => 'same-session-order'], ['createdBy' => 'DESC']);

        $this->assertNotNull($foundEntityDesc);
        $this->assertEquals('user-c', $foundEntityDesc->getCreatedBy());
    }

    protected function createNewEntity(): object
    {
        $entity = new JumpTrackingLog();

        // 设置必填字段
        $entity->setPage('/pages/test/test');
        $entity->setJumpResult(true);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<JumpTrackingLog>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
