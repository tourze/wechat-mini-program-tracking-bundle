<?php

namespace WechatMiniProgramTrackingBundle\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;

/**
 * 记录当前接口访问时，前端正在访问的小程序页面路径
 */
#[AutoconfigureTag('monolog.processor')]
#[AutoconfigureTag('as-coroutine')]
class RequestProcessor implements ProcessorInterface
{
    /**
     * @var string|null 当前正在访问的页面
     */
    private ?string $currentPage = null;

    /**
     * @var string|null 会话ID
     */
    private ?string $sessionId = null;

    /**
     * @var string|null 路由ID
     */
    private ?string $routeId = null;

    public function __construct(
        private readonly DoctrineService $doctrineService,
        private readonly CacheInterface $cache,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        if (null !== $this->currentPage) {
            $record->extra['current_page'] = $this->currentPage;
        }

        return $record;
    }

    #[AsEventListener(event: KernelEvents::REQUEST, priority: 2048)]
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $this->currentPage = null;
        foreach (['Current-Page', 'Current-Path'] as $key) {
            if ($request->headers->has($key)) {
                $this->currentPage = (string) $request->headers->get($key);
                break;
            }
        }

        $this->sessionId = null;
        if ($request->query->has('__sessionId')) {
            $this->sessionId = (string) $request->query->get('__sessionId');
        }

        $this->routeId = null;
        if ($request->query->has('__routeId')) {
            $this->routeId = (string) $request->query->get('__routeId');
        }
    }

    #[AsEventListener(event: KernelEvents::TERMINATE, priority: -999)]
    public function onTerminate(): void
    {
        try {
            $this->saveLog();
        } catch (\Throwable $exception) {
            // 这里发生异常的话，不希望抛出异常
        }

        // 每次结束后，我们都修改这个页面为null，防止上一次的数据污染今次的请求
        $this->currentPage = null;
    }

    private function saveLog(): void
    {
        if (null === $this->currentPage || null === $this->sessionId || null === $this->routeId) {
            return;
        }

        // 简单的防止重复插入，这里不加锁了
        $cacheKey = 'wm-page-visit-log-' . md5($this->currentPage . $this->sessionId . $this->routeId);
        if ($this->cache->has($cacheKey)) {
            return;
        }

        // 理论上，这里一定会重复
        $log = new PageVisitLog();
        $page = explode('?', $this->currentPage, 2);
        $log->setPage($page[0]);
        $query = [];
        if ((bool) isset($page[1])) {
            parse_str($page[1], $query);
        }
        $log->setQuery($query ?: (object) []);
        $log->setSessionId($this->sessionId);
        $log->setRouteId($this->routeId);
        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            $log->setCreatedBy($token->getUserIdentifier());
        }

        $this->doctrineService->asyncInsert($log, allowDuplicate: true);
        $this->cache->set($cacheKey, 1, 60);
    }
}
