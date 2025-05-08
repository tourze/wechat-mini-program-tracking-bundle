<?php

namespace WechatMiniProgramTrackingBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;

class PageVisitLogTest extends TestCase
{
    /**
     * 测试 getter 和 setter 方法是否正常工作
     */
    public function testGetterAndSetter(): void
    {
        $entity = new PageVisitLog();

        // 测试 page 属性
        $entity->setPage('/pages/index/index');
        $this->assertSame('/pages/index/index', $entity->getPage());

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
        $entity = new PageVisitLog();
        $dateTime = new \DateTime();

        $entity->setCreateTime($dateTime);
        $this->assertSame($dateTime, $entity->getCreateTime());
    }

    /**
     * 测试流畅接口（链式调用）
     */
    public function testFluentInterface(): void
    {
        $entity = new PageVisitLog();

        $this->assertInstanceOf(PageVisitLog::class, $entity->setPage('/pages/index/index'));
        $this->assertInstanceOf(PageVisitLog::class, $entity->setRouteId(123));
        $this->assertInstanceOf(PageVisitLog::class, $entity->setSessionId('test-session-id'));
        $this->assertInstanceOf(PageVisitLog::class, $entity->setQuery(['param' => 'value']));
        $this->assertInstanceOf(PageVisitLog::class, $entity->setCreatedFromUa('test-user-agent'));
        $this->assertInstanceOf(PageVisitLog::class, $entity->setCreateTime(new \DateTime()));
    }

    /**
     * 测试 ID 字段的只读性（不应有 setId 方法）
     */
    public function testIdFieldIsReadOnly(): void
    {
        $entity = new PageVisitLog();

        // 确认有 getId 方法
        $this->assertIsInt($entity->getId()); // 初始值可能为 0 而不是 null

        // 验证没有 setId 方法
        $this->assertFalse(method_exists($entity, 'setId'));
    }

    /**
     * 测试 query 字段默认为 null
     */
    public function testQueryDefaultValue(): void
    {
        $entity = new PageVisitLog();
        $this->assertNull($entity->getQuery());
    }

    /**
     * 测试查询参数为空时的行为
     */
    public function testEmptyQueryBehavior(): void
    {
        $entity = new PageVisitLog();

        // 设置空数组作为查询参数
        $entity->setQuery([]);
        $this->assertSame([], $entity->getQuery());

        // 设置 null 作为查询参数
        $entity->setQuery(null);
        $this->assertNull($entity->getQuery());
    }
}
