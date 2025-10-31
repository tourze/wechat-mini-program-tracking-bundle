<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;

class PageVisitLogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $log1 = new PageVisitLog();
        $log1->setPage('/pages/index/index');
        $log1->setRouteId(1);
        $log1->setSessionId('session_001');
        $log1->setQuery(['from' => 'home']);
        $log1->setCreatedFromIp('192.168.1.100');

        $log2 = new PageVisitLog();
        $log2->setPage('/pages/product/list');
        $log2->setRouteId(2);
        $log2->setSessionId('session_002');
        $log2->setQuery(['category' => 'electronics']);
        $log2->setCreatedFromIp('192.168.1.101');

        $log3 = new PageVisitLog();
        $log3->setPage('/pages/user/profile');
        $log3->setRouteId(3);
        $log3->setSessionId('session_003');
        $log3->setQuery(['tab' => 'settings']);
        $log3->setCreatedFromIp('192.168.1.102');

        $manager->persist($log1);
        $manager->persist($log2);
        $manager->persist($log3);
        $manager->flush();
    }
}
