<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundResponse;

/**
 * @internal
 */
#[CoversClass(ReportWechatMiniProgramPageNotFoundResponse::class)]
final class ReportWechatMiniProgramPageNotFoundResponseTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruction(): void
    {
        $response = new ReportWechatMiniProgramPageNotFoundResponse(
            time: 1234567890,
            reLaunch: ['url' => 'pages/index/index'],
            success: true,
            message: '测试消息'
        );

        $this->assertSame(1234567890, $response->time);
        $this->assertSame(['url' => 'pages/index/index'], $response->reLaunch);
        $this->assertTrue($response->success);
        $this->assertSame('测试消息', $response->message);
    }

    /**
     * 测试默认值
     */
    public function testDefaultValues(): void
    {
        $response = new ReportWechatMiniProgramPageNotFoundResponse(time: 1234567890);

        $this->assertSame(1234567890, $response->time);
        $this->assertNull($response->reLaunch);
        $this->assertTrue($response->success);
        $this->assertNull($response->message);
    }

    /**
     * 测试 success 静态方法
     */
    public function testSuccessFactory(): void
    {
        $response = ReportWechatMiniProgramPageNotFoundResponse::success(
            time: 1234567890,
            reLaunch: ['url' => 'pages/home/index'],
            message: '成功'
        );

        $this->assertSame(1234567890, $response->time);
        $this->assertSame(['url' => 'pages/home/index'], $response->reLaunch);
        $this->assertTrue($response->success);
        $this->assertSame('成功', $response->message);
    }

    /**
     * 测试 failure 静态方法
     */
    public function testFailureFactory(): void
    {
        $response = ReportWechatMiniProgramPageNotFoundResponse::failure(
            time: 1234567890,
            message: '处理失败'
        );

        $this->assertSame(1234567890, $response->time);
        $this->assertNull($response->reLaunch);
        $this->assertFalse($response->success);
        $this->assertSame('处理失败', $response->message);
    }

    /**
     * 测试 withReLaunch 静态方法
     */
    public function testWithReLaunchFactory(): void
    {
        $response = ReportWechatMiniProgramPageNotFoundResponse::withReLaunch(
            time: 1234567890,
            reLaunchUrl: 'pages/fallback/index'
        );

        $this->assertSame(1234567890, $response->time);
        $this->assertSame(['url' => 'pages/fallback/index'], $response->reLaunch);
        $this->assertTrue($response->success);
        $this->assertNull($response->message);
    }

    /**
     * 测试 toArray 方法
     */
    public function testToArray(): void
    {
        $response = new ReportWechatMiniProgramPageNotFoundResponse(
            time: 1234567890,
            reLaunch: ['url' => 'pages/index/index'],
            success: true,
            message: '测试'
        );

        $array = $response->toArray();

        $this->assertArrayHasKey('time', $array);
        $this->assertArrayHasKey('success', $array);
        $this->assertArrayHasKey('__reLaunch', $array);
        $this->assertArrayHasKey('message', $array);
        $this->assertSame(1234567890, $array['time']);
        $this->assertTrue($array['success']);
        $this->assertSame(['url' => 'pages/index/index'], $array['__reLaunch']);
        $this->assertSame('测试', $array['message']);
    }

    /**
     * 测试 toArray 方法只包含非空值
     */
    public function testToArrayWithNullValues(): void
    {
        $response = new ReportWechatMiniProgramPageNotFoundResponse(time: 1234567890);

        $array = $response->toArray();

        $this->assertArrayHasKey('time', $array);
        $this->assertArrayHasKey('success', $array);
        $this->assertArrayNotHasKey('__reLaunch', $array);
        $this->assertArrayNotHasKey('message', $array);
    }

    /**
     * 测试 toLegacyArray 方法
     */
    public function testToLegacyArray(): void
    {
        $response = new ReportWechatMiniProgramPageNotFoundResponse(
            time: 1234567890,
            reLaunch: ['url' => 'pages/index/index']
        );

        $array = $response->toLegacyArray();

        $this->assertArrayHasKey('time', $array);
        $this->assertArrayHasKey('__reLaunch', $array);
        $this->assertArrayNotHasKey('success', $array);
        $this->assertArrayNotHasKey('message', $array);
        $this->assertSame(1234567890, $array['time']);
        $this->assertSame(['url' => 'pages/index/index'], $array['__reLaunch']);
    }

    /**
     * 测试 toLegacyArray 方法无 reLaunch 时
     */
    public function testToLegacyArrayWithoutReLaunch(): void
    {
        $response = new ReportWechatMiniProgramPageNotFoundResponse(time: 1234567890);

        $array = $response->toLegacyArray();

        $this->assertArrayHasKey('time', $array);
        $this->assertArrayNotHasKey('__reLaunch', $array);
    }
}
