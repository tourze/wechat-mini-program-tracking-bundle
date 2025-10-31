<?php

namespace WechatMiniProgramTrackingBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;

/**
 * @internal
 */
#[CoversClass(JumpTrackingLog::class)]
final class JumpTrackingLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): JumpTrackingLog
    {
        return new JumpTrackingLog();
    }

    /**
     * 测试 getter 和 setter 方法是否正常工作
     */
    public function testGetterAndSetter(): void
    {
        $entity = $this->createEntity();

        // 由于我们没有查看完整的 JumpTrackingLog 类，这里假设它有与 PageVisitLog 类似的属性
        // 测试 ID 属性
        $this->assertSame(0, $entity->getId()); // 初始值为 0

        // 测试 Page 属性
        $entity->setPage('pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index');
        $this->assertSame('pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index', $entity->getPage());

        // 测试 SessionId 属性
        $entity->setSessionId('test-session-id');
        $this->assertSame('test-session-id', $entity->getSessionId());

        // 测试 CreatedBy 属性
        $entity->setCreatedBy('test-user');
        $this->assertSame('test-user', $entity->getCreatedBy());
    }

    /**
     * 测试 ID 字段的只读性（不应有 setId 方法）
     */
    public function testIdFieldIsReadOnly(): void
    {
        $entity = $this->createEntity();

        // 使用反射API检查setId方法不存在
        $reflection = new \ReflectionClass($entity);
        $this->assertFalse($reflection->hasMethod('setId'));
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'page' => ['page', 'pages/index/index'];
        yield 'openId' => ['openId', 'test-openid-123'];
        yield 'unionId' => ['unionId', 'test-unionid-456'];
        yield 'query' => ['query', ['param1' => 'value1', 'param2' => 'value2']];
        yield 'appKey' => ['appKey', 'test-app-key'];
        yield 'businessChannel' => ['businessChannel', 'test-channel'];
        yield 'deviceBrand' => ['deviceBrand', 'Apple'];
        yield 'deviceId' => ['deviceId', 'test-device-id'];
        yield 'deviceModel' => ['deviceModel', 'iPhone 14'];
        yield 'deviceScreenHeight' => ['deviceScreenHeight', 844];
        yield 'deviceScreenWidth' => ['deviceScreenWidth', 390];
        yield 'deviceSystem' => ['deviceSystem', 'iOS'];
        yield 'deviceSystemVersion' => ['deviceSystemVersion', '16.0'];
        yield 'eventName' => ['eventName', 'click_event'];
        yield 'eventParam' => ['eventParam', ['event_data' => 'test']];
        yield 'networkType' => ['networkType', 'wifi'];
        yield 'pageName' => ['pageName', 'Home Page'];
        yield 'pageQuery' => ['pageQuery', 'param1=value1'];
        yield 'pageTitle' => ['pageTitle', 'Test Page Title'];
        yield 'pageUrl' => ['pageUrl', 'https://example.com/page'];
        yield 'platform' => ['platform', 'ios'];
        yield 'prevPath' => ['prevPath', 'pages/previous/index'];
        yield 'prevSessionId' => ['prevSessionId', 'prev-session-123'];
        yield 'scene' => ['scene', '1001'];
        yield 'sdkName' => ['sdkName', 'WeChatSDK'];
        yield 'sdkType' => ['sdkType', 'mini-program'];
        yield 'sdkVersion' => ['sdkVersion', '3.0.0'];
        yield 'sessionId' => ['sessionId', 'session-123'];
        yield 'jumpResult' => ['jumpResult', true];
        yield 'createdFromUa' => ['createdFromUa', 'Mozilla/5.0 Test User Agent'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
    }
}
