<?php

namespace WechatMiniProgramTrackingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;
use WechatMiniProgramTrackingBundle\Repository\JumpTrackingLogRepository;

#[AsScheduleClean(expression: '23 4 * * *', defaultKeepDay: 30, keepDayEnv: 'JUMP_TRACKING_LOG_PERSIST_DAY_NUM')]
#[ORM\Entity(repositoryClass: JumpTrackingLogRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_jump_tracking_log', options: ['comment' => '跳转tracking日志'])]
#[ORM\Index(columns: ['page'], name: 'idx_page')]
class JumpTrackingLog implements Stringable
{
    use CreatedByAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    private string $page;

    private ?string $openId = null;

    private ?string $unionId = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数'])]
    private ?array $query = null;

#[ORM\Column(length: 100, nullable: true, options: ['comment' => '字段说明'])]
    private ?string $appKey = null;

    // 添加 businessChannel 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '业务渠道'])]
    private ?string $businessChannel = null;

    // 添加 deviceBrand 变量
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '设备品牌'])]
    private ?string $deviceBrand = null;

    // 添加 deviceId 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '设备ID'])]
    private ?string $deviceId = null;

    // 添加 deviceModel 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '设备型号'])]
    private ?string $deviceModel = null;

    // 添加 deviceScreenHeight 变量
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '设备屏幕高度'])]
    private ?int $deviceScreenHeight = null;

    // 添加 deviceScreenWidth 变量
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '设备屏幕宽度'])]
    private ?int $deviceScreenWidth = null;

    // 添加 deviceSystem 变量
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '设备系统'])]
    private ?string $deviceSystem = null;

    // 添加 deviceSystemVersion 变量
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '设备系统版本'])]
    private ?string $deviceSystemVersion = null;

    // 添加 eventName 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '事件名称'])]
    private ?string $eventName = null;

    // 添加 eventParam 变量
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '事件参数'])]
    private ?array $eventParam = null;

    // 添加 networkType 变量
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '网络类型'])]
    private ?string $networkType = null;

    // 添加 pageName 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '页面名称'])]
    private ?string $pageName = null;

    // 添加 pageQuery 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '页面查询参数'])]
    private ?string $pageQuery = null;

    // 添加 pageTitle 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '页面标题'])]
    private ?string $pageTitle = null;

    // 添加 pageUrl 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '页面URL'])]
    private ?string $pageUrl = null;

    // 添加 platform 变量
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '平台'])]
    private ?string $platform = null;

    // 添加 prevPath 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '前一个路径'])]
    private ?string $prevPath = null;

    // 添加 prevSessionId 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '前一个会话ID'])]
    private ?string $prevSessionId = null;

    // 添加 scene 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '场景'])]
    private ?string $scene = null;

    // 添加 sdkName 变量
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => 'SDK名称'])]
    private ?string $sdkName = null;

    // 添加 sdkType 变量
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => 'SDK类型'])]
    private ?string $sdkType = null;

    // 添加 sdkVersion 变量
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => 'SDK版本'])]
    private ?string $sdkVersion = null;

    // 添加 sessionId 变量
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '会话ID'])]
    private ?string $sessionId = null;

    #[ORM\Column(options: ['comment' => '跳转结果'])]
    private ?bool $jumpResult = null;

    #[CreateUserAgentColumn]
    private ?string $createdFromUa = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeImmutable $createTime = null;

    public function getPage(): string
    {
        return $this->page;
    }

    public function setPage(string $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(?string $openId): self
    {
        $this->openId = $openId;

        return $this;
    }

    public function getUnionId(): ?string
    {
        return $this->unionId;
    }

    public function setUnionId(?string $unionId): static
    {
        $this->unionId = $unionId;

        return $this;
    }

    public function getQuery(): ?array
    {
        return $this->query;
    }

    public function setQuery(?array $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function getAppKey(): ?string
    {
        return $this->appKey;
    }

    public function setAppKey(?string $appKey): static
    {
        $this->appKey = $appKey;

        return $this;
    }

    // 添加 getBusinessChannel 方法
    public function getBusinessChannel(): ?string
    {
        return $this->businessChannel;
    }

    // 添加 setBusinessChannel 方法
    public function setBusinessChannel(?string $businessChannel): static
    {
        $this->businessChannel = $businessChannel;

        return $this;
    }

    // 添加 getDeviceBrand 方法
    public function getDeviceBrand(): ?string
    {
        return $this->deviceBrand;
    }

    // 添加 setDeviceBrand 方法
    public function setDeviceBrand(?string $deviceBrand): static
    {
        $this->deviceBrand = $deviceBrand;

        return $this;
    }

    // 添加 getDeviceId 方法
    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    // 添加 setDeviceId 方法
    public function setDeviceId(?string $deviceId): static
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    // 添加 getDeviceModel 方法
    public function getDeviceModel(): ?string
    {
        return $this->deviceModel;
    }

    // 添加 setDeviceModel 方法
    public function setDeviceModel(?string $deviceModel): static
    {
        $this->deviceModel = $deviceModel;

        return $this;
    }

    // 添加 getDeviceScreenHeight 方法
    public function getDeviceScreenHeight(): ?int
    {
        return $this->deviceScreenHeight;
    }

    // 添加 setDeviceScreenHeight 方法
    public function setDeviceScreenHeight(?int $deviceScreenHeight): static
    {
        $this->deviceScreenHeight = $deviceScreenHeight;

        return $this;
    }

    // 添加 getDeviceScreenWidth 方法
    public function getDeviceScreenWidth(): ?int
    {
        return $this->deviceScreenWidth;
    }

    // 添加 setDeviceScreenWidth 方法
    public function setDeviceScreenWidth(?int $deviceScreenWidth): static
    {
        $this->deviceScreenWidth = $deviceScreenWidth;

        return $this;
    }

    // 添加 getDeviceSystem 方法
    public function getDeviceSystem(): ?string
    {
        return $this->deviceSystem;
    }

    // 添加 setDeviceSystem 方法
    public function setDeviceSystem(?string $deviceSystem): static
    {
        $this->deviceSystem = $deviceSystem;

        return $this;
    }

    // 添加 getDeviceSystemVersion 方法
    public function getDeviceSystemVersion(): ?string
    {
        return $this->deviceSystemVersion;
    }

    // 添加 setDeviceSystemVersion 方法
    public function setDeviceSystemVersion(?string $deviceSystemVersion): static
    {
        $this->deviceSystemVersion = $deviceSystemVersion;

        return $this;
    }

    // 添加 getEventName 方法
    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    // 添加 setEventName 方法
    public function setEventName(?string $eventName): static
    {
        $this->eventName = $eventName;

        return $this;
    }

    // 添加 getEventParam 方法
    public function getEventParam(): ?array
    {
        return $this->eventParam;
    }

    // 添加 setEventParam 方法
    public function setEventParam(?array $eventParam): static
    {
        $this->eventParam = $eventParam;

        return $this;
    }

    // 添加 getNetworkType 方法
    public function getNetworkType(): ?string
    {
        return $this->networkType;
    }

    // 添加 setNetworkType 方法
    public function setNetworkType(?string $networkType): static
    {
        $this->networkType = $networkType;

        return $this;
    }

    // 添加 getPageName 方法
    public function getPageName(): ?string
    {
        return $this->pageName;
    }

    // 添加 setPageName 方法
    public function setPageName(?string $pageName): static
    {
        $this->pageName = $pageName;

        return $this;
    }

    // 添加 getPageQuery 方法
    public function getPageQuery(): ?string
    {
        return $this->pageQuery;
    }

    // 添加 setPageQuery 方法
    public function setPageQuery(?string $pageQuery): static
    {
        $this->pageQuery = $pageQuery;

        return $this;
    }

    // 添加 getPageTitle 方法
    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    // 添加 setPageTitle 方法
    public function setPageTitle(?string $pageTitle): static
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    // 添加 getPageUrl 方法
    public function getPageUrl(): ?string
    {
        return $this->pageUrl;
    }

    // 添加 setPageUrl 方法
    public function setPageUrl(?string $pageUrl): static
    {
        $this->pageUrl = $pageUrl;

        return $this;
    }

    // 添加 getPlatform 方法
    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    // 添加 setPlatform 方法
    public function setPlatform(?string $platform): static
    {
        $this->platform = $platform;

        return $this;
    }

    // 添加 getPrevPath 方法
    public function getPrevPath(): ?string
    {
        return $this->prevPath;
    }

    // 添加 setPrevPath 方法
    public function setPrevPath(?string $prevPath): static
    {
        $this->prevPath = $prevPath;

        return $this;
    }

    // 添加 getPrevSessionId 方法
    public function getPrevSessionId(): ?string
    {
        return $this->prevSessionId;
    }

    // 添加 setPrevSessionId 方法
    public function setPrevSessionId(?string $prevSessionId): static
    {
        $this->prevSessionId = $prevSessionId;

        return $this;
    }

    // 添加 getScene 方法
    public function getScene(): ?string
    {
        return $this->scene;
    }

    // 添加 setScene 方法
    public function setScene(?string $scene): static
    {
        $this->scene = $scene;

        return $this;
    }

    // 添加 getSdkName 方法
    public function getSdkName(): ?string
    {
        return $this->sdkName;
    }

    // 添加 setSdkName 方法
    public function setSdkName(?string $sdkName): static
    {
        $this->sdkName = $sdkName;

        return $this;
    }

    // 添加 getSdkType 方法
    public function getSdkType(): ?string
    {
        return $this->sdkType;
    }

    // 添加 setSdkType 方法
    public function setSdkType(?string $sdkType): static
    {
        $this->sdkType = $sdkType;

        return $this;
    }

    // 添加 getSdkVersion 方法
    public function getSdkVersion(): ?string
    {
        return $this->sdkVersion;
    }

    // 添加 setSdkVersion 方法
    public function setSdkVersion(?string $sdkVersion): static
    {
        $this->sdkVersion = $sdkVersion;

        return $this;
    }

    // 添加 getSessionId 方法
    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    // 添加 setSessionId 方法
    public function setSessionId(?string $sessionId): static
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function isJumpResult(): ?bool
    {
        return $this->jumpResult;
    }

    public function setJumpResult(bool $jumpResult): static
    {
        $this->jumpResult = $jumpResult;

        return $this;
    }

    public function getCreatedFromUa(): ?string
    {
        return $this->createdFromUa;
    }

    public function setCreatedFromUa(?string $createdFromUa): void
    {
        $this->createdFromUa = $createdFromUa;
    }

    public function getCreateTime(): ?\DateTimeImmutableImmutable
    {
        return $this->createTime;
    }

    public function setCreateTime(?\DateTimeImmutable $createTime): void
    {
        $this->createTime = $createTime;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}