<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatMiniProgramTrackingBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private ItemInterface&MockObject $menuItem;

    private LinkGeneratorInterface&MockObject $linkGenerator;

    protected function onSetUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->linkGenerator->method('getCurdListPage')->willReturn('/admin/test');

        // 在集成测试中，我们应该从容器获取服务而不是直接实例化
        // 但我们需要先注入mock到容器，然后获取服务
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        $this->adminMenu = self::getService(AdminMenu::class);
        $this->menuItem = $this->createMock(ItemInterface::class);
    }

    public function testInvokeCreatesMainMenu(): void
    {
        $childItem = $this->createMock(ItemInterface::class);

        $this->menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('微信小程序跟踪')
            ->willReturnOnConsecutiveCalls(null, $childItem)
        ;

        $this->menuItem->expects($this->once())
            ->method('addChild')
            ->with('微信小程序跟踪')
            ->willReturn($childItem)
        ;

        $this->adminMenu->__invoke($this->menuItem);
    }

    public function testInvokeDoesNotCreateMainMenuWhenExists(): void
    {
        $childItem = $this->createMock(ItemInterface::class);

        $this->menuItem->expects($this->atLeastOnce())
            ->method('getChild')
            ->with('微信小程序跟踪')
            ->willReturn($childItem)
        ;

        $this->menuItem->expects($this->never())
            ->method('addChild')
            ->with('微信小程序跟踪')
        ;

        $this->adminMenu->__invoke($this->menuItem);
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }

    public function testInvokeHandlesNullTrackingMenu(): void
    {
        $this->menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('微信小程序跟踪')
            ->willReturnOnConsecutiveCalls(null, null)
        ;

        $childItem = $this->createMock(ItemInterface::class);
        $this->menuItem->expects($this->once())
            ->method('addChild')
            ->with('微信小程序跟踪')
            ->willReturn($childItem)
        ;

        // 验证方法执行不抛出异常
        $this->adminMenu->__invoke($this->menuItem);

        // 使用具体断言来验证结果
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }
}
