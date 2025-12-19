<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;
use WechatMiniProgramTrackingBundle\Repository\JumpTrackingLogRepository;

#[AsScheduleClean(expression: '23 4 * * *', defaultKeepDay: 30, keepDayEnv: 'JUMP_TRACKING_LOG_PERSIST_DAY_NUM')]
#[ORM\Entity(repositoryClass: JumpTrackingLogRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_jump_tracking_log', options: ['comment' => '跳转tracking日志'])]
class JumpTrackingLog implements \Stringable
{
    use CreatedByAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    #[Assert\Length(max: 255)]
    #[IndexColumn]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '页面路径'])]
    private ?string $page = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => 'OpenID'])]
    private ?string $openId = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => 'UnionID'])]
    private ?string $unionId = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Valid]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数'])]
    private ?array $query = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '字段说明'])]
    private ?string $appKey = null;

    // 添加 businessChannel 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '业务渠道'])]
    private ?string $businessChannel = null;

    // 添加 deviceBrand 变量
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '设备品牌'])]
    private ?string $deviceBrand = null;

    // 添加 deviceId 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '设备ID'])]
    private ?string $deviceId = null;

    // 添加 deviceModel 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '设备型号'])]
    private ?string $deviceModel = null;

    // 添加 deviceScreenHeight 变量
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '设备屏幕高度'])]
    private ?int $deviceScreenHeight = null;

    // 添加 deviceScreenWidth 变量
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '设备屏幕宽度'])]
    private ?int $deviceScreenWidth = null;

    // 添加 deviceSystem 变量
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '设备系统'])]
    private ?string $deviceSystem = null;

    // 添加 deviceSystemVersion 变量
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '设备系统版本'])]
    private ?string $deviceSystemVersion = null;

    // 添加 eventName 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '事件名称'])]
    private ?string $eventName = null;

    // 添加 eventParam 变量
    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Valid]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '事件参数'])]
    private ?array $eventParam = null;

    // 添加 networkType 变量
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '网络类型'])]
    private ?string $networkType = null;

    // 添加 pageName 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '页面名称'])]
    private ?string $pageName = null;

    // 添加 pageQuery 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '页面查询参数'])]
    private ?string $pageQuery = null;

    // 添加 pageTitle 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '页面标题'])]
    private ?string $pageTitle = null;

    // 添加 pageUrl 变量
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '页面URL'])]
    private ?string $pageUrl = null;

    // 添加 platform 变量
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '平台'])]
    private ?string $platform = null;

    // 添加 prevPath 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '前一个路径'])]
    private ?string $prevPath = null;

    // 添加 prevSessionId 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '前一个会话ID'])]
    private ?string $prevSessionId = null;

    // 添加 scene 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '场景'])]
    private ?string $scene = null;

    // 添加 sdkName 变量
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => 'SDK名称'])]
    private ?string $sdkName = null;

    // 添加 sdkType 变量
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => 'SDK类型'])]
    private ?string $sdkType = null;

    // 添加 sdkVersion 变量
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => 'SDK版本'])]
    private ?string $sdkVersion = null;

    // 添加 sessionId 变量
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '会话ID'])]
    private ?string $sessionId = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(options: ['comment' => '跳转结果'])]
    private ?bool $jumpResult = null;

    #[Assert\Length(max: 500)]
    #[CreateUserAgentColumn]
    private ?string $createdFromUa = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeImmutable $createTime = null;

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(?string $page): void
    {
        $this->page = $page;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(?string $openId): void
    {
        $this->openId = $openId;
    }

    public function getUnionId(): ?string
    {
        return $this->unionId;
    }

    public function setUnionId(?string $unionId): void
    {
        $this->unionId = $unionId;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getQuery(): ?array
    {
        return $this->query;
    }

    public function getQueryAsJson(): string
    {
        if (null === $this->query || [] === $this->query) {
            return '';
        }

        $encoded = json_encode($this->query, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return false === $encoded ? '' : $encoded;
    }

    /**
     * @param array<string, mixed>|null $query
     */
    public function setQuery(?array $query): void
    {
        $this->query = $query;
    }

    public function getAppKey(): ?string
    {
        return $this->appKey;
    }

    public function setAppKey(?string $appKey): void
    {
        $this->appKey = $appKey;
    }

    // 添加 getBusinessChannel 方法
    public function getBusinessChannel(): ?string
    {
        return $this->businessChannel;
    }

    // 添加 setBusinessChannel 方法
    public function setBusinessChannel(?string $businessChannel): void
    {
        $this->businessChannel = $businessChannel;
    }

    // 添加 getDeviceBrand 方法
    public function getDeviceBrand(): ?string
    {
        return $this->deviceBrand;
    }

    // 添加 setDeviceBrand 方法
    public function setDeviceBrand(?string $deviceBrand): void
    {
        $this->deviceBrand = $deviceBrand;
    }

    // 添加 getDeviceId 方法
    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    // 添加 setDeviceId 方法
    public function setDeviceId(?string $deviceId): void
    {
        $this->deviceId = $deviceId;
    }

    // 添加 getDeviceModel 方法
    public function getDeviceModel(): ?string
    {
        return $this->deviceModel;
    }

    // 添加 setDeviceModel 方法
    public function setDeviceModel(?string $deviceModel): void
    {
        $this->deviceModel = $deviceModel;
    }

    // 添加 getDeviceScreenHeight 方法
    public function getDeviceScreenHeight(): ?int
    {
        return $this->deviceScreenHeight;
    }

    // 添加 setDeviceScreenHeight 方法
    public function setDeviceScreenHeight(?int $deviceScreenHeight): void
    {
        $this->deviceScreenHeight = $deviceScreenHeight;
    }

    // 添加 getDeviceScreenWidth 方法
    public function getDeviceScreenWidth(): ?int
    {
        return $this->deviceScreenWidth;
    }

    // 添加 setDeviceScreenWidth 方法
    public function setDeviceScreenWidth(?int $deviceScreenWidth): void
    {
        $this->deviceScreenWidth = $deviceScreenWidth;
    }

    // 添加 getDeviceSystem 方法
    public function getDeviceSystem(): ?string
    {
        return $this->deviceSystem;
    }

    // 添加 setDeviceSystem 方法
    public function setDeviceSystem(?string $deviceSystem): void
    {
        $this->deviceSystem = $deviceSystem;
    }

    // 添加 getDeviceSystemVersion 方法
    public function getDeviceSystemVersion(): ?string
    {
        return $this->deviceSystemVersion;
    }

    // 添加 setDeviceSystemVersion 方法
    public function setDeviceSystemVersion(?string $deviceSystemVersion): void
    {
        $this->deviceSystemVersion = $deviceSystemVersion;
    }

    // 添加 getEventName 方法
    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    // 添加 setEventName 方法
    public function setEventName(?string $eventName): void
    {
        $this->eventName = $eventName;
    }

    // 添加 getEventParam 方法
    /**
     * @return array<string, mixed>|null
     */
    public function getEventParam(): ?array
    {
        return $this->eventParam;
    }

    public function getEventParamAsJson(): string
    {
        if (null === $this->eventParam || [] === $this->eventParam) {
            return '';
        }

        $encoded = json_encode($this->eventParam, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return false === $encoded ? '' : $encoded;
    }

    // 添加 setEventParam 方法
    /**
     * @param array<string, mixed>|null $eventParam
     */
    public function setEventParam(?array $eventParam): void
    {
        $this->eventParam = $eventParam;
    }

    // 添加 getNetworkType 方法
    public function getNetworkType(): ?string
    {
        return $this->networkType;
    }

    // 添加 setNetworkType 方法
    public function setNetworkType(?string $networkType): void
    {
        $this->networkType = $networkType;
    }

    // 添加 getPageName 方法
    public function getPageName(): ?string
    {
        return $this->pageName;
    }

    // 添加 setPageName 方法
    public function setPageName(?string $pageName): void
    {
        $this->pageName = $pageName;
    }

    // 添加 getPageQuery 方法
    public function getPageQuery(): ?string
    {
        return $this->pageQuery;
    }

    // 添加 setPageQuery 方法
    public function setPageQuery(?string $pageQuery): void
    {
        $this->pageQuery = $pageQuery;
    }

    // 添加 getPageTitle 方法
    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    // 添加 setPageTitle 方法
    public function setPageTitle(?string $pageTitle): void
    {
        $this->pageTitle = $pageTitle;
    }

    // 添加 getPageUrl 方法
    public function getPageUrl(): ?string
    {
        return $this->pageUrl;
    }

    // 添加 setPageUrl 方法
    public function setPageUrl(?string $pageUrl): void
    {
        $this->pageUrl = $pageUrl;
    }

    // 添加 getPlatform 方法
    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    // 添加 setPlatform 方法
    public function setPlatform(?string $platform): void
    {
        $this->platform = $platform;
    }

    // 添加 getPrevPath 方法
    public function getPrevPath(): ?string
    {
        return $this->prevPath;
    }

    // 添加 setPrevPath 方法
    public function setPrevPath(?string $prevPath): void
    {
        $this->prevPath = $prevPath;
    }

    // 添加 getPrevSessionId 方法
    public function getPrevSessionId(): ?string
    {
        return $this->prevSessionId;
    }

    // 添加 setPrevSessionId 方法
    public function setPrevSessionId(?string $prevSessionId): void
    {
        $this->prevSessionId = $prevSessionId;
    }

    // 添加 getScene 方法
    public function getScene(): ?string
    {
        return $this->scene;
    }

    // 添加 setScene 方法
    public function setScene(?string $scene): void
    {
        $this->scene = $scene;
    }

    // 添加 getSdkName 方法
    public function getSdkName(): ?string
    {
        return $this->sdkName;
    }

    // 添加 setSdkName 方法
    public function setSdkName(?string $sdkName): void
    {
        $this->sdkName = $sdkName;
    }

    // 添加 getSdkType 方法
    public function getSdkType(): ?string
    {
        return $this->sdkType;
    }

    // 添加 setSdkType 方法
    public function setSdkType(?string $sdkType): void
    {
        $this->sdkType = $sdkType;
    }

    // 添加 getSdkVersion 方法
    public function getSdkVersion(): ?string
    {
        return $this->sdkVersion;
    }

    // 添加 setSdkVersion 方法
    public function setSdkVersion(?string $sdkVersion): void
    {
        $this->sdkVersion = $sdkVersion;
    }

    // 添加 getSessionId 方法
    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    // 添加 setSessionId 方法
    public function setSessionId(?string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    public function isJumpResult(): ?bool
    {
        return $this->jumpResult;
    }

    public function setJumpResult(bool $jumpResult): void
    {
        $this->jumpResult = $jumpResult;
    }

    public function getCreatedFromUa(): ?string
    {
        return $this->createdFromUa;
    }

    public function setCreatedFromUa(?string $createdFromUa): void
    {
        $this->createdFromUa = $createdFromUa;
    }

    public function getCreateTime(): ?\DateTimeImmutable
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
