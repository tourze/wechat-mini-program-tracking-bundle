<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatMiniProgramTrackingBundle\Controller\Admin\PageNotFoundLogCrudController;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;

/**
 * @internal
 */
#[CoversClass(PageNotFoundLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class PageNotFoundLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): PageNotFoundLogCrudController
    {
        return new PageNotFoundLogCrudController();
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(PageNotFoundLog::class, PageNotFoundLogCrudController::getEntityFqcn());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield '用户账号' => ['用户账号'];
        yield '路径' => ['路径'];
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
