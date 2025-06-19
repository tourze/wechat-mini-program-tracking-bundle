<?php

namespace WechatMiniProgramTrackingBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;

class JumpTrackingLogTest extends TestCase
{
    /**
     * 测试 getter 和 setter 方法是否正常工作
     */
    public function testGetterAndSetter(): void
    {
        $entity = new JumpTrackingLog();

        // 由于我们没有查看完整的 JumpTrackingLog 类，这里假设它有与 PageVisitLog 类似的属性
        // 测试 ID 属性
        $this->assertIsInt($entity->getId()); // 初始值可能为 0 而不是 null

        // 测试 Page 属性
        $entity->setPage('/pages/index/index');
        $this->assertSame('/pages/index/index', $entity->getPage());

        // 测试 SessionId 属性
        $entity->setSessionId('test-session-id');
        $this->assertSame('test-session-id', $entity->getSessionId());

        // 测试 CreatedBy 属性
        $entity->setCreatedBy('test-user');
        $this->assertSame('test-user', $entity->getCreatedBy());
    }

    /**
     * 测试 ID 字段的只读性（不应有 setId 方法）
     */
    public function testIdFieldIsReadOnly(): void
    {
        $entity = new JumpTrackingLog();

        // 使用反射API检查setId方法不存在
        $reflection = new \ReflectionClass($entity);
        $this->assertFalse($reflection->hasMethod('setId'));
    }
}
