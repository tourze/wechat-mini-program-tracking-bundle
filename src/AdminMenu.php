<?php

namespace WechatMiniProgramTrackingBundle;

use AmisBundle\Attribute\MenuProvider;
use Knp\Menu\ItemInterface;

#[MenuProvider]
class AdminMenu
{
    public function __invoke(ItemInterface $item): void
    {
    }
}
