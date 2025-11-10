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

    /** @var array<string, mixed>|null */
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

        self::mapProcedureProperties($procedure, $request);

        return $request;
    }

    /**
     * 将 Procedure 属性映射到 DTO
     */
    private static function mapProcedureProperties(object $procedure, self $request): void
    {
        self::mapDeviceProperties($procedure, $request);
        self::mapEventProperties($procedure, $request);
        self::mapPageProperties($procedure, $request);
        self::mapSessionProperties($procedure, $request);
        self::mapSdkProperties($procedure, $request);
    }

    /**
     * 映射设备相关属性
     */
    private static function mapDeviceProperties(object $procedure, self $request): void
    {
        if (property_exists($procedure, 'deviceBrand')) {
            $request->deviceBrand = $procedure->deviceBrand;
        }
        if (property_exists($procedure, 'deviceId')) {
            $request->deviceId = $procedure->deviceId;
        }
        if (property_exists($procedure, 'deviceModel')) {
            $request->deviceModel = $procedure->deviceModel;
        }
        if (property_exists($procedure, 'deviceScreenHeight')) {
            $request->deviceScreenHeight = $procedure->deviceScreenHeight;
        }
        if (property_exists($procedure, 'deviceScreenWidth')) {
            $request->deviceScreenWidth = $procedure->deviceScreenWidth;
        }
        if (property_exists($procedure, 'deviceSystem')) {
            $request->deviceSystem = $procedure->deviceSystem;
        }
        if (property_exists($procedure, 'deviceSystemVersion')) {
            $request->deviceSystemVersion = $procedure->deviceSystemVersion;
        }
        if (property_exists($procedure, 'networkType')) {
            $request->networkType = $procedure->networkType;
        }
    }

    /**
     * 映射事件相关属性
     */
    private static function mapEventProperties(object $procedure, self $request): void
    {
        if (property_exists($procedure, 'eventName')) {
            $request->eventName = $procedure->eventName;
        }
        if (property_exists($procedure, 'eventParam')) {
            $request->eventParam = $procedure->eventParam;
        }
    }

    /**
     * 映射页面相关属性
     */
    private static function mapPageProperties(object $procedure, self $request): void
    {
        if (property_exists($procedure, 'currentPath')) {
            $request->currentPath = $procedure->currentPath;
        }
        if (property_exists($procedure, 'jumpResult')) {
            $request->jumpResult = $procedure->jumpResult ?? false;
        }
        if (property_exists($procedure, 'pageName')) {
            $request->pageName = $procedure->pageName;
        }
        if (property_exists($procedure, 'pageQuery')) {
            $request->pageQuery = $procedure->pageQuery;
        }
        if (property_exists($procedure, 'pageTitle')) {
            $request->pageTitle = $procedure->pageTitle;
        }
        if (property_exists($procedure, 'pageUrl')) {
            $request->pageUrl = $procedure->pageUrl;
        }
        if (property_exists($procedure, 'platform')) {
            $request->platform = $procedure->platform;
        }
        if (property_exists($procedure, 'prevPath')) {
            $request->prevPath = $procedure->prevPath;
        }
        if (property_exists($procedure, 'scene')) {
            $request->scene = $procedure->scene;
        }
    }

    /**
     * 映射会话相关属性
     */
    private static function mapSessionProperties(object $procedure, self $request): void
    {
        if (property_exists($procedure, 'sessionId')) {
            $request->sessionId = $procedure->sessionId;
        }
        if (property_exists($procedure, 'prevSessionId')) {
            $request->prevSessionId = $procedure->prevSessionId;
        }
    }

    /**
     * 映射 SDK 相关属性
     */
    private static function mapSdkProperties(object $procedure, self $request): void
    {
        if (property_exists($procedure, 'sdkName')) {
            $request->sdkName = $procedure->sdkName;
        }
        if (property_exists($procedure, 'sdkType')) {
            $request->sdkType = $procedure->sdkType;
        }
        if (property_exists($procedure, 'sdkVersion')) {
            $request->sdkVersion = $procedure->sdkVersion;
        }
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