<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatMiniProgramTrackingBundle\Controller\Admin\PageVisitLogCrudController;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;

/**
 * @internal
 */
#[CoversClass(PageVisitLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class PageVisitLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): PageVisitLogCrudController
    {
        return new PageVisitLogCrudController();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield '页面路径' => ['页面路径'];
        yield '路由ID' => ['路由ID'];
        yield '会话ID' => ['会话ID'];
        yield '创建时间' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        // NEW action is disabled for log controllers - return dummy data to prevent empty dataset error
        yield 'dummy' => ['dummy'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        // EDIT action is disabled for log controllers - return dummy data to prevent empty dataset error
        yield 'dummy' => ['dummy'];
    }
}
