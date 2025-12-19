<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Procedure;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogRequest;
use WechatMiniProgramTrackingBundle\DTO\ReportJumpTrackingLogResponse;
use WechatMiniProgramTrackingBundle\Param\ReportJumpTrackingLogParam;
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

    /**
     * @phpstan-param ReportJumpTrackingLogParam $param
     */
    public function execute(ReportJumpTrackingLogParam|RpcParamInterface $param): ArrayResult
    {
        try {
            // 创建请求 DTO
            $request = ReportJumpTrackingLogRequest::fromProcedure($param);

            // 委托给服务层处理
            $response = $this->jumpTrackingLogService->handleReport($request);

            // 返回向后兼容的格式
            return new ArrayResult($response->toLegacyArray());
        } catch (\Exception $e) {
            // 异常处理,确保返回格式一致
            return new ArrayResult(ReportJumpTrackingLogResponse::failure($e->getMessage())->toLegacyArray());
        }
    }
}
