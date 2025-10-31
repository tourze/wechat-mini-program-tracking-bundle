<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\JsonRPCSecurityBundle\JsonRPCSecurityBundle;
use WechatMiniProgramBundle\WechatMiniProgramBundle;

class WechatMiniProgramTrackingBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            WechatMiniProgramBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
            JsonRPCSecurityBundle::class => ['all' => true],
        ];
    }
}
