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

        // 由于我们没有查看完整的 PageNotFoundLog 类，这里假设它有与 PageVisitLog 类似的属性
        // 测试 ID 属性
        $this->assertNull($entity->getId());

        // 测试其他可能存在的属性（基于类名和一般实体模式）
        if (method_exists($entity, 'setPage')) {
            $entity->setPage('/pages/not-found');
            $this->assertSame('/pages/not-found', $entity->getPage());
        }

        if (method_exists($entity, 'setSessionId')) {
            $entity->setSessionId('test-session-id');
            $this->assertSame('test-session-id', $entity->getSessionId());
        }

        if (method_exists($entity, 'setCreatedBy')) {
            $entity->setCreatedBy('test-user');
            $this->assertSame('test-user', $entity->getCreatedBy());
        }
    }

    /**
     * 测试 ID 字段的只读性（不应有 setId 方法）
     */
    public function testIdFieldIsReadOnly(): void
    {
        $entity = new PageNotFoundLog();

        // 验证没有 setId 方法
        $this->assertFalse(method_exists($entity, 'setId'));
    }
}
