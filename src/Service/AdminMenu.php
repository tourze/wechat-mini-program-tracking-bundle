<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;

/**
 * 微信小程序跟踪管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('微信小程序跟踪')) {
            $item->addChild('微信小程序跟踪')
                ->setAttribute('icon', 'fas fa-chart-line')
            ;
        }

        $trackingMenu = $item->getChild('微信小程序跟踪');
        if (null === $trackingMenu) {
            return;
        }

        $trackingMenu->addChild('页面访问日志')
            ->setUri($this->linkGenerator->getCurdListPage(PageVisitLog::class))
            ->setAttribute('icon', 'fas fa-eye')
        ;

        $trackingMenu->addChild('跳转tracking日志')
            ->setUri($this->linkGenerator->getCurdListPage(JumpTrackingLog::class))
            ->setAttribute('icon', 'fas fa-external-link-alt')
        ;

        $trackingMenu->addChild('404页面日志')
            ->setUri($this->linkGenerator->getCurdListPage(PageNotFoundLog::class))
            ->setAttribute('icon', 'fas fa-exclamation-triangle')
        ;
    }
}
