<?php

namespace WechatMiniProgramTrackingBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;

/**
 * @internal
 */
#[CoversClass(PageNotFoundLog::class)]
final class PageNotFoundLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): PageNotFoundLog
    {
        return new PageNotFoundLog();
    }

    /**
     * 测试 getter 和 setter 方法是否正常工作
     */
    public function testGetterAndSetter(): void
    {
        $entity = $this->createEntity();

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
        $entity = $this->createEntity();

        // 使用反射API检查setId方法存在（从 SnowflakeKeyAware trait）
        $reflection = new \ReflectionClass($entity);
        $this->assertTrue($reflection->hasMethod('setId'));
        $this->assertTrue($reflection->hasMethod('getId'));

        // 测试 setId 和 getId 方法
        $entity->setId('123456789');
        $this->assertSame('123456789', $entity->getId());
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'createdFromUa' => ['createdFromUa', 'Mozilla/5.0 Test User Agent'];
        yield 'updatedFromUa' => ['updatedFromUa', 'Mozilla/5.0 Updated User Agent'];
        yield 'path' => ['path', '/pages/not-found'];
        yield 'openType' => ['openType', 'navigate'];
        yield 'query' => ['query', ['param1' => 'value1', 'param2' => 'value2']];
        yield 'rawError' => ['rawError', 'Page not found error message'];
        yield 'openId' => ['openId', 'test-openid-123'];
        yield 'unionId' => ['unionId', 'test-unionid-456'];
    }
}
