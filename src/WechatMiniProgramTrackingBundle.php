<?php

namespace WechatMiniProgramTrackingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class WechatMiniProgramTrackingBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \WechatMiniProgramBundle\WechatMiniProgramBundle::class => ['all' => true],
        ];
    }
}
