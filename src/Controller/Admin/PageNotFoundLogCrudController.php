<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatMiniProgramTrackingBundle\Entity\PageNotFoundLog;

#[AdminCrud(routePath: '/wechat-tracking/page-not-found-log', routeName: 'wechat_tracking_page_not_found_log')]
final class PageNotFoundLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageNotFoundLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('404页面日志')
            ->setEntityLabelInPlural('404页面日志')
            ->setPageTitle('index', '404页面日志列表')
            ->setPageTitle('detail', '404页面日志详情')
            ->setPageTitle('edit', '编辑404页面日志')
            ->setPageTitle('new', '新增404页面日志')
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

        yield AssociationField::new('account', '用户账号')
            ->formatValue(function ($value) {
                return $value ? $value->__toString() : '未知用户';
            })
        ;

        yield TextField::new('path', '路径')
            ->setMaxLength(1000)
            ->setHelp('发生404错误的页面路径')
        ;

        yield TextField::new('openType', '打开类型')
            ->hideOnIndex()
        ;

        yield TextareaField::new('queryAsJson', '参数')
            ->onlyOnDetail()
            ->setHelp('访问页面时的查询参数')
        ;

        yield TextareaField::new('rawError', '原始错误')
            ->hideOnIndex()
        ;

        yield TextField::new('openId', 'OpenID')
            ->hideOnIndex()
        ;

        yield TextField::new('unionId', 'UnionID')
            ->hideOnIndex()
        ;

        yield TextField::new('createdFromUa', '用户代理')
            ->hideOnIndex()
        ;

        yield TextField::new('createdFromIp', '创建IP')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnIndex()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('account')
            ->add('path')
            ->add('openType')
            ->add('openId')
            ->add('unionId')
            ->add('createTime')
            ->add('createdFromIp')
        ;
    }
}
