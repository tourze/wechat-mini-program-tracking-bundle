<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
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

    protected function onSetUp(): void
    {
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testServiceCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }

    public function testInvokeCreatesMainMenu(): void
    {
        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 第一次调用应该创建菜单
        $this->adminMenu->__invoke($rootItem);

        $this->assertNotNull($rootItem->getChild('微信小程序跟踪'));
    }

    public function testInvokeCreatesSubMenuItems(): void
    {
        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $this->adminMenu->__invoke($rootItem);

        $trackingMenu = $rootItem->getChild('微信小程序跟踪');
        $this->assertNotNull($trackingMenu);

        // 验证子菜单项
        $this->assertNotNull($trackingMenu->getChild('页面访问日志'));
        $this->assertNotNull($trackingMenu->getChild('跳转tracking日志'));
        $this->assertNotNull($trackingMenu->getChild('404页面日志'));
    }

    public function testInvokeDoesNotDuplicateMainMenu(): void
    {
        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 预先创建菜单
        $rootItem->addChild('微信小程序跟踪');

        // 调用应该不会创建重复的主菜单
        $this->adminMenu->__invoke($rootItem);

        // 只有一个同名菜单
        $children = array_filter(
            $rootItem->getChildren(),
            fn ($child) => $child->getName() === '微信小程序跟踪'
        );
        $this->assertCount(1, $children);
    }

    public function testMenuItemsHaveIcons(): void
    {
        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $this->adminMenu->__invoke($rootItem);

        $trackingMenu = $rootItem->getChild('微信小程序跟踪');
        $this->assertNotNull($trackingMenu);
        $this->assertNotEmpty($trackingMenu->getAttribute('icon'));

        // 验证子菜单项的图标
        $pageVisitLog = $trackingMenu->getChild('页面访问日志');
        $this->assertNotNull($pageVisitLog);
        $this->assertNotEmpty($pageVisitLog->getAttribute('icon'));

        $jumpTrackingLog = $trackingMenu->getChild('跳转tracking日志');
        $this->assertNotNull($jumpTrackingLog);
        $this->assertNotEmpty($jumpTrackingLog->getAttribute('icon'));

        $pageNotFoundLog = $trackingMenu->getChild('404页面日志');
        $this->assertNotNull($pageNotFoundLog);
        $this->assertNotEmpty($pageNotFoundLog->getAttribute('icon'));
    }

    public function testMenuItemsHaveUris(): void
    {
        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $this->adminMenu->__invoke($rootItem);

        $trackingMenu = $rootItem->getChild('微信小程序跟踪');
        $this->assertNotNull($trackingMenu);

        // 验证子菜单项有 URI
        $pageVisitLog = $trackingMenu->getChild('页面访问日志');
        $this->assertNotNull($pageVisitLog);
        $this->assertNotNull($pageVisitLog->getUri());

        $jumpTrackingLog = $trackingMenu->getChild('跳转tracking日志');
        $this->assertNotNull($jumpTrackingLog);
        $this->assertNotNull($jumpTrackingLog->getUri());

        $pageNotFoundLog = $trackingMenu->getChild('404页面日志');
        $this->assertNotNull($pageNotFoundLog);
        $this->assertNotNull($pageNotFoundLog->getUri());
    }
}
