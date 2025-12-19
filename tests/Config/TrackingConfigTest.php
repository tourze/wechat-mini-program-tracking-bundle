<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Config;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramTrackingBundle\Config\TrackingConfig;

/**
 * @internal
 */
#[CoversClass(TrackingConfig::class)]
#[RunTestsInSeparateProcesses]
final class TrackingConfigTest extends AbstractIntegrationTestCase
{
    private TrackingConfig $config;

    protected function onSetUp(): void
    {
        $this->config = self::getService(TrackingConfig::class);
    }

    /**
     * 测试服务可以从容器获取
     */
    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(TrackingConfig::class, $this->config);
    }

    /**
     * 测试获取默认的 notFoundFallbackPage
     */
    public function testGetNotFoundFallbackPage(): void
    {
        $fallbackPage = $this->config->getNotFoundFallbackPage();

        $this->assertNotEmpty($fallbackPage);
        $this->assertStringContainsString('_from=page_not_found', $fallbackPage);
    }

    /**
     * 测试 supportsUserIdentity 默认返回 true
     */
    public function testSupportsUserIdentity(): void
    {
        $this->assertTrue($this->config->supportsUserIdentity());
    }

    /**
     * 测试回退页面包含必要部分
     */
    public function testFallbackPageContainsRequiredParts(): void
    {
        $fallbackPage = $this->config->getNotFoundFallbackPage();

        // 验证包含 pages
        $this->assertStringContainsString('pages', $fallbackPage);
        // 验证包含 index
        $this->assertStringContainsString('index', $fallbackPage);
    }
}
