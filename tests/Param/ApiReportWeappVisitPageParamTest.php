<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use WechatMiniProgramTrackingBundle\Param\ApiReportWeappVisitPageParam;

/**
 * ApiReportWeappVisitPageParam 单元测试
 *
 * @internal
 */
#[CoversClass(ApiReportWeappVisitPageParam::class)]
final class ApiReportWeappVisitPageParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new ApiReportWeappVisitPageParam(
            path: '/pages/index',
            query: ['id' => '123']
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithRequiredParametersOnly(): void
    {
        $param = new ApiReportWeappVisitPageParam(
            path: '/pages/index',
            query: ['id' => '123']
        );

        $this->assertSame('/pages/index', $param->path);
        $this->assertSame(['id' => '123'], $param->query);
        $this->assertSame([], $param->referrerInfo);
        $this->assertSame(0, $param->scene);
        $this->assertSame('', $param->shareTicket);
    }

    public function testConstructorWithAllParameters(): void
    {
        $param = new ApiReportWeappVisitPageParam(
            path: '/pages/product',
            query: ['id' => '456'],
            referrerInfo: ['appId' => 'test'],
            scene: 1001,
            shareTicket: 'ticket123'
        );

        $this->assertSame('/pages/product', $param->path);
        $this->assertSame(['id' => '456'], $param->query);
        $this->assertSame(['appId' => 'test'], $param->referrerInfo);
        $this->assertSame(1001, $param->scene);
        $this->assertSame('ticket123', $param->shareTicket);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(ApiReportWeappVisitPageParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(ApiReportWeappVisitPageParam::class);

        $properties = ['path', 'query', 'referrerInfo', 'scene', 'shareTicket'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(ApiReportWeappVisitPageParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $attrs = $parameter->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }
}
