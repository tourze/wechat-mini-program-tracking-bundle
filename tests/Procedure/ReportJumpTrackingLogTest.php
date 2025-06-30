<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;
use WechatMiniProgramTrackingBundle\Procedure\ReportJumpTrackingLog;

class ReportJumpTrackingLogTest extends TestCase
{
    private AsyncInsertService $doctrineService;
    private Security $security;
    private ReportJumpTrackingLog $procedure;

    protected function setUp(): void
    {
        $this->doctrineService = $this->createMock(AsyncInsertService::class);
        $this->security = $this->createMock(Security::class);
        
        $this->procedure = new ReportJumpTrackingLog(
            $this->doctrineService,
            $this->security
        );
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
        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->isInstanceOf(JumpTrackingLog::class));

        $this->procedure->currentPath = '/pages/index';
        $this->procedure->jumpResult = true;
        $this->procedure->sessionId = 'session123';

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('id', $result);
    }

    /**
     * 测试 execute 方法（有用户）
     */
    public function testExecuteWithUser(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('user123');

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->callback(function (JumpTrackingLog $log) {
                return $log->getOpenId() === 'user123';
            }));

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('id', $result);
    }

    /**
     * 测试 execute 方法（完整参数）
     */
    public function testExecuteWithFullParameters(): void
    {
        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->callback(function (JumpTrackingLog $log) {
                return $log->getPage() === '/pages/test' &&
                    $log->isJumpResult() === true &&
                    $log->getDeviceBrand() === 'Apple' &&
                    $log->getDeviceId() === 'device123' &&
                    $log->getDeviceModel() === 'iPhone 12' &&
                    $log->getDeviceScreenHeight() === 2532 &&
                    $log->getDeviceScreenWidth() === 1170 &&
                    $log->getDeviceSystem() === 'iOS' &&
                    $log->getDeviceSystemVersion() === '15.0' &&
                    $log->getEventName() === 'page_view' &&
                    $log->getEventParam() === ['test' => 'value'] &&
                    $log->getNetworkType() === 'wifi' &&
                    $log->getPageName() === 'Test Page' &&
                    $log->getPageQuery() === 'id=123' &&
                    $log->getPageTitle() === 'Test Title' &&
                    $log->getPageUrl() === '/pages/test?id=123' &&
                    $log->getPlatform() === 'iOS' &&
                    $log->getPrevPath() === '/pages/home' &&
                    $log->getPrevSessionId() === 'prev123' &&
                    $log->getScene() === '1001' &&
                    $log->getSdkName() === 'WeChat' &&
                    $log->getSdkType() === 'miniprogram' &&
                    $log->getSdkVersion() === '8.0.0' &&
                    $log->getSessionId() === 'session456';
            }));

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

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('id', $result);
    }
}