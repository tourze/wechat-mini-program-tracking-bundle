<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatMiniProgramTrackingBundle\WechatMiniProgramTrackingBundle;

/**
 * @internal
 */
#[CoversClass(WechatMiniProgramTrackingBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatMiniProgramTrackingBundleTest extends AbstractBundleTestCase
{
}
