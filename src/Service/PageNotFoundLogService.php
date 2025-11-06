<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use WechatMiniProgramTrackingBundle\Config\TrackingConfig;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundRequest;
use WechatMiniProgramTrackingBundle\DTO\ReportWechatMiniProgramPageNotFoundResponse;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;
use Yiisoft\Json\Json;

/**
 * 页面不存在日志服务
 *
 * 分离业务逻辑，提高代码可测试性和可维护性
 */
class PageNotFoundLogService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TrackingConfig $config,
    ) {
    }

    /**
     * 创建页面不存在日志记录
     */
    public function createPageNotFoundLog(ReportWechatMiniProgramPageNotFoundRequest $request): PageNotFoundLog
    {
        $log = new PageNotFoundLog();

        // 设置基本错误信息
        $log->setAccount(null);
        $log->setPath($request->getErrorPath());
        $log->setOpenType($request->getErrorOpenType());
        $log->setQuery($request->getErrorQuery());

        // 设置原始错误信息
        try {
            $rawError = Json::encode($request->error);
            $log->setRawError($rawError);
        } catch (\Exception $e) {
            // 如果 JSON 编码失败，使用字符串表示
            $log->setRawError(json_encode(['error' => 'Failed to encode error data', 'original' => (string) $e]));
        }

        // 设置启动选项
        if ($request->launchOptions !== null) {
            $log->setLaunchOptions($request->launchOptions);
        }

        // 设置进入选项
        if ($request->enterOptions !== null) {
            $log->setEnterOptions($request->enterOptions);
        }

        return $log;
    }

    /**
     * 保存页面不存在日志
     *
     * @throws \RuntimeException 当保存失败时
     */
    public function savePageNotFoundLog(PageNotFoundLog $log): void
    {
        try {
            $this->entityManager->persist($log);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException('保存页面不存在日志失败: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 生成重启 URL
     */
    public function generateReLaunchUrl(ReportWechatMiniProgramPageNotFoundRequest $request): ?string
    {
        if (!$request->isAppLaunch()) {
            return null;
        }

        return $this->config->getNotFoundFallbackPage();
    }

    /**
     * 处理页面不存在上报
     */
    public function handleReport(ReportWechatMiniProgramPageNotFoundRequest $request): ReportWechatMiniProgramPageNotFoundResponse
    {
        $currentTime = time();

        try {
            // 验证请求
            $request->validate();

            // 创建日志记录
            $pageNotFoundLog = $this->createPageNotFoundLog($request);

            // 保存日志
            $this->savePageNotFoundLog($pageNotFoundLog);

            // 检查是否需要生成重启 URL
            $reLaunchUrl = $this->generateReLaunchUrl($request);

            if ($reLaunchUrl !== null) {
                return ReportWechatMiniProgramPageNotFoundResponse::withReLaunch($currentTime, $reLaunchUrl);
            }

            return ReportWechatMiniProgramPageNotFoundResponse::success($currentTime, null, '页面不存在日志上报成功');
        } catch (\InvalidArgumentException $e) {
            return ReportWechatMiniProgramPageNotFoundResponse::failure($currentTime, '请求参数无效: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            return ReportWechatMiniProgramPageNotFoundResponse::failure($currentTime, '处理请求失败: ' . $e->getMessage());
        } catch (\Exception $e) {
            return ReportWechatMiniProgramPageNotFoundResponse::failure($currentTime, '未知错误: ' . $e->getMessage());
        }
    }
}