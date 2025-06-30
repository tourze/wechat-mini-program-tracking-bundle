<?php

namespace WechatMiniProgramTrackingBundle\Procedure;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;
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
#[MethodTag(name: '微信小程序')]
#[MethodDoc(summary: '上报跳转tracking日志')]
#[MethodExpose(method: 'ReportJumpTrackingLog')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class ReportJumpTrackingLog extends BaseProcedure
{
    public function __construct(
        private readonly DoctrineService $doctrineService,
        private readonly Security $security,
    ) {
    }

    #[MethodParam(description: '当前路径')]
    public ?string $currentPath = null;

    #[MethodParam(description: '跳转结果')]
    public ?bool $jumpResult = false;

    #[MethodParam(description: '设备品牌')]
    public ?string $deviceBrand = null;

    #[MethodParam(description: '设备ID')]
    public ?string $deviceId = null;

    #[MethodParam(description: '设备型号')]
    public ?string $deviceModel = null;

    #[MethodParam(description: '设备屏幕高度')]
    public ?int $deviceScreenHeight = null;

    #[MethodParam(description: '设备屏幕宽度')]
    public ?int $deviceScreenWidth = null;

    #[MethodParam(description: '设备系统')]
    public ?string $deviceSystem = null;

    #[MethodParam(description: '设备系统版本')]
    public ?string $deviceSystemVersion = null;

    #[MethodParam(description: '事件名称')]
    public ?string $eventName = null;

    #[MethodParam(description: '事件参数')]
    public ?array $eventParam = null;

    #[MethodParam(description: '网络类型')]
    public ?string $networkType = null;

    #[MethodParam(description: '页面名称')]
    public ?string $pageName = null;

    #[MethodParam(description: '页面查询参数')]
    public ?string $pageQuery = null;

    #[MethodParam(description: '页面标题')]
    public ?string $pageTitle = null;

    #[MethodParam(description: '页面URL')]
    public ?string $pageUrl = null;

    #[MethodParam(description: '平台')]
    public ?string $platform = null;

    #[MethodParam(description: '前一个路径')]
    public ?string $prevPath = null;

    #[MethodParam(description: '前一个会话ID')]
    public ?string $prevSessionId = null;

    #[MethodParam(description: '场景')]
    public ?string $scene = null;

    #[MethodParam(description: 'SDK名称')]
    public ?string $sdkName = null;

    #[MethodParam(description: 'SDK类型')]
    public ?string $sdkType = null;

    #[MethodParam(description: 'SDK版本')]
    public ?string $sdkVersion = null;

    #[MethodParam(description: '会话ID')]
    public ?string $sessionId = null;

    public function execute(): array
    {
        $jumpTrackingLog = new JumpTrackingLog();
        if (null !== $this->currentPath) {
            $jumpTrackingLog->setPage($this->currentPath);
        }
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
        $user = $this->security->getUser();
        if (null !== $user) {
            $jumpTrackingLog->setOpenId($user->getUserIdentifier());
            // TODO: getIdentity() 方法需要在用户实体中实现
            if (method_exists($user, 'getIdentity')) {
                $jumpTrackingLog->setUnionId($user->getIdentity());
            }
        }

        $this->doctrineService->asyncInsert($jumpTrackingLog);

        return [
            'id' => $jumpTrackingLog->getId(),
        ];
    }
}
