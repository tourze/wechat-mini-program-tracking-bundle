<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundRequest;

/**
 * @internal
 */
#[CoversClass(ReportWechatMiniProgramPageNotFoundRequest::class)]
final class ReportWechatMiniProgramPageNotFoundRequestTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruction(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/test', 'openType' => 'navigateTo', 'query' => []],
            launchOptions: ['scene' => 1001],
            enterOptions: ['from' => 'test']
        );

        $this->assertSame(['path' => 'pages/test', 'openType' => 'navigateTo', 'query' => []], $request->error);
        $this->assertSame(['scene' => 1001], $request->launchOptions);
        $this->assertSame(['from' => 'test'], $request->enterOptions);
    }

    /**
     * 测试只有 error 参数的构造
     */
    public function testConstructionWithErrorOnly(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/test']
        );

        $this->assertSame(['path' => 'pages/test'], $request->error);
        $this->assertNull($request->launchOptions);
        $this->assertNull($request->enterOptions);
    }

    /**
     * 测试 getErrorPath 方法
     */
    public function testGetErrorPath(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/not-exist/index']
        );

        $this->assertSame('pages/not-exist/index', $request->getErrorPath());
    }

    /**
     * 测试 getErrorPath 缺失 path 时返回空字符串
     */
    public function testGetErrorPathWhenMissing(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['openType' => 'navigateTo']
        );

        $this->assertSame('', $request->getErrorPath());
    }

    /**
     * 测试 getErrorOpenType 方法
     */
    public function testGetErrorOpenType(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/test', 'openType' => 'redirectTo']
        );

        $this->assertSame('redirectTo', $request->getErrorOpenType());
    }

    /**
     * 测试 getErrorOpenType 缺失时返回 null
     */
    public function testGetErrorOpenTypeWhenMissing(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/test']
        );

        $this->assertNull($request->getErrorOpenType());
    }

    /**
     * 测试 getErrorQuery 方法
     */
    public function testGetErrorQuery(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/test', 'query' => ['id' => '123', 'name' => 'test']]
        );

        $this->assertSame(['id' => '123', 'name' => 'test'], $request->getErrorQuery());
    }

    /**
     * 测试 getErrorQuery 缺失时返回空数组
     */
    public function testGetErrorQueryWhenMissing(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/test']
        );

        $this->assertSame([], $request->getErrorQuery());
    }

    /**
     * 测试 isAppLaunch 方法
     */
    public function testIsAppLaunch(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/test', 'openType' => 'appLaunch']
        );

        $this->assertTrue($request->isAppLaunch());
    }

    /**
     * 测试 isAppLaunch 非 appLaunch 时返回 false
     */
    public function testIsAppLaunchReturnsFalse(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/test', 'openType' => 'navigateTo']
        );

        $this->assertFalse($request->isAppLaunch());
    }

    /**
     * 测试 fromProcedure 静态方法
     */
    public function testFromProcedure(): void
    {
        $procedure = new class {
            /** @var array<string, mixed> */
            public array $error = ['path' => 'pages/proc', 'openType' => 'switchTab'];

            /** @return array<string, mixed> */
            public function getLaunchOptions(): array
            {
                return ['scene' => 1089];
            }

            /** @return array<string, mixed> */
            public function getEnterOptions(): array
            {
                return ['referrer' => 'test'];
            }
        };

        $request = ReportWechatMiniProgramPageNotFoundRequest::fromProcedure($procedure);

        $this->assertSame(['path' => 'pages/proc', 'openType' => 'switchTab'], $request->error);
        $this->assertSame(['scene' => 1089], $request->launchOptions);
        $this->assertSame(['referrer' => 'test'], $request->enterOptions);
    }

    /**
     * 测试 validate 方法空错误时抛出异常
     */
    public function testValidateThrowsOnEmptyError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('错误信息不能为空');

        $request = new ReportWechatMiniProgramPageNotFoundRequest(error: []);
        $request->validate();
    }

    /**
     * 测试 validate 方法正常数据不抛出异常
     */
    public function testValidateDoesNotThrowOnValidData(): void
    {
        $request = new ReportWechatMiniProgramPageNotFoundRequest(
            error: ['path' => 'pages/test']
        );

        $request->validate();

        $this->assertTrue(true); // 如果执行到这里，说明没有抛出异常
    }
}
