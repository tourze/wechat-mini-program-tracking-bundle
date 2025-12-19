<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use WechatMiniProgramTrackingBundle\Param\ReportWechatMiniProgramPageNotFoundParam;

/**
 * ReportWechatMiniProgramPageNotFoundParam 单元测试
 *
 * @internal
 */
#[CoversClass(ReportWechatMiniProgramPageNotFoundParam::class)]
final class ReportWechatMiniProgramPageNotFoundParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new ReportWechatMiniProgramPageNotFoundParam(
            error: ['path' => '/pages/not-found']
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithRequiredParameterOnly(): void
    {
        $param = new ReportWechatMiniProgramPageNotFoundParam(
            error: ['path' => '/pages/not-found', 'message' => 'Page not found']
        );

        $this->assertSame(['path' => '/pages/not-found', 'message' => 'Page not found'], $param->error);
        $this->assertSame([], $param->launchOptions);
        $this->assertSame([], $param->enterOptions);
    }

    public function testConstructorWithAllParameters(): void
    {
        $param = new ReportWechatMiniProgramPageNotFoundParam(
            error: ['path' => '/pages/404'],
            launchOptions: ['scene' => 1001, 'path' => '/pages/index'],
            enterOptions: ['scene' => 1002, 'path' => '/pages/product']
        );

        $this->assertSame(['path' => '/pages/404'], $param->error);
        $this->assertSame(['scene' => 1001, 'path' => '/pages/index'], $param->launchOptions);
        $this->assertSame(['scene' => 1002, 'path' => '/pages/product'], $param->enterOptions);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(ReportWechatMiniProgramPageNotFoundParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(ReportWechatMiniProgramPageNotFoundParam::class);

        $properties = ['error', 'launchOptions', 'enterOptions'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(ReportWechatMiniProgramPageNotFoundParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $attrs = $parameter->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }
}
