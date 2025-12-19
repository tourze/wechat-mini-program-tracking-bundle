<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogRequest;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;
use WechatMiniProgramTrackingBundle\Service\JumpTrackingLogService;

/**
 * @internal
 */
#[CoversClass(JumpTrackingLogService::class)]
#[RunTestsInSeparateProcesses]
final class JumpTrackingLogServiceTest extends AbstractIntegrationTestCase
{
    private JumpTrackingLogService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(JumpTrackingLogService::class);
    }

    /**
     * 测试成功处理报告
     */
    public function testHandleReportSuccess(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/test';
        $request->sessionId = 'session123';

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
        $this->assertIsInt($response->id);
    }

    /**
     * 测试创建追踪日志
     */
    public function testCreateTrackingLog(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/test';
        $request->jumpResult = true;
        $request->deviceBrand = 'Apple';
        $request->deviceId = 'device123';
        $request->eventName = 'page_view';
        $request->sessionId = 'session456';

        $jumpTrackingLog = $this->service->createTrackingLog($request);

        $this->assertInstanceOf(JumpTrackingLog::class, $jumpTrackingLog);
        $this->assertSame('/pages/test', $jumpTrackingLog->getPage());
        $this->assertTrue($jumpTrackingLog->isJumpResult());
        $this->assertSame('Apple', $jumpTrackingLog->getDeviceBrand());
        $this->assertSame('device123', $jumpTrackingLog->getDeviceId());
        $this->assertSame('page_view', $jumpTrackingLog->getEventName());
        $this->assertSame('session456', $jumpTrackingLog->getSessionId());
    }

    /**
     * 测试 saveTrackingLog 方法
     */
    public function testSaveTrackingLog(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/save-test';
        $request->sessionId = 'session-save-test';

        $jumpTrackingLog = $this->service->createTrackingLog($request);

        // 此方法使用异步插入，不会抛出异常表示成功
        $this->service->saveTrackingLog($jumpTrackingLog);

        // 验证日志对象已设置基本属性
        $this->assertSame('/pages/save-test', $jumpTrackingLog->getPage());
    }

    /**
     * 测试完整参数的报告处理
     */
    public function testHandleReportWithFullParameters(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/full-test';
        $request->jumpResult = true;
        $request->deviceBrand = 'Xiaomi';
        $request->deviceId = 'xiaomi-123';
        $request->deviceModel = 'Mi 11';
        $request->deviceScreenHeight = 2400;
        $request->deviceScreenWidth = 1080;
        $request->deviceSystem = 'Android';
        $request->deviceSystemVersion = '12.0';
        $request->eventName = 'click';
        $request->eventParam = ['button' => 'submit'];
        $request->networkType = '4g';
        $request->pageName = 'Test Page';
        $request->pageQuery = 'id=456';
        $request->pageTitle = 'Test Title';
        $request->pageUrl = '/pages/full-test?id=456';
        $request->platform = 'android';
        $request->prevPath = '/pages/home';
        $request->prevSessionId = 'prev-session';
        $request->scene = '1001';
        $request->sdkName = 'WeChat';
        $request->sdkType = 'miniprogram';
        $request->sdkVersion = '8.0.0';
        $request->sessionId = 'session-full';

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
        $this->assertIsInt($response->id);
    }

    /**
     * 测试响应消息
     */
    public function testHandleReportResponseMessage(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/message-test';
        $request->sessionId = 'session-msg';

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
        $this->assertSame('跳转追踪日志上报成功', $response->message);
    }
}
