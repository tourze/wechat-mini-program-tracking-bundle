<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * 跳转追踪日志上报请求 DTO
 *
 * 分离请求数据的验证和转换逻辑
 */
class ReportJumpTrackingLogRequest
{
    #[Assert\Length(max: 255)]
    public ?string $currentPath = null;

    public ?bool $jumpResult = false;

    #[Assert\Length(max: 100)]
    public ?string $deviceBrand = null;

    #[Assert\Length(max: 255)]
    public ?string $deviceId = null;

    #[Assert\Length(max: 255)]
    public ?string $deviceModel = null;

    #[Assert\PositiveOrZero]
    public ?int $deviceScreenHeight = null;

    #[Assert\PositiveOrZero]
    public ?int $deviceScreenWidth = null;

    #[Assert\Length(max: 100)]
    public ?string $deviceSystem = null;

    #[Assert\Length(max: 50)]
    public ?string $deviceSystemVersion = null;

    #[Assert\Length(max: 255)]
    public ?string $eventName = null;

    #[Assert\Valid]
    public ?array $eventParam = null;

    #[Assert\Length(max: 50)]
    public ?string $networkType = null;

    #[Assert\Length(max: 255)]
    public ?string $pageName = null;

    #[Assert\Length(max: 255)]
    public ?string $pageQuery = null;

    #[Assert\Length(max: 255)]
    public ?string $pageTitle = null;

    #[Assert\Url]
    #[Assert\Length(max: 255)]
    public ?string $pageUrl = null;

    #[Assert\Length(max: 50)]
    public ?string $platform = null;

    #[Assert\Length(max: 255)]
    public ?string $prevPath = null;

    #[Assert\Length(max: 255)]
    public ?string $prevSessionId = null;

    #[Assert\Length(max: 255)]
    public ?string $scene = null;

    #[Assert\Length(max: 100)]
    public ?string $sdkName = null;

    #[Assert\Length(max: 50)]
    public ?string $sdkType = null;

    #[Assert\Length(max: 50)]
    public ?string $sdkVersion = null;

    #[Assert\Length(max: 255)]
    public ?string $sessionId = null;

    /**
     * 从 Procedure 属性创建请求 DTO
     *
     * 这个方法充当 Procedure 和 DTO 之间的适配器
     */
    public static function fromProcedure(object $procedure): self
    {
        $request = new self();

        $request->currentPath = $procedure->currentPath ?? null;
        $request->jumpResult = $procedure->jumpResult ?? false;
        $request->deviceBrand = $procedure->deviceBrand ?? null;
        $request->deviceId = $procedure->deviceId ?? null;
        $request->deviceModel = $procedure->deviceModel ?? null;
        $request->deviceScreenHeight = $procedure->deviceScreenHeight ?? null;
        $request->deviceScreenWidth = $procedure->deviceScreenWidth ?? null;
        $request->deviceSystem = $procedure->deviceSystem ?? null;
        $request->deviceSystemVersion = $procedure->deviceSystemVersion ?? null;
        $request->eventName = $procedure->eventName ?? null;
        $request->eventParam = $procedure->eventParam ?? null;
        $request->networkType = $procedure->networkType ?? null;
        $request->pageName = $procedure->pageName ?? null;
        $request->pageQuery = $procedure->pageQuery ?? null;
        $request->pageTitle = $procedure->pageTitle ?? null;
        $request->pageUrl = $procedure->pageUrl ?? null;
        $request->platform = $procedure->platform ?? null;
        $request->prevPath = $procedure->prevPath ?? null;
        $request->prevSessionId = $procedure->prevSessionId ?? null;
        $request->scene = $procedure->scene ?? null;
        $request->sdkName = $procedure->sdkName ?? null;
        $request->sdkType = $procedure->sdkType ?? null;
        $request->sdkVersion = $procedure->sdkVersion ?? null;
        $request->sessionId = $procedure->sessionId ?? null;

        return $request;
    }

    /**
     * 验证请求数据
     *
     * @throws \InvalidArgumentException 当请求数据无效时
     */
    public function validate(): void
    {
        // 这里可以添加更复杂的验证逻辑
        // 目前主要依赖 Symfony Validator 注解
    }
}