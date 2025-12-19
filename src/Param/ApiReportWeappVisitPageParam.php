<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class ApiReportWeappVisitPageParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '访问路径')]
        public string $path,

        /**
         * @var array<string, mixed>
         */
        #[MethodParam(description: '参数')]
        public array $query,

        /**
         * @var array<string, mixed>
         */
        #[MethodParam(description: '访问来源信息')]
        public array $referrerInfo = [],

        #[MethodParam(description: '场景值')]
        public int $scene = 0,

        #[MethodParam(description: 'TICKET')]
        public string $shareTicket = '',
    ) {}
}
