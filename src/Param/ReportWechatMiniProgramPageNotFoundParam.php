<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Param;

use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class ReportWechatMiniProgramPageNotFoundParam implements RpcParamInterface
{
    public function __construct(
        /**
         * @var array<string, mixed>
         */
        #[MethodParam(description: '错误信息')]
        public array $error,

        /**
         * @var array<string, mixed>|null
         */
        #[MethodParam(description: '启动选项')]
        public ?array $launchOptions = [],

        /**
         * @var array<string, mixed>|null
         */
        #[MethodParam(description: '进入选项')]
        public ?array $enterOptions = [],
    ) {}
}
