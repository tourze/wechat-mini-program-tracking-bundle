<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
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

        // 验证重启URL包含默认页面或环境变量指定的页面
        $expectedUrl = $_ENV['WECHAT_MINI_PROGRAM_NOT_FOUND_FALLBACK_PAGE'] ?? 'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index?_from=page_not_found';
        $this->assertSame($expectedUrl, $result['__reLaunch']['url']);
    }
}
