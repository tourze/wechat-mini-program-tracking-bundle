<?php

namespace WechatMiniProgramTrackingBundle\Tests\Procedure;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramTrackingBundle\Procedure\ApiReportWeappVisitPage;

class ApiReportWeappVisitPageTest extends TestCase
{
    private ApiReportWeappVisitPage $procedure;

    protected function setUp(): void
    {
        $this->procedure = new ApiReportWeappVisitPage();
    }

    /**
     * 测试 execute 方法返回正确的响应
     */
    public function testExecuteReturnsSuccess(): void
    {
        $result = $this->procedure->execute();
        
        $this->assertArrayHasKey('ok', $result);
        $this->assertEquals(1, $result['ok']);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultPropertyValues(): void
    {
        $this->assertNull($this->procedure->path);
        $this->assertNull($this->procedure->query);
        $this->assertEquals([], $this->procedure->referrerInfo);
        $this->assertEquals(0, $this->procedure->scene);
        $this->assertEquals('', $this->procedure->shareTicket);
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

    /**
     * 测试属性设置
     */
    public function testPropertySetters(): void
    {
        $this->procedure->path = '/pages/index';
        $this->procedure->query = ['id' => 123];
        $this->procedure->referrerInfo = ['appId' => 'wx123'];
        $this->procedure->scene = 1001;
        $this->procedure->shareTicket = 'ticket123';

        $this->assertEquals('/pages/index', $this->procedure->path);
        $this->assertEquals(['id' => 123], $this->procedure->query);
        $this->assertEquals(['appId' => 'wx123'], $this->procedure->referrerInfo);
        $this->assertEquals(1001, $this->procedure->scene);
        $this->assertEquals('ticket123', $this->procedure->shareTicket);
    }
}