<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use WechatMiniProgramTrackingBundle\Config\TrackingConfig;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogRequest;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogResponse;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;
use WechatMiniProgramTrackingBundle\Service\JumpTrackingLogService;

/**
 * 测试用的用户接口
 */
interface TestUserInterface extends UserInterface
{
    public function getIdentity(): ?string;
}

/**
 * @internal
 */
#[CoversClass(JumpTrackingLogService::class)]
final class JumpTrackingLogServiceTest extends TestCase
{
    private JumpTrackingLogService $service;
    private AsyncInsertService $doctrineService;
    private Security $security;
    private TrackingConfig $config;

    protected function setUp(): void
    {
        $this->doctrineService = $this->createMock(AsyncInsertService::class);
        $this->security = $this->createMock(Security::class);
        $this->config = $this->createMock(TrackingConfig::class);

        $this->service = new JumpTrackingLogService(
            $this->doctrineService,
            $this->security,
            $this->config,
        );
    }

    /**
     * 测试成功处理报告
     */
    public function testHandleReportSuccess(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/test';
        $request->sessionId = 'session123';

        $this->security->method('getUser')->willReturn(null);

        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->isInstanceOf(JumpTrackingLog::class));

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
        $this->assertIsInt($response->id);
    }

    /**
     * 测试带用户信息的报告处理
     */
    public function testHandleReportWithUser(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/test';

        // 创建一个支持 getIdentity 方法的 mock 用户
        $user = $this->createMock(TestUserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user123');
        $user->method('getIdentity')->willReturn('identity456');
        $user->method('getRoles')->willReturn(['ROLE_USER']);

        $this->security->method('getUser')->willReturn($user);
        $this->config->method('supportsUserIdentity')->willReturn(true);

        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->isInstanceOf(JumpTrackingLog::class));

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
        $this->assertIsInt($response->id);
    }

    /**
     * 测试 getIdentity 方法异常处理
     */
    public function testHandleReportWithUserIdentityException(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/test';

        // 创建一个 getIdentity 方法会抛出异常的用户
        $user = $this->createMock(TestUserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user123');
        $user->method('getIdentity')->willThrowException(new \RuntimeException('Identity not available'));
        $user->method('getRoles')->willReturn(['ROLE_USER']);

        $this->security->method('getUser')->willReturn($user);
        $this->config->method('supportsUserIdentity')->willReturn(true);

        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->isInstanceOf(JumpTrackingLog::class));

        $response = $this->service->handleReport($request);

        $this->assertTrue($response->success);
        $this->assertIsInt($response->id);
    }

    /**
     * 测试保存失败的情况
     */
    public function testHandleReportSaveFailure(): void
    {
        $request = new ReportJumpTrackingLogRequest();
        $request->currentPath = '/pages/test';

        $this->security->method('getUser')->willReturn(null);

        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->willThrowException(new \RuntimeException('Database error'));

        $response = $this->service->handleReport($request);

        $this->assertFalse($response->success);
        $this->assertStringContainsString('处理请求失败', $response->message);
    }

    /**
     * 测试无效请求参数
     */
    public function testHandleReportInvalidRequest(): void
    {
        $request = $this->createMock(ReportJumpTrackingLogRequest::class);
        $request->method('validate')->willThrowException(new \InvalidArgumentException('Invalid parameter'));

        $response = $this->service->handleReport($request);

        $this->assertFalse($response->success);
        $this->assertStringContainsString('请求参数无效', $response->message);
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

        $this->security->method('getUser')->willReturn(null);

        $jumpTrackingLog = $this->service->createTrackingLog($request);

        $this->assertInstanceOf(JumpTrackingLog::class, $jumpTrackingLog);
        $this->assertSame('/pages/test', $jumpTrackingLog->getPage());
        $this->assertTrue($jumpTrackingLog->isJumpResult());
        $this->assertSame('Apple', $jumpTrackingLog->getDeviceBrand());
        $this->assertSame('device123', $jumpTrackingLog->getDeviceId());
        $this->assertSame('page_view', $jumpTrackingLog->getEventName());
        $this->assertSame('session456', $jumpTrackingLog->getSessionId());
    }
}