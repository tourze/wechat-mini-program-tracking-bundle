<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundResponse;
use WechatMiniProgramTrackingBundle\Param\ReportWechatMiniProgramPageNotFoundParam;
use WechatMiniProgramTrackingBundle\Procedure\ReportWechatMiniProgramPageNotFound;

/**
 * @internal
 */
#[CoversClass(ReportWechatMiniProgramPageNotFound::class)]
#[RunTestsInSeparateProcesses]
final class ReportWechatMiniProgramPageNotFoundTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试设置
    }

    /**
     * 测试 procedure 可以正确从容器获取
     */
    public function testProcedureCanBeResolvedFromContainer(): void
    {
        $procedure = self::getService(ReportWechatMiniProgramPageNotFound::class);
        $this->assertInstanceOf(ReportWechatMiniProgramPageNotFound::class, $procedure);
    }

    /**
     * 测试 execute() 方法执行页面404上报
     */
    public function testExecuteCreatesPageNotFoundLog(): void
    {
        $procedure = self::getService(ReportWechatMiniProgramPageNotFound::class);

        $param = new ReportWechatMiniProgramPageNotFoundParam(
            error: [
                'path' => 'pages/not-exist/index',
                'openType' => 'navigateTo',
                'query' => ['param1' => 'value1'],
            ],
            launchOptions: ['scene' => 1001],
            enterOptions: ['from' => 'test']
        );

        $result = $procedure->execute($param);

        // 验证返回结果包含时间戳
        $this->assertArrayHasKey('time', $result);
        $this->assertIsInt($result['time']);

        // 验证没有重启操作(因为不是appLaunch)
        $this->assertArrayNotHasKey('__reLaunch', $result);
    }

    /**
     * 测试 execute() 方法在app启动时的重启逻辑
     */
    public function testExecuteWithAppLaunchReturnsReLaunchUrl(): void
    {
        $procedure = self::getService(ReportWechatMiniProgramPageNotFound::class);

        $param = new ReportWechatMiniProgramPageNotFoundParam(
            error: [
                'path' => 'pages/not-exist/index',
                'openType' => 'appLaunch',
                'query' => [],
            ],
            launchOptions: ['scene' => 1001],
            enterOptions: ['from' => 'launch']
        );

        $result = $procedure->execute($param);

        // 验证返回结果包含时间戳
        $this->assertArrayHasKey('time', $result);
        $this->assertIsInt($result['time']);

        // 验证包含重启操作
        $this->assertArrayHasKey('__reLaunch', $result);
        $this->assertIsArray($result['__reLaunch']);
        $this->assertArrayHasKey('url', $result['__reLaunch']);

        // 验证重启URL包含默认页面
        $expectedUrl = 'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index?_from=page_not_found';
        $this->assertSame($expectedUrl, $result['__reLaunch']['url']);
    }

    /**
     * 测试异常处理
     */
    public function testExecuteWithException(): void
    {
        $procedure = self::getService(ReportWechatMiniProgramPageNotFound::class);

        $param = new ReportWechatMiniProgramPageNotFoundParam(
            error: [
                'path' => 'pages/test/index',
                'openType' => 'navigateTo',
                'query' => [],
            ]
        );

        $result = $procedure->execute($param);

        // 验证正常响应格式
        $this->assertArrayHasKey('time', $result);
        $this->assertIsInt($result['time']);
    }

    /**
     * 测试响应 DTO 创建
     */
    public function testResponseDTOCreation(): void
    {
        $response = ReportWechatMiniProgramPageNotFoundResponse::success(12345);
        $this->assertTrue($response->success);
        $this->assertSame(12345, $response->time);
        $this->assertNull($response->reLaunch);

        $responseWithReLaunch = ReportWechatMiniProgramPageNotFoundResponse::withReLaunch(12345, 'pages/index/index');
        $this->assertTrue($responseWithReLaunch->success);
        $this->assertSame(12345, $responseWithReLaunch->time);
        $this->assertSame(['url' => 'pages/index/index'], $responseWithReLaunch->reLaunch);

        $responseWithFailure = ReportWechatMiniProgramPageNotFoundResponse::failure(12345, 'Test error');
        $this->assertFalse($responseWithFailure->success);
        $this->assertSame(12345, $responseWithFailure->time);
        $this->assertSame('Test error', $responseWithFailure->message);
    }
}
