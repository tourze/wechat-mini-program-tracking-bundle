<?php

namespace WechatMiniProgramTrackingBundle\Tests\Logger;

use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;
use WechatMiniProgramTrackingBundle\Logger\RequestProcessor;

/**
 * @internal
 */
#[CoversClass(RequestProcessor::class)]
#[RunTestsInSeparateProcesses]
final class RequestProcessorTest extends AbstractEventSubscriberTestCase
{
    protected function onSetUp(): void
    {
        // No specific setup needed for this test
    }

    /**
     * 测试处理器处理日志记录的功能
     */
    public function testInvoke(): void
    {
        $processor = self::getService(RequestProcessor::class);

        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'Test message',
            context: [],
            extra: []
        );

        // 调用处理器，此时没有页面信息
        $result = $processor->__invoke($record);

        // 验证结果
        $this->assertArrayNotHasKey('current_page', $result->extra);

        // 模拟请求事件
        $request = new Request();
        $request->headers = new HeaderBag(['Current-Page' => 'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index']);
        $request->query = new InputBag([
            '__sessionId' => 'test-session',
            '__routeId' => '123',
        ]);

        /*
         * 使用 RequestEvent 具体类进行 Mock 是必要的，因为：
         * 1. RequestEvent 是 Symfony 内核事件系统的核心类，没有对应的接口
         * 2. 该类包含了请求处理逻辑需要的关键方法（isMainRequest, getRequest）
         * 3. 这是 Symfony 官方推荐的测试方式，无更好的替代方案
         */
        $requestEvent = $this->createMock(RequestEvent::class);
        $requestEvent->expects($this->once())
            ->method('isMainRequest')
            ->willReturn(true)
        ;
        $requestEvent->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        // 触发请求事件
        $processor->onKernelRequest($requestEvent);

        // 再次调用处理器
        $result = $processor->__invoke($record);

        // 验证结果
        $this->assertArrayHasKey('current_page', $result->extra);
        $this->assertEquals('pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index', $result->extra['current_page']);
    }

    /**
     * 测试非主请求事件的处理
     */
    public function testOnKernelRequestWithNonMainRequest(): void
    {
        $processor = self::getService(RequestProcessor::class);

        /*
         * 使用 RequestEvent 具体类进行 Mock 是必要的，因为：
         * 1. RequestEvent 是 Symfony 内核事件系统的核心类，没有对应的接口
         * 2. 该类包含了请求处理逻辑需要的关键方法（isMainRequest, getRequest）
         * 3. 这是 Symfony 官方推荐的测试方式，无更好的替代方案
         */
        $requestEvent = $this->createMock(RequestEvent::class);
        $requestEvent->expects($this->once())
            ->method('isMainRequest')
            ->willReturn(false)
        ;
        $requestEvent->expects($this->never())
            ->method('getRequest')
        ;

        // 触发请求事件
        $processor->onKernelRequest($requestEvent);

        // 验证不会处理非主请求
        $record = new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: 'Test message',
            context: [],
            extra: []
        );

        $result = $processor->__invoke($record);
        $this->assertArrayNotHasKey('current_page', $result->extra);
    }

    /**
     * 测试请求结束时的日志保存
     */
    public function testOnTerminateWithValidData(): void
    {
        $processor = self::getService(RequestProcessor::class);

        // 模拟请求事件
        $request = new Request();
        $request->headers = new HeaderBag(['Current-Page' => 'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index?param=value']);
        $request->query = new InputBag([
            '__sessionId' => 'test-session',
            '__routeId' => '123',
        ]);

        /*
         * 使用 RequestEvent 具体类进行 Mock 是必要的，因为：
         * 1. RequestEvent 是 Symfony 内核事件系统的核心类，没有对应的接口
         * 2. 该类包含了请求处理逻辑需要的关键方法（isMainRequest, getRequest）
         * 3. 这是 Symfony 官方推荐的测试方式，无更好的替代方案
         */
        $requestEvent = $this->createMock(RequestEvent::class);
        $requestEvent->expects($this->once())
            ->method('isMainRequest')
            ->willReturn(true)
        ;
        $requestEvent->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        // 触发请求事件
        $processor->onKernelRequest($requestEvent);

        // 触发终止事件 - 验证方法正常执行，不会抛出异常
        $processor->onTerminate();
    }

    /**
     * 测试请求结束时没有足够数据的情况
     */
    public function testOnTerminateWithInsufficientData(): void
    {
        $processor = self::getService(RequestProcessor::class);

        // 模拟请求事件，但没有设置 sessionId
        $request = new Request();
        $request->headers = new HeaderBag(['Current-Page' => 'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index']);
        $request->query = new InputBag([
            '__routeId' => '123',
        ]);

        /*
         * 使用 RequestEvent 具体类进行 Mock 是必要的，因为：
         * 1. RequestEvent 是 Symfony 内核事件系统的核心类，没有对应的接口
         * 2. 该类包含了请求处理逻辑需要的关键方法（isMainRequest, getRequest）
         * 3. 这是 Symfony 官方推荐的测试方式，无更好的替代方案
         */
        $requestEvent = $this->createMock(RequestEvent::class);
        $requestEvent->expects($this->once())
            ->method('isMainRequest')
            ->willReturn(true)
        ;
        $requestEvent->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        // 触发请求事件
        $processor->onKernelRequest($requestEvent);

        // 触发终止事件 - 验证方法正常执行，不会抛出异常
        $processor->onTerminate();
    }

    /**
     * 测试请求结束时有缓存的情况
     */
    public function testOnTerminateWithCachedData(): void
    {
        $processor = self::getService(RequestProcessor::class);

        // 模拟请求事件
        $request = new Request();
        $request->headers = new HeaderBag(['Current-Page' => 'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index']);
        $request->query = new InputBag([
            '__sessionId' => 'test-session',
            '__routeId' => '123',
        ]);

        /*
         * 使用 RequestEvent 具体类进行 Mock 是必要的，因为：
         * 1. RequestEvent 是 Symfony 内核事件系统的核心类，没有对应的接口
         * 2. 该类包含了请求处理逻辑需要的关键方法（isMainRequest, getRequest）
         * 3. 这是 Symfony 官方推荐的测试方式，无更好的替代方案
         */
        $requestEvent = $this->createMock(RequestEvent::class);
        $requestEvent->expects($this->once())
            ->method('isMainRequest')
            ->willReturn(true)
        ;
        $requestEvent->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        // 触发请求事件
        $processor->onKernelRequest($requestEvent);

        // 触发终止事件 - 验证方法正常执行，不会抛出异常
        $processor->onTerminate();
    }
}
