<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use WechatMiniProgramTrackingBundle\Param\ReportJumpTrackingLogParam;
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
     * 测试 execute 方法(无用户)
     */
    public function testExecuteWithoutUser(): void
    {
        $param = new ReportJumpTrackingLogParam(
            currentPath: '/pages/index',
            jumpResult: true,
            sessionId: 'session123'
        );

        $result = $this->procedure->execute($param);

        // 验证返回格式
        $this->assertArrayHasKey('id', $result);
        $this->assertIsInt($result['id']);
    }

    /**
     * 测试 execute 方法(完整参数)
     */
    public function testExecuteWithFullParameters(): void
    {
        $param = new ReportJumpTrackingLogParam(
            currentPath: '/pages/test',
            jumpResult: true,
            deviceBrand: 'Apple',
            deviceId: 'device123',
            deviceModel: 'iPhone 12',
            deviceScreenHeight: 2532,
            deviceScreenWidth: 1170,
            deviceSystem: 'iOS',
            deviceSystemVersion: '15.0',
            eventName: 'page_view',
            eventParam: ['test' => 'value'],
            networkType: 'wifi',
            pageName: 'Test Page',
            pageQuery: 'id=123',
            pageTitle: 'Test Title',
            pageUrl: '/pages/test?id=123',
            platform: 'iOS',
            prevPath: '/pages/home',
            prevSessionId: 'prev123',
            scene: '1001',
            sdkName: 'WeChat',
            sdkType: 'miniprogram',
            sdkVersion: '8.0.0',
            sessionId: 'session456'
        );

        $result = $this->procedure->execute($param);

        // 验证返回格式
        $this->assertArrayHasKey('id', $result);
        $this->assertIsInt($result['id']);
    }

    /**
     * 测试默认参数值
     */
    public function testExecuteWithDefaultParameters(): void
    {
        $param = new ReportJumpTrackingLogParam();

        $result = $this->procedure->execute($param);

        // 正常情况下应该返回成功响应
        $this->assertArrayHasKey('id', $result);
        $this->assertIsInt($result['id']);
    }
}
