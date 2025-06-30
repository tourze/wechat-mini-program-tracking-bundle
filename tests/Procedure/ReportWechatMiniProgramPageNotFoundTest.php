<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;
use WechatMiniProgramTrackingBundle\Procedure\ReportWechatMiniProgramPageNotFound;

class ReportWechatMiniProgramPageNotFoundTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ReportWechatMiniProgramPageNotFound $procedure;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->procedure = new ReportWechatMiniProgramPageNotFound($this->entityManager);
    }

    /**
     * 测试执行基本错误上报
     */
    public function testExecuteBasicError(): void
    {
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(PageNotFoundLog::class));
        
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->procedure->error = [
            'path' => '/pages/nonexistent',
            'openType' => 'navigate',
            'query' => ['id' => '123'],
        ];

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('time', $result);
        $this->assertIsInt($result['time']);
        $this->assertArrayNotHasKey('__reLaunch', $result);
    }

    /**
     * 测试应用启动时页面不存在的处理
     */
    public function testExecuteAppLaunchError(): void
    {
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (PageNotFoundLog $log) {
                return $log->getPath() === '/pages/missing' &&
                    $log->getOpenType() === 'appLaunch';
            }));
        
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->procedure->error = [
            'path' => '/pages/missing',
            'openType' => 'appLaunch',
        ];

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('time', $result);
        $this->assertArrayHasKey('__reLaunch', $result);
        $this->assertIsArray($result['__reLaunch']);
        $this->assertArrayHasKey('url', $result['__reLaunch']);
        $this->assertEquals('/pages/index/index?_from=page_not_found', $result['__reLaunch']['url']);
    }

    /**
     * 测试使用自定义回退页面
     */
    public function testExecuteWithCustomFallbackPage(): void
    {
        $_ENV['WECHAT_MINI_PROGRAM_NOT_FOUND_FALLBACK_PAGE'] = '/pages/home/home';

        $this->entityManager->expects($this->once())
            ->method('persist');
        
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->procedure->error = [
            'path' => '/pages/missing',
            'openType' => 'appLaunch',
        ];

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('__reLaunch', $result);
        $this->assertEquals('/pages/home/home', $result['__reLaunch']['url']);

        // 清理环境变量
        unset($_ENV['WECHAT_MINI_PROGRAM_NOT_FOUND_FALLBACK_PAGE']);
    }

    /**
     * 测试完整的错误信息存储
     */
    public function testExecuteStoresCompleteErrorInfo(): void
    {
        $expectedError = [
            'path' => '/pages/test',
            'openType' => 'navigate',
            'query' => ['foo' => 'bar'],
            'message' => 'Page not found',
        ];

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (PageNotFoundLog $log) use ($expectedError) {
                $rawError = json_decode($log->getRawError(), true);
                return $log->getPath() === '/pages/test' &&
                    $log->getOpenType() === 'navigate' &&
                    $log->getQuery() === ['foo' => 'bar'] &&
                    $rawError === $expectedError;
            }));

        $this->procedure->error = $expectedError;
        $this->procedure->launchOptions = ['scene' => 1001];
        $this->procedure->enterOptions = ['referrerInfo' => ['appId' => 'wx123']];

        $result = $this->procedure->execute();

        $this->assertArrayHasKey('time', $result);
    }

    /**
     * 测试缺少 openType 时的处理
     */
    public function testExecuteWithMissingOpenType(): void
    {
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (PageNotFoundLog $log) {
                return $log->getOpenType() === '';
            }));

        $this->procedure->error = [
            'path' => '/pages/test',
        ];

        $result = $this->procedure->execute();

        $this->assertArrayNotHasKey('__reLaunch', $result);
    }
}