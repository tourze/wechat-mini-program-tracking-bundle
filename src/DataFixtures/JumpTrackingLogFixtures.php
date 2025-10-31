<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;

class JumpTrackingLogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $log1 = new JumpTrackingLog();
        $log1->setPage('/pages/index/index');
        $log1->setOpenId('sample_open_id_1');
        $log1->setUnionId('sample_union_id_1');
        $log1->setQuery(['param1' => 'value1']);
        $log1->setAppKey('sample_app_key');
        $log1->setBusinessChannel('miniprogram');
        $log1->setDeviceBrand('iPhone');
        $log1->setDeviceModel('iPhone 14');
        $log1->setDeviceSystem('iOS');
        $log1->setDeviceSystemVersion('16.0');
        $log1->setEventName('page_jump');
        $log1->setPlatform('wechat');
        $log1->setJumpResult(true);

        $log2 = new JumpTrackingLog();
        $log2->setPage('/pages/product/detail');
        $log2->setOpenId('sample_open_id_2');
        $log2->setUnionId('sample_union_id_2');
        $log2->setQuery(['productId' => '123']);
        $log2->setAppKey('sample_app_key');
        $log2->setBusinessChannel('miniprogram');
        $log2->setDeviceBrand('Huawei');
        $log2->setDeviceModel('P50 Pro');
        $log2->setDeviceSystem('Android');
        $log2->setDeviceSystemVersion('12.0');
        $log2->setEventName('product_view');
        $log2->setPlatform('wechat');
        $log2->setJumpResult(true);

        $manager->persist($log1);
        $manager->persist($log2);
        $manager->flush();
    }
}
