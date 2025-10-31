<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use WechatMiniProgramTrackingBundle\Procedure\ReportJumpTrackingLog;

/**
 * @internal
 */
#[CoversClass(ReportJumpTrackingLog::class)]
#[RunTestsInSeparateProcesses]
final class ReportJumpTrackingLogTest extends AbstractProcedureTestCase
{
    private ReportJumpTrackingLog $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(ReportJumpTrackingLog::class);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultPropertyValues(): void
    {
        $this->assertNull($this->procedure->currentPath);
        $this->assertFalse($this->procedure->jumpResult);
        $this->assertNull($this->procedure->deviceBrand);
        $this->assertNull($this->procedure->deviceId);
        $this->assertNull($this->procedure->deviceModel);
        $this->assertNull($this->procedure->deviceScreenHeight);
        $this->assertNull($this->procedure->deviceScreenWidth);
        $this->assertNull($this->procedure->deviceSystem);
        $this->assertNull($this->procedure->deviceSystemVersion);
        $this->assertNull($this->procedure->eventName);
        $this->assertNull($this->procedure->eventParam);
        $this->assertNull($this->procedure->networkType);
        $this->assertNull($this->procedure->pageName);
        $this->assertNull($this->procedure->pageQuery);
        $this->assertNull($this->procedure->pageTitle);
        $this->assertNull($this->procedure->pageUrl);
        $this->assertNull($this->procedure->platform);
        $this->assertNull($this->procedure->prevPath);
        $this->assertNull($this->procedure->prevSessionId);
        $this->assertNull($this->procedure->scene);
        $this->assertNull($this->procedure->sdkName);
        $this->assertNull($this->procedure->sdkType);
        $this->assertNull($this->procedure->sdkVersion);
        $this->assertNull($this->procedure->sessionId);
    }

    /**
     * 测试 execute 方法（无用户）
     */
    public function testExecuteWithoutUser(): void
    {
        $this->procedure->currentPath = '/pages/index';
        $this->procedure->jumpResult = true;
        $this->procedure->sessionId = 'session123';

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('id', $result);
    }

    /**
     * 测试 execute 方法（完整参数）
     */
    public function testExecuteWithFullParameters(): void
    {
        $this->setUpFullParameters();

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('id', $result);
    }

    private function setUpFullParameters(): void
    {
        $this->procedure->currentPath = '/pages/test';
        $this->procedure->jumpResult = true;
        $this->procedure->deviceBrand = 'Apple';
        $this->procedure->deviceId = 'device123';
        $this->procedure->deviceModel = 'iPhone 12';
        $this->procedure->deviceScreenHeight = 2532;
        $this->procedure->deviceScreenWidth = 1170;
        $this->procedure->deviceSystem = 'iOS';
        $this->procedure->deviceSystemVersion = '15.0';
        $this->procedure->eventName = 'page_view';
        $this->procedure->eventParam = ['test' => 'value'];
        $this->procedure->networkType = 'wifi';
        $this->procedure->pageName = 'Test Page';
        $this->procedure->pageQuery = 'id=123';
        $this->procedure->pageTitle = 'Test Title';
        $this->procedure->pageUrl = '/pages/test?id=123';
        $this->procedure->platform = 'iOS';
        $this->procedure->prevPath = '/pages/home';
        $this->procedure->prevSessionId = 'prev123';
        $this->procedure->scene = '1001';
        $this->procedure->sdkName = 'WeChat';
        $this->procedure->sdkType = 'miniprogram';
        $this->procedure->sdkVersion = '8.0.0';
        $this->procedure->sessionId = 'session456';
    }
}
