<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;
use Tourze\UserServiceContracts\UserManagerInterface;
use WechatMiniProgramTrackingBundle\Config\TrackingConfig;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogRequest;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogResponse;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;

/**
 * 跳转追踪日志服务
 *
 * 分离业务逻辑，提高代码可测试性和可维护性
 */
class JumpTrackingLogService
{
    public function __construct(
        private readonly DoctrineService $doctrineService,
        private readonly Security $security,
        private readonly TrackingConfig $config,
        private readonly UserManagerInterface $userManager,
    ) {
    }

    /**
     * 创建跳转追踪日志记录
     */
    public function createTrackingLog(ReportJumpTrackingLogRequest $request): JumpTrackingLog
    {
        $jumpTrackingLog = new JumpTrackingLog();

        // 设置页面信息
        if ($request->currentPath !== null) {
            $jumpTrackingLog->setPage($request->currentPath);
        }

        // 设置设备信息
        $jumpTrackingLog->setDeviceBrand($request->deviceBrand);
        $jumpTrackingLog->setDeviceId($request->deviceId);
        $jumpTrackingLog->setDeviceModel($request->deviceModel);
        $jumpTrackingLog->setDeviceScreenHeight($request->deviceScreenHeight);
        $jumpTrackingLog->setDeviceScreenWidth($request->deviceScreenWidth);
        $jumpTrackingLog->setDeviceSystem($request->deviceSystem);
        $jumpTrackingLog->setDeviceSystemVersion($request->deviceSystemVersion);

        // 设置事件信息
        $jumpTrackingLog->setEventName($request->eventName);
        $jumpTrackingLog->setEventParam($request->eventParam);

        // 设置网络信息
        $jumpTrackingLog->setNetworkType($request->networkType);

        // 设置页面信息
        $jumpTrackingLog->setPageName($request->pageName);
        $jumpTrackingLog->setPageQuery($request->pageQuery);
        $jumpTrackingLog->setPageTitle($request->pageTitle);
        $jumpTrackingLog->setPageUrl($request->pageUrl);

        // 设置平台信息
        $jumpTrackingLog->setPlatform($request->platform);

        // 设置会话信息
        $jumpTrackingLog->setPrevPath($request->prevPath);
        $jumpTrackingLog->setPrevSessionId($request->prevSessionId);
        $jumpTrackingLog->setSessionId($request->sessionId);

        // 设置场景信息
        $jumpTrackingLog->setScene($request->scene);

        // 设置 SDK 信息
        $jumpTrackingLog->setSdkName($request->sdkName);
        $jumpTrackingLog->setSdkType($request->sdkType);
        $jumpTrackingLog->setSdkVersion($request->sdkVersion);

        // 设置跳转结果
        if ($request->jumpResult !== null) {
            $jumpTrackingLog->setJumpResult($request->jumpResult);
        }

        // 设置用户信息
        $this->setUserInfo($jumpTrackingLog);

        return $jumpTrackingLog;
    }

    /**
     * 设置用户信息
     */
    private function setUserInfo(JumpTrackingLog $jumpTrackingLog): void
    {
        $user = $this->security->getUser();
        if ($user === null) {
            return;
        }

        $jumpTrackingLog->setOpenId($user->getUserIdentifier());

        // 使用 UserManagerInterface 获取用户身份信息
        if ($this->config->supportsUserIdentity()) {
            try {
                // 通过 UserManagerInterface 获取完整的用户信息
                $userIdentifier = $user->getUserIdentifier();
                $fullUser = $this->userManager->loadUserByIdentifier($userIdentifier);

                if ($fullUser !== null && method_exists($fullUser, 'getIdentity')) {
                    $identity = $fullUser->getIdentity();
                    if ($identity !== null) {
                        $jumpTrackingLog->setUnionId($identity);
                    }
                }
            } catch (\Exception $e) {
                // 记录错误但不中断流程
                // 可以考虑记录到日志中
                // 暂时静默处理，保持向后兼容性
            }
        }
    }

    /**
     * 保存追踪日志
     *
     * @throws \RuntimeException 当保存失败时
     */
    public function saveTrackingLog(JumpTrackingLog $jumpTrackingLog): void
    {
        try {
            $this->doctrineService->asyncInsert($jumpTrackingLog);
        } catch (\Exception $e) {
            throw new \RuntimeException('保存跳转追踪日志失败: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 处理跳转追踪日志上报
     */
    public function handleReport(ReportJumpTrackingLogRequest $request): ReportJumpTrackingLogResponse
    {
        try {
            // 验证请求
            $request->validate();

            // 创建日志记录
            $jumpTrackingLog = $this->createTrackingLog($request);

            // 保存日志
            $this->saveTrackingLog($jumpTrackingLog);

            return ReportJumpTrackingLogResponse::success(
                $jumpTrackingLog->getId(),
                '跳转追踪日志上报成功'
            );
        } catch (\InvalidArgumentException $e) {
            return ReportJumpTrackingLogResponse::failure('请求参数无效: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            return ReportJumpTrackingLogResponse::failure('处理请求失败: ' . $e->getMessage());
        } catch (\Exception $e) {
            return ReportJumpTrackingLogResponse::failure('未知错误: ' . $e->getMessage());
        }
    }
}