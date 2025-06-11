<?php

namespace WechatMiniProgramTrackingBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;

/**
 * 上报跳转tracking日志
 */
#[MethodTag('微信小程序')]
#[MethodDoc('上报跳转tracking日志')]
#[MethodExpose('ReportJumpTrackingLog')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Log]
class ReportJumpTrackingLog extends BaseProcedure
{
    public function __construct(
        private readonly AsyncInsertService $doctrineService,
        private readonly Security $security,
    ) {
    }

    #[MethodParam('当前路径')]
    public ?string $currentPath = null;

    #[MethodParam('跳转结果')]
    public ?bool $jumpResult = false;

    #[MethodParam('设备品牌')]
    public ?string $deviceBrand = null;

    #[MethodParam('设备ID')]
    public ?string $deviceId = null;

    #[MethodParam('设备型号')]
    public ?string $deviceModel = null;

    #[MethodParam('设备屏幕高度')]
    public ?int $deviceScreenHeight = null;

    #[MethodParam('设备屏幕宽度')]
    public ?int $deviceScreenWidth = null;

    #[MethodParam('设备系统')]
    public ?string $deviceSystem = null;

    #[MethodParam('设备系统版本')]
    public ?string $deviceSystemVersion = null;

    #[MethodParam('事件名称')]
    public ?string $eventName = null;

    #[MethodParam('事件参数')]
    public ?array $eventParam = null;

    #[MethodParam('网络类型')]
    public ?string $networkType = null;

    #[MethodParam('页面名称')]
    public ?string $pageName = null;

    #[MethodParam('页面查询参数')]
    public ?string $pageQuery = null;

    #[MethodParam('页面标题')]
    public ?string $pageTitle = null;

    #[MethodParam('页面URL')]
    public ?string $pageUrl = null;

    #[MethodParam('平台')]
    public ?string $platform = null;

    #[MethodParam('前一个路径')]
    public ?string $prevPath = null;

    #[MethodParam('前一个会话ID')]
    public ?string $prevSessionId = null;

    #[MethodParam('场景')]
    public ?string $scene = null;

    #[MethodParam('SDK名称')]
    public ?string $sdkName = null;

    #[MethodParam('SDK类型')]
    public ?string $sdkType = null;

    #[MethodParam('SDK版本')]
    public ?string $sdkVersion = null;

    #[MethodParam('会话ID')]
    public ?string $sessionId = null;

    public function execute(): array
    {
        $jumpTrackingLog = new JumpTrackingLog();
        $jumpTrackingLog->setPage($this->currentPath);
        $jumpTrackingLog->setJumpResult($this->jumpResult);
        $jumpTrackingLog->setDeviceBrand($this->deviceBrand);
        $jumpTrackingLog->setDeviceId($this->deviceId);
        $jumpTrackingLog->setDeviceModel($this->deviceModel);
        $jumpTrackingLog->setDeviceScreenHeight($this->deviceScreenHeight);
        $jumpTrackingLog->setDeviceScreenWidth($this->deviceScreenWidth);
        $jumpTrackingLog->setDeviceSystem($this->deviceSystem);
        $jumpTrackingLog->setDeviceSystemVersion($this->deviceSystemVersion);
        $jumpTrackingLog->setEventName($this->eventName);
        $jumpTrackingLog->setEventParam($this->eventParam);
        $jumpTrackingLog->setNetworkType($this->networkType);
        $jumpTrackingLog->setPageName($this->pageName);
        $jumpTrackingLog->setPageQuery($this->pageQuery);
        $jumpTrackingLog->setPageTitle($this->pageTitle);
        $jumpTrackingLog->setPageUrl($this->pageUrl);
        $jumpTrackingLog->setPlatform($this->platform);
        $jumpTrackingLog->setPrevPath($this->prevPath);
        $jumpTrackingLog->setPrevSessionId($this->prevSessionId);
        $jumpTrackingLog->setScene($this->scene);
        $jumpTrackingLog->setSdkName($this->sdkName);
        $jumpTrackingLog->setSdkType($this->sdkType);
        $jumpTrackingLog->setSdkVersion($this->sdkVersion);
        $jumpTrackingLog->setSessionId($this->sessionId);
        $jumpTrackingLog->setOpenId($this->security->getUser()->getUserIdentifier());
        $jumpTrackingLog->setUnionId($this->security->getUser()->getIdentity());

        $this->doctrineService->asyncInsert($jumpTrackingLog);

        return [
            'id' => $jumpTrackingLog->getId(),
        ];
    }
}
