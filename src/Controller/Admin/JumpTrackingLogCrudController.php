<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatMiniProgramTrackingBundle\Entity\JumpTrackingLog;

#[AdminCrud(routePath: '/wechat-tracking/jump-log', routeName: 'wechat_tracking_jump_log')]
final class JumpTrackingLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return JumpTrackingLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('跳转tracking日志')
            ->setEntityLabelInPlural('跳转tracking日志')
            ->setPageTitle('index', '跳转tracking日志列表')
            ->setPageTitle('detail', '跳转tracking日志详情')
            ->setPageTitle('edit', '编辑跳转tracking日志')
            ->setPageTitle('new', '新增跳转tracking日志')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->showEntityActionsInlined()
            ->setPaginatorPageSize(50)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT)
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnDetail();

        yield TextField::new('page', '页面路径')
            ->setHelp('跳转的页面路径')
        ;

        yield TextField::new('openId', 'OpenID')
            ->hideOnIndex()
        ;

        yield TextField::new('unionId', 'UnionID')
            ->hideOnIndex()
        ;

        yield TextareaField::new('queryAsJson', '参数')
            ->onlyOnDetail()
            ->setHelp('跳转时携带的查询参数')
        ;

        yield TextField::new('appKey', 'AppKey')
            ->hideOnIndex()
        ;

        yield TextField::new('businessChannel', '业务渠道')
            ->hideOnIndex()
        ;

        yield TextField::new('deviceBrand', '设备品牌')
            ->hideOnIndex()
        ;

        yield TextField::new('deviceModel', '设备型号')
            ->hideOnIndex()
        ;

        yield IntegerField::new('deviceScreenHeight', '设备屏幕高度')
            ->hideOnIndex()
        ;

        yield IntegerField::new('deviceScreenWidth', '设备屏幕宽度')
            ->hideOnIndex()
        ;

        yield TextField::new('deviceSystem', '设备系统')
            ->hideOnIndex()
        ;

        yield TextField::new('deviceSystemVersion', '设备系统版本')
            ->hideOnIndex()
        ;

        yield TextField::new('eventName', '事件名称')
            ->hideOnIndex()
        ;

        yield TextareaField::new('eventParamAsJson', '事件参数')
            ->onlyOnDetail()
            ->setHelp('埋点事件上报的参数')
        ;

        yield TextField::new('networkType', '网络类型')
            ->hideOnIndex()
        ;

        yield TextField::new('pageName', '页面名称')
            ->hideOnIndex()
        ;

        yield TextField::new('pageTitle', '页面标题')
            ->hideOnIndex()
        ;

        yield TextField::new('pageUrl', '页面URL')
            ->hideOnIndex()
        ;

        yield TextField::new('platform', '平台')
            ->hideOnIndex()
        ;

        yield TextField::new('sessionId', '会话ID')
            ->hideOnIndex()
        ;

        yield BooleanField::new('jumpResult', '跳转结果')
            ->renderAsSwitch(false)
        ;

        yield TextField::new('createdFromUa', '用户代理')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('page')
            ->add('openId')
            ->add('unionId')
            ->add('appKey')
            ->add('businessChannel')
            ->add('deviceBrand')
            ->add('deviceSystem')
            ->add('eventName')
            ->add('networkType')
            ->add('platform')
            ->add('jumpResult')
            ->add('createTime')
        ;
    }
}
