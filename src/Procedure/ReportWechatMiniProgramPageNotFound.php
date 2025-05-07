<?php

namespace WechatMiniProgramTrackingBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatMiniProgramBundle\Procedure\LaunchOptionsAware;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;
use WechatMiniProgramTrackingBundle\Repository\PageNotFoundLogRepository;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Json\Json;

#[MethodTag('微信小程序')]
#[MethodDoc('上报不存在的页面')]
#[MethodExpose('ReportWechatMiniProgramPageNotFound')]
#[Log]
class ReportWechatMiniProgramPageNotFound extends LockableProcedure
{
    use LaunchOptionsAware;

    #[MethodParam('错误信息')]
    public array $error;

    public function __construct(
        private readonly PageNotFoundLogRepository $logRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(): array
    {
        // 日志存库
        $log = new PageNotFoundLog();
        $log->setAccount(null);
        $log->setPath($this->error['path']);
        $log->setOpenType($this->error['openType'] ?? '');
        $log->setQuery($this->error['query'] ?? null);
        $log->setRawError(Json::encode($this->error));
        $log->setLaunchOptions($this->launchOptions);
        $log->setEnterOptions($this->enterOptions);
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        $result = [
            'time' => time(),
        ];

        // 如果一启动就进入了一个不存在的页面，那我们就尝试重新进入页面吧
        $openType = ArrayHelper::getValue($this->error, 'openType');
        if ('appLaunch' === $openType) {
            $result['__reLaunch'] = [
                'url' => $_ENV['WECHAT_MINI_PROGRAM_NOT_FOUND_FALLBACK_PAGE'] ?? '/pages/index/index?_from=page_not_found',
            ];
        }

        return $result;
    }
}
