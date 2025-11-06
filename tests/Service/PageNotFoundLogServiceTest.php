<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\Config\TrackingConfig;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundRequest;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundResponse;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;
use WechatMiniProgramTrackingBundle\Service\PageNotFoundLogService;

/**
 * @internal
 */
#[CoversClass(PageNotFoundLogService::class)]
#[RunTestsInSeparateProcesses]
final class PageNotFoundLogServiceTest extends TestCase
{
    private PageNotFoundLogService $service;
    private EntityManagerInterface $entityManager;
    private TrackingConfig $config;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->config = $this->createMock(TrackingConfig::class);

        $this->service = new PageNotFoundLogService(
            $this->entityManager,
            $this->config,
        );
    }

    /**
     * 测试成功处理页面不存在报告
     */
    public function testHandleReportSuccess(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest([
            'path' => 'pages/not-exist/index',
            'openType' => 'navigateTo',
            'query' => ['param1' => 'value1'],
        ]);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(PageNotFoundLog::class));
        $this->entityManager->expects($this->once())
            ->method('flush');

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
        $request = new ReportWechatMiniProgramPageNotFoundRequest([
            'path' => 'pages/not-exist/index',
            'openType' => 'appLaunch',
            'query' => [],
        ]);

        $this->config->expects($this->once())
            ->method('getNotFoundFallbackPage')
            ->willReturn('pages/index/index?_from=page_not_found');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(PageNotFoundLog::class));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
        $this->assertIsInt($response->time);
        $this->assertNotNull($response->reLaunch);
        $this->assertSame('pages/index/index?_from=page_not_found', $response->reLaunch['url']);
    }

    /**
     * 测试保存失败的情况
     */
    public function testHandleReportSaveFailure(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest([
            'path' => 'pages/not-exist/index',
            'openType' => 'navigateTo',
            'query' => [],
        ]);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->willThrowException(new \RuntimeException('Database error'));

        $response = $this->service->handleReport($request);

        $this->assertFalse($response->success);
        $this->assertStringContainsString('处理请求失败', $response->message);
        $this->assertIsInt($response->time);
    }

    /**
     * 测试无效请求参数
     */
    public function testHandleReportInvalidRequest(): void
    {
        $request = $this->createMock(ReportWechatMiniProgramPageNotFoundRequest::class);
        $request->method('validate')->willThrowException(new \InvalidArgumentException('Invalid parameter'));

        $response = $this->service->handleReport($request);

        $this->assertFalse($response->success);
        $this->assertStringContainsString('请求参数无效', $response->message);
        $this->assertIsInt($response->time);
    }

    /**
     * 测试创建页面不存在日志
     */
    public function testCreatePageNotFoundLog(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest([
            'path' => 'pages/not-exist/index',
            'openType' => 'navigateTo',
            'query' => ['param1' => 'value1'],
        ], ['scene' => 1001], ['from' => 'test']);

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
        $request = new ReportWechatMiniProgramPageNotFoundRequest([
            'path' => 'pages/not-exist/index',
            'openType' => 'appLaunch',
            'query' => [],
        ]);

        $this->config->expects($this->once())
            ->method('getNotFoundFallbackPage')
            ->willReturn('pages/index/index?_from=page_not_found');

        $reLaunchUrl = $this->service->generateReLaunchUrl($request);

        $this->assertSame('pages/index/index?_from=page_not_found', $reLaunchUrl);
    }

    /**
     * 测试非应用启动时不生成重启 URL
     */
    public function testGenerateReLaunchUrlNotAppLaunch(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest([
            'path' => 'pages/not-exist/index',
            'openType' => 'navigateTo',
            'query' => [],
        ]);

        $this->config->expects($this->never())
            ->method('getNotFoundFallbackPage');

        $reLaunchUrl = $this->service->generateReLaunchUrl($request);

        $this->assertNull($reLaunchUrl);
    }

    /**
     * 测试带启动选项的请求
     */
    public function testHandleReportWithLaunchOptions(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest([
            'path' => 'pages/not-exist/index',
            'openType' => 'navigateTo',
            'query' => [],
        ], ['scene' => 1001], ['from' => 'test']);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (PageNotFoundLog $log) {
                return $log->getLaunchOptions() === ['scene' => 1001]
                    && $log->getEnterOptions() === ['from' => 'test'];
            }));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
    }
}