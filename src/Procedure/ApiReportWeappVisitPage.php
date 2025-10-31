<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodTag(name: '微信小程序')]
#[MethodDoc(summary: '小程序启动访问上报接口')]
#[MethodExpose(method: 'apiReportWeappVisitPage')]
class ApiReportWeappVisitPage extends LockableProcedure
{
    /**
     * @var string 访问路径
     */
    public $path;

    /**
     * @var array<string, mixed> 参数
     */
    public $query;

    /**
     * @var array<string, mixed> 访问来源信息
     */
    public $referrerInfo = [];

    /**
     * @var int 场景值
     */
    public $scene = 0;

    /**
     * @var string TICKET
     */
    public $shareTicket = '';

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        return [
            'ok' => 1,
        ];
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
