<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundRequest;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundResponse;
use WechatMiniProgramTrackingBundle\Param\ReportWechatMiniProgramPageNotFoundParam;
use WechatMiniProgramTrackingBundle\Service\PageNotFoundLogService;

#[MethodTag(name: '微信小程序')]
#[MethodDoc(summary: '上报不存在的页面')]
#[MethodExpose(method: 'ReportWechatMiniProgramPageNotFound')]
#[Log]
class ReportWechatMiniProgramPageNotFound extends LockableProcedure
{
    public function __construct(
        private readonly PageNotFoundLogService $pageNotFoundLogService,
    ) {
    }

    /**
     * @phpstan-param ReportWechatMiniProgramPageNotFoundParam $param
     */
    public function execute(ReportWechatMiniProgramPageNotFoundParam|RpcParamInterface $param): ArrayResult
    {
        try {
            // 创建请求 DTO
            $request = ReportWechatMiniProgramPageNotFoundRequest::fromProcedure($param);

            // 委托给服务层处理
            $response = $this->pageNotFoundLogService->handleReport($request);

            // 返回向后兼容的格式
            return new ArrayResult($response->toLegacyArray());
        } catch (\Exception $e) {
            // 异常处理,确保返回格式一致
            $currentTime = time();
            return new ArrayResult(ReportWechatMiniProgramPageNotFoundResponse::failure($currentTime, $e->getMessage())->toLegacyArray());
        }
    }
}
