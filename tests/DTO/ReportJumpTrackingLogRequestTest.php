<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogRequest;

/**
 * @internal
 */
#[CoversClass(ReportJumpTrackingLogRequest::class)]
final class ReportJumpTrackingLogRequestTest extends TestCase
{
    /**
     * 测试默认属性值
     */
    public function testDefaultPropertyValues(): void
    {
        $request = new ReportJumpTrackingLogRequest();

        $this->assertNull($request->currentPath);
        $this->assertFalse($request->jumpResult);
        $this->assertNull($request->deviceBrand);
        $this->assertNull($request->deviceId);
        $this->assertNull($request->deviceModel);
        $this->assertNull($request->deviceScreenHeight);
        $this->assertNull($request->deviceScreenWidth);
        $this->assertNull($request->deviceSystem);
        $this->assertNull($request->deviceSystemVersion);
        $this->assertNull($request->eventName);
        $this->assertNull($request->eventParam);
        $this->assertNull($request->networkType);
        $this->assertNull($request->pageName);
        $this->assertNull($request->pageQuery);
        $this->assertNull($request->pageTitle);
        $this->assertNull($request->pageUrl);
        $this->assertNull($request->platform);
        $this->assertNull($request->prevPath);
        $this->assertNull($request->prevSessionId);
        $this->assertNull($request->scene);
        $this->assertNull($request->sdkName);
        $this->assertNull($request->sdkType);
        $this->assertNull($request->sdkVersion);
        $this->assertNull($request->sessionId);
    }

    /**
     * 测试设置属性
     */
    public function testSetProperties(): void
    {
        $request = new ReportJumpTrackingLogRequest();

        $request->currentPath = '/pages/test';
        $request->jumpResult = true;
        $request->deviceBrand = 'Apple';
        $request->deviceId = 'device123';
        $request->sessionId = 'session456';

        $this->assertSame('/pages/test', $request->currentPath);
        $this->assertTrue($request->jumpResult);
        $this->assertSame('Apple', $request->deviceBrand);
        $this->assertSame('device123', $request->deviceId);
        $this->assertSame('session456', $request->sessionId);
    }

    /**
     * 测试 fromProcedure 静态方法
     */
    public function testFromProcedure(): void
    {
        $procedure = new \stdClass();
        $procedure->currentPath = '/pages/test';
        $procedure->jumpResult = true;
        $procedure->deviceBrand = 'Samsung';
        $procedure->deviceId = 'samsung123';
        $procedure->deviceModel = 'Galaxy S21';
        $procedure->deviceScreenHeight = 2400;
        $procedure->deviceScreenWidth = 1080;
        $procedure->deviceSystem = 'Android';
        $procedure->deviceSystemVersion = '12.0';
        $procedure->eventName = 'click';
        $procedure->eventParam = ['button' => 'submit'];
        $procedure->networkType = '5g';
        $procedure->pageName = 'Test Page';
        $procedure->pageQuery = 'id=123';
        $procedure->pageTitle = 'Test Title';
        $procedure->pageUrl = '/pages/test?id=123';
        $procedure->platform = 'android';
        $procedure->prevPath = '/pages/home';
        $procedure->prevSessionId = 'prev123';
        $procedure->scene = '1001';
        $procedure->sdkName = 'WeChat';
        $procedure->sdkType = 'miniprogram';
        $procedure->sdkVersion = '8.0.0';
        $procedure->sessionId = 'session456';

        $request = ReportJumpTrackingLogRequest::fromProcedure($procedure);

        $this->assertSame('/pages/test', $request->currentPath);
        $this->assertTrue($request->jumpResult);
        $this->assertSame('Samsung', $request->deviceBrand);
        $this->assertSame('samsung123', $request->deviceId);
        $this->assertSame('Galaxy S21', $request->deviceModel);
        $this->assertSame(2400, $request->deviceScreenHeight);
        $this->assertSame(1080, $request->deviceScreenWidth);
        $this->assertSame('Android', $request->deviceSystem);
        $this->assertSame('12.0', $request->deviceSystemVersion);
        $this->assertSame('click', $request->eventName);
        $this->assertSame(['button' => 'submit'], $request->eventParam);
        $this->assertSame('5g', $request->networkType);
        $this->assertSame('Test Page', $request->pageName);
        $this->assertSame('id=123', $request->pageQuery);
        $this->assertSame('Test Title', $request->pageTitle);
        $this->assertSame('/pages/test?id=123', $request->pageUrl);
        $this->assertSame('android', $request->platform);
        $this->assertSame('/pages/home', $request->prevPath);
        $this->assertSame('prev123', $request->prevSessionId);
        $this->assertSame('1001', $request->scene);
        $this->assertSame('WeChat', $request->sdkName);
        $this->assertSame('miniprogram', $request->sdkType);
        $this->assertSame('8.0.0', $request->sdkVersion);
        $this->assertSame('session456', $request->sessionId);
    }

    /**
     * 测试 fromProcedure 处理不完整的 procedure
     */
    public function testFromProcedureWithPartialData(): void
    {
        $procedure = new \stdClass();
        $procedure->currentPath = '/pages/partial';

        $request = ReportJumpTrackingLogRequest::fromProcedure($procedure);

        $this->assertSame('/pages/partial', $request->currentPath);
        $this->assertNull($request->deviceBrand);
        $this->assertNull($request->sessionId);
    }

    /**
     * 测试 validate 方法不抛出异常
     */
    public function testValidateDoesNotThrow(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/test';

        // validate 方法目前只是一个占位符
        $request->validate();

        $this->assertTrue(true); // 如果执行到这里，说明没有抛出异常
    }
}
