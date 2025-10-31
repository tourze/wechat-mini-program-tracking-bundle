<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatMiniProgramTrackingBundle\Entity\PageVisitLog;

#[AdminCrud(routePath: '/wechat-tracking/page-visit-log', routeName: 'wechat_tracking_page_visit_log')]
final class PageVisitLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageVisitLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('页面访问日志')
            ->setEntityLabelInPlural('页面访问日志')
            ->setPageTitle('index', '页面访问日志列表')
            ->setPageTitle('detail', '页面访问日志详情')
            ->setPageTitle('edit', '编辑页面访问日志')
            ->setPageTitle('new', '新增页面访问日志')
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
            ->setHelp('访问的页面路径')
        ;

        yield IntegerField::new('routeId', '路由ID')
            ->setHelp('页面对应的路由ID')
        ;

        yield TextField::new('sessionId', '会话ID')
            ->setHelp('用户会话标识')
        ;

        yield TextareaField::new('queryAsJson', '参数')
            ->onlyOnDetail()
            ->setHelp('访问页面时的查询参数')
        ;

        yield TextField::new('createdFromUa', '用户代理')
            ->hideOnIndex()
            ->setHelp('访问时的用户代理字符串')
        ;

        yield TextField::new('createdFromIp', '创建IP')
            ->hideOnIndex()
            ->setHelp('访问时的IP地址')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('日志记录时间')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('page')
            ->add('routeId')
            ->add('sessionId')
            ->add('createdFromIp')
            ->add('createTime')
        ;
    }
}
