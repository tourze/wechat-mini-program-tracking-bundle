<?php

namespace WechatMiniProgramTrackingBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;

class PageNotFoundLogTest extends TestCase
{
    /**
     * 测试 getter 和 setter 方法是否正常工作
     */
    public function testGetterAndSetter(): void
    {
        $entity = new PageNotFoundLog();

        // 测试 ID 属性
        $this->assertNull($entity->getId());

        // 测试 Path 属性
        $entity->setPath('/pages/not-found');
        $this->assertSame('/pages/not-found', $entity->getPath());

        // 测试 OpenType 属性
        $entity->setOpenType('navigate');
        $this->assertSame('navigate', $entity->getOpenType());

        // 测试 Query 属性
        $query = ['param1' => 'value1'];
        $entity->setQuery($query);
        $this->assertSame($query, $entity->getQuery());

        // 测试 RawError 属性
        $entity->setRawError('Page not found error');
        $this->assertSame('Page not found error', $entity->getRawError());

        // 测试 OpenId 属性
        $entity->setOpenId('test-openid');
        $this->assertSame('test-openid', $entity->getOpenId());

        // 测试 UnionId 属性
        $entity->setUnionId('test-unionid');
        $this->assertSame('test-unionid', $entity->getUnionId());
    }

    /**
     * 测试 ID 字段的 getter 和 setter 方法（使用 SnowflakeKeyAware trait）
     */
    public function testIdFieldMethods(): void
    {
        $entity = new PageNotFoundLog();

        // 使用反射API检查setId方法存在（从 SnowflakeKeyAware trait）
        $reflection = new \ReflectionClass($entity);
        $this->assertTrue($reflection->hasMethod('setId'));
        $this->assertTrue($reflection->hasMethod('getId'));
        
        // 测试 setId 和 getId 方法
        $entity->setId('123456789');
        $this->assertSame('123456789', $entity->getId());
    }
}
