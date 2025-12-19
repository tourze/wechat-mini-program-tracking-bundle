<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use WechatMiniProgramTrackingBundle\Param\ApiReportWeappVisitPageParam;
use WechatMiniProgramTrackingBundle\Procedure\ApiReportWeappVisitPage;

/**
 * @internal
 */
#[CoversClass(ApiReportWeappVisitPage::class)]
#[RunTestsInSeparateProcesses]
final class ApiReportWeappVisitPageTest extends AbstractProcedureTestCase
{
    private ApiReportWeappVisitPage $procedure;

    protected function onSetUp(): void
    {
        $this->procedure = self::getService(ApiReportWeappVisitPage::class);
    }

    /**
     * 测试 execute 方法返回正确的响应
     */
    public function testExecuteReturnsSuccess(): void
    {
        $param = new ApiReportWeappVisitPageParam(
            path: '/pages/index',
            query: ['id' => '123']
        );

        $result = $this->procedure->execute($param);

        $this->assertArrayHasKey('ok', $result);
        $this->assertEquals(1, $result['ok']);
    }

    /**
     * 测试带完整参数的 execute
     */
    public function testExecuteWithFullParameters(): void
    {
        $param = new ApiReportWeappVisitPageParam(
            path: '/pages/product',
            query: ['id' => '456'],
            referrerInfo: ['appId' => 'wx123'],
            scene: 1001,
            shareTicket: 'ticket123'
        );

        $result = $this->procedure->execute($param);

        $this->assertArrayHasKey('ok', $result);
        $this->assertEquals(1, $result['ok']);
    }

    /**
     * 测试静态方法 getCategory
     */
    public function testGetCategory(): void
    {
        $category = ApiReportWeappVisitPage::getCategory();
        $this->assertEquals('微信小程序-页面上报', $category);
    }

    /**
     * 测试静态方法 getDesc
     */
    public function testGetDesc(): void
    {
        $desc = ApiReportWeappVisitPage::getDesc();
        $this->assertEquals('小程序启动访问上报接口', $desc);
    }
}
