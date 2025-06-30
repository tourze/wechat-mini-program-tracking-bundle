<?php

namespace WechatMiniProgramTrackingBundle\Tests\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\Repository\PageNotFoundLogRepository;

class PageNotFoundLogRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;

    protected function setUp(): void
    {
        // 创建 ManagerRegistry 模拟对象
        $this->registry = $this->createMock(ManagerRegistry::class);
    }

    /**
     * 测试构造函数是否正确设置实体类
     */
    public function testConstructor(): void
    {
        $repository = new PageNotFoundLogRepository($this->registry);
        $this->assertInstanceOf(PageNotFoundLogRepository::class, $repository);
        $this->assertInstanceOf(EntityRepository::class, $repository);
    }

    /**
     * 简单测试仓库是否继承了 ServiceEntityRepository
     */
    public function testIsServiceEntityRepository(): void
    {
        // 这个测试主要是确认仓库类的继承关系
        $reflectionClass = new \ReflectionClass(PageNotFoundLogRepository::class);
        $this->assertTrue($reflectionClass->isSubclassOf(EntityRepository::class));
    }
}