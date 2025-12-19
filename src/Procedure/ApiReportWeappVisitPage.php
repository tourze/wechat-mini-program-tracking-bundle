<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use WechatMiniProgramTrackingBundle\Param\ApiReportWeappVisitPageParam;

#[MethodTag(name: '微信小程序')]
#[MethodDoc(summary: '小程序启动访问上报接口')]
#[MethodExpose(method: 'apiReportWeappVisitPage')]
class ApiReportWeappVisitPage extends LockableProcedure
{
    /**
     * @phpstan-param ApiReportWeappVisitPageParam $param
     */
    public function execute(ApiReportWeappVisitPageParam|RpcParamInterface $param): ArrayResult
    {
        return new ArrayResult([
            'ok' => 1,
        ]);
    }

    public static function getCategory(): string
    {
        return '微信小程序-页面上报';
    }

    public static function getDesc(): string
    {
        return '小程序启动访问上报接口';
    }
}
