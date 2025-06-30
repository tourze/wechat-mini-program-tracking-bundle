<?php

namespace WechatMiniProgramTrackingBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;

#[MethodExpose(method: 'apiReportWeappVisitPage')]
class ApiReportWeappVisitPage extends LockableProcedure
{
    /**
     * @var string 访问路径
     */
    public $path;

    /**
     * @var array 参数
     */
    public $query;

    /**
     * @var array 访问来源信息
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
