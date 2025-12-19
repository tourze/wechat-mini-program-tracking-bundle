<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\DTO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogResponse;

/**
 * @internal
 */
#[CoversClass(ReportJumpTrackingLogResponse::class)]
final class ReportJumpTrackingLogResponseTest extends TestCase
{
    /**
     * 测试默认构造函数
     */
    public function testDefaultConstruction(): void
    {
        $response = new ReportJumpTrackingLogResponse();

        $this->assertNull($response->id);
        $this->assertTrue($response->success);
        $this->assertNull($response->message);
    }

    /**
     * 测试带参数的构造函数
     */
    public function testConstructionWithParameters(): void
    {
        $response = new ReportJumpTrackingLogResponse(123, true, '成功消息');

        $this->assertSame(123, $response->id);
        $this->assertTrue($response->success);
        $this->assertSame('成功消息', $response->message);
    }

    /**
     * 测试 success 静态方法
     */
    public function testSuccessFactory(): void
    {
        $response = ReportJumpTrackingLogResponse::success(456, '操作成功');

        $this->assertSame(456, $response->id);
        $this->assertTrue($response->success);
        $this->assertSame('操作成功', $response->message);
    }

    /**
     * 测试 success 静态方法无参数
     */
    public function testSuccessFactoryWithoutParameters(): void
    {
        $response = ReportJumpTrackingLogResponse::success();

        $this->assertNull($response->id);
        $this->assertTrue($response->success);
        $this->assertNull($response->message);
    }

    /**
     * 测试 failure 静态方法
     */
    public function testFailureFactory(): void
    {
        $response = ReportJumpTrackingLogResponse::failure('发生错误');

        $this->assertNull($response->id);
        $this->assertFalse($response->success);
        $this->assertSame('发生错误', $response->message);
    }

    /**
     * 测试 toArray 方法
     */
    public function testToArray(): void
    {
        $response = new ReportJumpTrackingLogResponse(789, true, '测试消息');

        $array = $response->toArray();

        $this->assertArrayHasKey('success', $array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('message', $array);
        $this->assertTrue($array['success']);
        $this->assertSame(789, $array['id']);
        $this->assertSame('测试消息', $array['message']);
    }

    /**
     * 测试 toArray 方法只包含非空值
     */
    public function testToArrayWithNullValues(): void
    {
        $response = new ReportJumpTrackingLogResponse();

        $array = $response->toArray();

        $this->assertArrayHasKey('success', $array);
        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayNotHasKey('message', $array);
    }

    /**
     * 测试 toLegacyArray 方法成功时
     */
    public function testToLegacyArraySuccess(): void
    {
        $response = ReportJumpTrackingLogResponse::success(123);

        $array = $response->toLegacyArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertSame(123, $array['id']);
        $this->assertArrayNotHasKey('success', $array);
    }

    /**
     * 测试 toLegacyArray 方法失败时
     */
    public function testToLegacyArrayFailure(): void
    {
        $response = ReportJumpTrackingLogResponse::failure('错误');

        $array = $response->toLegacyArray();

        $this->assertArrayHasKey('success', $array);
        $this->assertFalse($array['success']);
    }
}
