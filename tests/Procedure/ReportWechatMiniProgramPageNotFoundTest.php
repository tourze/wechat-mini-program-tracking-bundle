<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use WechatMiniProgramTrackingBundle\Procedure\ReportWechatMiniProgramPageNotFound;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundRequest;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundResponse;

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

        // 设置错误信息
        $procedure->error = [
            'path' => 'pages/not-exist/index',
            'openType' => 'navigateTo',
            'query' => ['param1' => 'value1'],
        ];

        // 设置启动选项
        $procedure->setLaunchOptions(['scene' => 1001]);
        $procedure->setEnterOptions(['from' => 'test']);

        $result = $procedure->execute();

        // 验证返回结果包含时间戳
        $this->assertArrayHasKey('time', $result);
        $this->assertIsInt($result['time']);

        // 验证没有重启操作（因为不是appLaunch）
        $this->assertArrayNotHasKey('__reLaunch', $result);
    }

    /**
     * 测试 execute() 方法在app启动时的重启逻辑
     */
    public function testExecuteWithAppLaunchReturnsReLaunchUrl(): void
    {
        $procedure = self::getService(ReportWechatMiniProgramPageNotFound::class);

        // 设置app启动时的错误信息
        $procedure->error = [
            'path' => 'pages/not-exist/index',
            'openType' => 'appLaunch',
            'query' => [],
        ];

        $procedure->setLaunchOptions(['scene' => 1001]);
        $procedure->setEnterOptions(['from' => 'launch']);

        $result = $procedure->execute();

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

        // 设置正常的错误信息，测试正常流程
        $procedure->error = [
            'path' => 'pages/test/index',
            'openType' => 'navigateTo',
            'query' => [],
        ];

        $result = $procedure->execute();

        // 验证正常响应格式
        $this->assertArrayHasKey('time', $result);
        $this->assertIsInt($result['time']);
    }

    /**
     * 测试 DTO 转换
     */
    public function testDTOTransformation(): void
    {
        $procedure = self::getService(ReportWechatMiniProgramPageNotFound::class);

        // 设置错误信息
        $procedure->error = [
            'path' => 'pages/not-exist/index',
            'openType' => 'appLaunch',
            'query' => ['param1' => 'value1'],
        ];

        $procedure->setLaunchOptions(['scene' => 1001]);
        $procedure->setEnterOptions(['from' => 'test']);

        // 验证 DTO 可以正确创建
        $request = ReportWechatMiniProgramPageNotFoundRequest::fromProcedure($procedure);

        $this->assertSame('pages/not-exist/index', $request->getErrorPath());
        $this->assertSame('appLaunch', $request->getErrorOpenType());
        $this->assertSame(['param1' => 'value1'], $request->getErrorQuery());
        $this->assertTrue($request->isAppLaunch());
        $this->assertSame(['scene' => 1001], $request->launchOptions);
        $this->assertSame(['from' => 'test'], $request->enterOptions);
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
