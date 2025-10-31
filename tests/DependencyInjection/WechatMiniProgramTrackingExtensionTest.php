<?php

namespace WechatMiniProgramTrackingBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatMiniProgramTrackingBundle\DependencyInjection\WechatMiniProgramTrackingExtension;

/**
 * @internal
 */
#[CoversClass(WechatMiniProgramTrackingExtension::class)]
final class WechatMiniProgramTrackingExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    /**
     * 测试 services.yaml 是否正确加载
     *
     * 注意：这是一个集成测试，需要访问文件系统
     */
    public function testServicesYamlExists(): void
    {
        $configPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config';
        $this->assertDirectoryExists($configPath);
        $this->assertFileExists($configPath . DIRECTORY_SEPARATOR . 'services.yaml');
    }

    /**
     * 创建 WechatMiniProgramTrackingExtension 实例的工厂方法
     *
     * @phpstan-ignore-next-line 为了测试目的，允许在工厂方法中直接实例化扩展类
     */
    private function createExtension(): WechatMiniProgramTrackingExtension
    {
        /** @phpstan-ignore-next-line */
        return new WechatMiniProgramTrackingExtension();
    }
}
