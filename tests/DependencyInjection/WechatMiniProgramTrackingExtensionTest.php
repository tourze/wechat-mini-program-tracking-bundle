<?php

namespace WechatMiniProgramTrackingBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatMiniProgramTrackingBundle\DependencyInjection\WechatMiniProgramTrackingExtension;

class WechatMiniProgramTrackingExtensionTest extends TestCase
{
    /**
     * 测试加载配置
     */
    public function testLoad(): void
    {
        $container = new ContainerBuilder();

        // 创建扩展实例
        $extension = new WechatMiniProgramTrackingExtension();

        // 调用扩展的load方法
        $extension->load([], $container);

        // 由于我们不能直接修改 load 方法的实现，这里我们主要测试它不会抛出异常
        $this->assertTrue(true);
    }

    /**
     * 测试 services.yaml 是否正确加载
     *
     * 注意：这是一个集成测试，需要访问文件系统
     */
    public function testServicesYamlExists(): void
    {
        $configPath = dirname(__DIR__, 2) . '/src/Resources/config';
        $this->assertDirectoryExists($configPath);
        $this->assertFileExists($configPath . '/services.yaml');
    }
}
