<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundRequest;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;
use WechatMiniProgramTrackingBundle\Service\PageNotFoundLogService;

/**
 * @internal
 */
#[CoversClass(PageNotFoundLogService::class)]
#[RunTestsInSeparateProcesses]
final class PageNotFoundLogServiceTest extends AbstractIntegrationTestCase
{
    private PageNotFoundLogService $service;

    protected function onSetUp(): void
    {
        $this->service = self::getService(PageNotFoundLogService::class);
    }

    /**
     * 测试成功处理页面不存在报告
     */
    public function testHandleReportSuccess(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: [
                'path' => 'pages/not-exist/index',
                'openType' => 'navigateTo',
                'query' => ['param1' => 'value1']
            ]
        );

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
        $this->assertIsInt($response->time);
        $this->assertNull($response->reLaunch);
    }

    /**
     * 测试应用启动时的页面不存在报告
     */
    public function testHandleReportWithAppLaunch(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: [
                'path' => 'pages/not-exist/index',
                'openType' => 'appLaunch',
                'query' => []
            ]
        );

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
        $this->assertIsInt($response->time);
        $this->assertNotNull($response->reLaunch);
        $this->assertArrayHasKey('url', $response->reLaunch);
    }

    /**
     * 测试创建页面不存在日志
     */
    public function testCreatePageNotFoundLog(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: [
                'path' => 'pages/not-exist/index',
                'openType' => 'navigateTo',
                'query' => ['param1' => 'value1']
            ],
            launchOptions: ['scene' => 1001],
            enterOptions: ['from' => 'test']
        );

        $pageNotFoundLog = $this->service->createPageNotFoundLog($request);

        $this->assertInstanceOf(PageNotFoundLog::class, $pageNotFoundLog);
        $this->assertSame('pages/not-exist/index', $pageNotFoundLog->getPath());
        $this->assertSame('navigateTo', $pageNotFoundLog->getOpenType());
        $this->assertSame(['param1' => 'value1'], $pageNotFoundLog->getQuery());
        $this->assertNotNull($pageNotFoundLog->getRawError());
    }

    /**
     * 测试生成重启 URL
     */
    public function testGenerateReLaunchUrl(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: [
                'path' => 'pages/not-exist/index',
                'openType' => 'appLaunch',
                'query' => []
            ]
        );

        $reLaunchUrl = $this->service->generateReLaunchUrl($request);

        $this->assertNotNull($reLaunchUrl);
        $this->assertStringContainsString('_from=page_not_found', $reLaunchUrl);
    }

    /**
     * 测试非应用启动时不生成重启 URL
     */
    public function testGenerateReLaunchUrlNotAppLaunch(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: [
                'path' => 'pages/not-exist/index',
                'openType' => 'navigateTo',
                'query' => []
            ]
        );

        $reLaunchUrl = $this->service->generateReLaunchUrl($request);

        $this->assertNull($reLaunchUrl);
    }

    /**
     * 测试带启动选项的请求
     */
    public function testHandleReportWithLaunchOptions(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: [
                'path' => 'pages/not-exist/index',
                'openType' => 'navigateTo',
                'query' => [],
            ],
            launchOptions: ['scene' => 1001],
            enterOptions: ['from' => 'test']
        );

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
    }

    /**
     * 测试保存页面不存在日志
     */
    public function testSavePageNotFoundLog(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: [
                'path' => 'pages/save-test/index',
                'openType' => 'navigateTo',
                'query' => []
            ]
        );

        $pageNotFoundLog = $this->service->createPageNotFoundLog($request);

        // savePageNotFoundLog 会持久化到数据库
        $this->service->savePageNotFoundLog($pageNotFoundLog);

        // 验证日志对象已设置基本属性
        $this->assertSame('pages/save-test/index', $pageNotFoundLog->getPath());
    }
}
