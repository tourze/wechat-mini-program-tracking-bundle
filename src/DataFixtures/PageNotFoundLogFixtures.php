<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;

class PageNotFoundLogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $log1 = new PageNotFoundLog();
        $log1->setPath('/pages/not-found/index');
        $log1->setOpenType('navigateTo');
        $log1->setQuery(['from' => 'home']);
        $log1->setRawError('Page not found: /pages/not-found/index');
        $log1->setOpenId('sample_open_id_1');
        $log1->setUnionId('sample_union_id_1');

        $log2 = new PageNotFoundLog();
        $log2->setPath('/pages/missing/detail');
        $log2->setOpenType('redirectTo');
        $log2->setQuery(['id' => '999']);
        $log2->setRawError('Page does not exist: /pages/missing/detail');
        $log2->setOpenId('sample_open_id_2');
        $log2->setUnionId('sample_union_id_2');

        $manager->persist($log1);
        $manager->persist($log2);
        $manager->flush();
    }
}
