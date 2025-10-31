<?php

namespace WechatMiniProgramTrackingBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;

/**
 * @internal
 */
#[CoversClass(PageVisitLog::class)]
final class PageVisitLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): PageVisitLog
    {
        return new PageVisitLog();
    }

    /**
     * 测试 getter 和 setter 方法是否正常工作
     */
    public function testGetterAndSetter(): void
    {
        $entity = $this->createEntity();

        // 测试 page 属性
        $entity->setPage('pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index');
        $this->assertSame('pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index', $entity->getPage());

        // 测试 routeId 属性
        $entity->setRouteId(123);
        $this->assertSame(123, $entity->getRouteId());

        // 测试 sessionId 属性
        $entity->setSessionId('test-session-id');
        $this->assertSame('test-session-id', $entity->getSessionId());

        // 测试 query 属性
        $query = ['param1' => 'value1', 'param2' => 'value2'];
        $entity->setQuery($query);
        $this->assertSame($query, $entity->getQuery());

        // 测试 createdFromUa 属性
        $entity->setCreatedFromUa('test-user-agent');
        $this->assertSame('test-user-agent', $entity->getCreatedFromUa());

        // 测试 createdFromIp 属性
        $entity->setCreatedFromIp('127.0.0.1');
        $this->assertSame('127.0.0.1', $entity->getCreatedFromIp());

        // 测试 createdBy 属性
        $entity->setCreatedBy('test-user');
        $this->assertSame('test-user', $entity->getCreatedBy());
    }

    /**
     * 测试创建时间属性的 getter 和 setter
     */
    public function testCreateTimeGetterAndSetter(): void
    {
        $entity = $this->createEntity();
        $dateTime = new \DateTimeImmutable();

        $entity->setCreateTime($dateTime);
        $this->assertSame($dateTime, $entity->getCreateTime());
    }

    /**
     * 测试setter方法返回void（PHP标准实体模式）
     */
    public function testSetterReturnVoid(): void
    {
        $entity = $this->createEntity();

        // Setter方法返回void，直接调用而不断言返回值
        $entity->setPage('pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index');
        $entity->setRouteId(123);
        $entity->setSessionId('test-session-id');
        $entity->setQuery(['param' => 'value']);
        $entity->setCreatedFromUa('test-user-agent');
        $entity->setCreateTime(new \DateTimeImmutable());

        // 验证setter方法确实设置了值
        self::assertSame('pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index', $entity->getPage());
        self::assertSame(123, $entity->getRouteId());
        self::assertSame('test-session-id', $entity->getSessionId());
        self::assertSame(['param' => 'value'], $entity->getQuery());
        self::assertSame('test-user-agent', $entity->getCreatedFromUa());
        self::assertInstanceOf(\DateTimeImmutable::class, $entity->getCreateTime());
    }

    /**
     * 测试 ID 字段的只读性（不应有 setId 方法）
     */
    public function testIdFieldIsReadOnly(): void
    {
        $entity = $this->createEntity();

        // 确认有 getId 方法
        $this->assertSame(0, $entity->getId()); // 初始值为 0

        // 使用反射API检查setId方法不存在
        $reflection = new \ReflectionClass($entity);
        $this->assertFalse($reflection->hasMethod('setId'));
    }

    /**
     * 测试 query 字段默认为 null
     */
    public function testQueryDefaultValue(): void
    {
        $entity = $this->createEntity();
        $this->assertNull($entity->getQuery());
    }

    /**
     * 测试查询参数为空时的行为
     */
    public function testEmptyQueryBehavior(): void
    {
        $entity = $this->createEntity();

        // 设置空数组作为查询参数
        $entity->setQuery([]);
        $this->assertSame([], $entity->getQuery());

        // 设置 null 作为查询参数
        $entity->setQuery(null);
        $this->assertNull($entity->getQuery());
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'page' => ['page', 'pages/index/index'];
        yield 'routeId' => ['routeId', 123];
        yield 'sessionId' => ['sessionId', 'test-session-123'];
        yield 'query' => ['query', ['param1' => 'value1', 'param2' => 'value2']];
        yield 'createdFromUa' => ['createdFromUa', 'Mozilla/5.0 Test User Agent'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'createdFromIp' => ['createdFromIp', '127.0.0.1'];
    }
}
