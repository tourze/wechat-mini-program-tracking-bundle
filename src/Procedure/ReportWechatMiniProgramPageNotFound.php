<?php

declare(strict_types=1);

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
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Json\Json;

#[MethodTag(name: '微信小程序')]
#[MethodDoc(summary: '上报不存在的页面')]
#[MethodExpose(method: 'ReportWechatMiniProgramPageNotFound')]
#[Log]
class ReportWechatMiniProgramPageNotFound extends LockableProcedure
{
    use LaunchOptionsAware;

    /**
     * @var array<string, mixed>
     */
    #[MethodParam(description: '错误信息')]
    public array $error;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        // 日志存库
        $log = new PageNotFoundLog();
        $log->setAccount(null);
        $log->setPath(is_string($this->error['path'] ?? null) ? $this->error['path'] : '');
        $log->setOpenType(is_string($this->error['openType'] ?? null) ? $this->error['openType'] : null);
        $log->setQuery(isset($this->error['query']) && is_array($this->error['query']) ? $this->error['query'] : []);
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
                'url' => $_ENV['WECHAT_MINI_PROGRAM_NOT_FOUND_FALLBACK_PAGE'] ?? 'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index?_from=page_not_found',
            ];
        }

        return $result;
    }
}
