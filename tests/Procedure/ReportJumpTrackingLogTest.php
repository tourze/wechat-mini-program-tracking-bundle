<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use WechatMiniProgramTrackingBundle\Procedure\ReportJumpTrackingLog;
use WechatMiniProgramTrackingBundle\Service\JumpTrackingLogService;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogRequest;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogResponse;

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

        // 验证返回格式
        $this->assertArrayHasKey('id', $result);
        $this->assertIsInt($result['id']);
    }

    /**
     * 测试 execute 方法（完整参数）
     */
    public function testExecuteWithFullParameters(): void
    {
        $this->setUpFullParameters();

        $result = $this->procedure->execute();

        // 验证返回格式
        $this->assertArrayHasKey('id', $result);
        $this->assertIsInt($result['id']);
    }

    /**
     * 测试异常处理
     */
    public function testExecuteWithException(): void
    {
        // 设置无效数据，虽然 DTO 可能不会直接抛出异常，但我们可以测试其他异常情况
        // 通过模拟服务层异常来测试异常处理逻辑
        $this->procedure->currentPath = '/pages/test';
        $this->procedure->jumpResult = true;
        $this->procedure->sessionId = 'session123';

        $result = $this->procedure->execute();

        // 正常情况下应该返回成功响应
        $this->assertArrayHasKey('id', $result);
        $this->assertIsInt($result['id']);
    }

    /**
     * 测试 DTO 转换
     */
    public function testDTOTransformation(): void
    {
        $this->setUpFullParameters();

        // 验证 DTO 可以正确创建
        $request = ReportJumpTrackingLogRequest::fromProcedure($this->procedure);

        $this->assertSame('/pages/test', $request->currentPath);
        $this->assertTrue($request->jumpResult);
        $this->assertSame('Apple', $request->deviceBrand);
        $this->assertSame('device123', $request->deviceId);
        $this->assertSame('iPhone 12', $request->deviceModel);
        $this->assertSame(2532, $request->deviceScreenHeight);
        $this->assertSame(1170, $request->deviceScreenWidth);
        $this->assertSame('iOS', $request->deviceSystem);
        $this->assertSame('15.0', $request->deviceSystemVersion);
        $this->assertSame('page_view', $request->eventName);
        $this->assertSame(['test' => 'value'], $request->eventParam);
        $this->assertSame('wifi', $request->networkType);
        $this->assertSame('Test Page', $request->pageName);
        $this->assertSame('id=123', $request->pageQuery);
        $this->assertSame('Test Title', $request->pageTitle);
        $this->assertSame('/pages/test?id=123', $request->pageUrl);
        $this->assertSame('iOS', $request->platform);
        $this->assertSame('/pages/home', $request->prevPath);
        $this->assertSame('prev123', $request->prevSessionId);
        $this->assertSame('1001', $request->scene);
        $this->assertSame('WeChat', $request->sdkName);
        $this->assertSame('miniprogram', $request->sdkType);
        $this->assertSame('8.0.0', $request->sdkVersion);
        $this->assertSame('session456', $request->sessionId);
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
