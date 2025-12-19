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
}
