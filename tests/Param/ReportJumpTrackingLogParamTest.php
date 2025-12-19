<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use WechatMiniProgramTrackingBundle\Param\ReportJumpTrackingLogParam;

/**
 * ReportJumpTrackingLogParam 单元测试
 *
 * @internal
 */
#[CoversClass(ReportJumpTrackingLogParam::class)]
final class ReportJumpTrackingLogParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new ReportJumpTrackingLogParam();

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithDefaultValues(): void
    {
        $param = new ReportJumpTrackingLogParam();

        $this->assertNull($param->currentPath);
        $this->assertFalse($param->jumpResult);
        $this->assertNull($param->deviceBrand);
        $this->assertNull($param->deviceId);
        $this->assertNull($param->deviceModel);
        $this->assertNull($param->deviceScreenHeight);
        $this->assertNull($param->deviceScreenWidth);
        $this->assertNull($param->deviceSystem);
        $this->assertNull($param->deviceSystemVersion);
        $this->assertNull($param->eventName);
        $this->assertNull($param->eventParam);
        $this->assertNull($param->networkType);
        $this->assertNull($param->pageName);
        $this->assertNull($param->pageQuery);
        $this->assertNull($param->pageTitle);
        $this->assertNull($param->pageUrl);
        $this->assertNull($param->platform);
        $this->assertNull($param->prevPath);
        $this->assertNull($param->prevSessionId);
        $this->assertNull($param->scene);
        $this->assertNull($param->sdkName);
        $this->assertNull($param->sdkType);
        $this->assertNull($param->sdkVersion);
        $this->assertNull($param->sessionId);
    }

    public function testConstructorWithAllParameters(): void
    {
        $param = new ReportJumpTrackingLogParam(
            currentPath: '/pages/current',
            jumpResult: true,
            deviceBrand: 'Apple',
            deviceId: 'device-123',
            deviceModel: 'iPhone 12',
            deviceScreenHeight: 844,
            deviceScreenWidth: 390,
            deviceSystem: 'iOS',
            deviceSystemVersion: '14.0',
            eventName: 'page_view',
            eventParam: ['key' => 'value'],
            networkType: 'wifi',
            pageName: 'TestPage',
            pageQuery: 'id=123',
            pageTitle: 'Test Title',
            pageUrl: 'https://example.com',
            platform: 'weapp',
            prevPath: '/pages/prev',
            prevSessionId: 'session-456',
            scene: 'scene-789',
            sdkName: 'test-sdk',
            sdkType: 'weapp',
            sdkVersion: '1.0.0',
            sessionId: 'session-123'
        );

        $this->assertSame('/pages/current', $param->currentPath);
        $this->assertTrue($param->jumpResult);
        $this->assertSame('Apple', $param->deviceBrand);
        $this->assertSame('device-123', $param->deviceId);
        $this->assertSame('iPhone 12', $param->deviceModel);
        $this->assertSame(844, $param->deviceScreenHeight);
        $this->assertSame(390, $param->deviceScreenWidth);
        $this->assertSame('iOS', $param->deviceSystem);
        $this->assertSame('14.0', $param->deviceSystemVersion);
        $this->assertSame('page_view', $param->eventName);
        $this->assertSame(['key' => 'value'], $param->eventParam);
        $this->assertSame('wifi', $param->networkType);
        $this->assertSame('TestPage', $param->pageName);
        $this->assertSame('id=123', $param->pageQuery);
        $this->assertSame('Test Title', $param->pageTitle);
        $this->assertSame('https://example.com', $param->pageUrl);
        $this->assertSame('weapp', $param->platform);
        $this->assertSame('/pages/prev', $param->prevPath);
        $this->assertSame('session-456', $param->prevSessionId);
        $this->assertSame('scene-789', $param->scene);
        $this->assertSame('test-sdk', $param->sdkName);
        $this->assertSame('weapp', $param->sdkType);
        $this->assertSame('1.0.0', $param->sdkVersion);
        $this->assertSame('session-123', $param->sessionId);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(ReportJumpTrackingLogParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(ReportJumpTrackingLogParam::class);

        $properties = [
            'currentPath', 'jumpResult', 'deviceBrand', 'deviceId', 'deviceModel',
            'deviceScreenHeight', 'deviceScreenWidth', 'deviceSystem', 'deviceSystemVersion',
            'eventName', 'eventParam', 'networkType', 'pageName', 'pageQuery', 'pageTitle',
            'pageUrl', 'platform', 'prevPath', 'prevSessionId', 'scene', 'sdkName',
            'sdkType', 'sdkVersion', 'sessionId'
        ];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(ReportJumpTrackingLogParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $attrs = $parameter->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }
}
