<?php

namespace WechatMiniProgramTrackingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '微信tracking模块')]
class WechatMiniProgramTrackingBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \WechatMiniProgramBundle\WechatMiniProgramBundle::class => ['all' => true],
        ];
    }
}
