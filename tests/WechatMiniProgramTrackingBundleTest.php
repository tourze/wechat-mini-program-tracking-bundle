<?php

namespace WechatMiniProgramTrackingBundle\Tests;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\WechatMiniProgramBundle;
use WechatMiniProgramTrackingBundle\WechatMiniProgramTrackingBundle;

class WechatMiniProgramTrackingBundleTest extends TestCase
{
    /**
     * 测试 Bundle 依赖配置
     */
    public function testGetBundleDependencies(): void
    {
        $dependencies = WechatMiniProgramTrackingBundle::getBundleDependencies();

        // 检查依赖中是否包含 WechatMiniProgramBundle
        $this->assertArrayHasKey(WechatMiniProgramBundle::class, $dependencies);

        // 检查依赖配置是否正确
        $this->assertIsArray($dependencies[WechatMiniProgramBundle::class]);
        $this->assertArrayHasKey('all', $dependencies[WechatMiniProgramBundle::class]);
        $this->assertTrue($dependencies[WechatMiniProgramBundle::class]['all']);
    }

    /**
     * 测试 Bundle 实例化
     */
    public function testBundleInstantiation(): void
    {
        $bundle = new WechatMiniProgramTrackingBundle();
        $this->assertInstanceOf(WechatMiniProgramTrackingBundle::class, $bundle);
    }
}
