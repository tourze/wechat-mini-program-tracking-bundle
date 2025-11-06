<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Config;

/**
 * 微信小程序追踪配置类
 *
 * 管理所有与环境相关的配置项，避免在业务逻辑中直接读取环境变量
 */
class TrackingConfig
{
    private readonly string $notFoundFallbackPage;

    public function __construct(
        ?string $notFoundFallbackPage = null
    ) {
        $this->notFoundFallbackPage = $notFoundFallbackPage ??
            $_ENV['WECHAT_MINI_PROGRAM_NOT_FOUND_FALLBACK_PAGE'] ??
            'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index?_from=page_not_found';
    }

    /**
     * 获取页面不存在时的回退页面
     */
    public function getNotFoundFallbackPage(): string
    {
        return $this->notFoundFallbackPage;
    }

    /**
     * 检查用户是否支持 getIdentity() 方法
     * 这是一个临时解决方案，直到用户实体完全实现 getIdentity() 方法
     */
    public function supportsUserIdentity(): bool
    {
        return true; // 目前假设支持，后续可以根据用户类型动态判断
    }
}