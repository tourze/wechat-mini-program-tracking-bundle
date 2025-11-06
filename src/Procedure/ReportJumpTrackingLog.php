<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Procedure;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogRequest;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogResponse;
use WechatMiniProgramTrackingBundle\Service\JumpTrackingLogService;

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
        private readonly JumpTrackingLogService $jumpTrackingLogService,
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

    /**
     * @var array<string, mixed>|null
     */
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

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        try {
            // 创建请求 DTO
            $request = ReportJumpTrackingLogRequest::fromProcedure($this);

            // 委托给服务层处理
            $response = $this->jumpTrackingLogService->handleReport($request);

            // 返回向后兼容的格式
            return $response->toLegacyArray();
        } catch (\Exception $e) {
            // 异常处理，确保返回格式一致
            return ReportJumpTrackingLogResponse::failure($e->getMessage())->toLegacyArray();
        }
    }
}
