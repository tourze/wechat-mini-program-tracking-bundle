# wechat-mini-program-tracking-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![最新版本](https://img.shields.io/packagist/v/tourze/wechat-mini-program-tracking-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-tracking-bundle)
[![PHP 版本](https://img.shields.io/badge/php-%5E8.2-blue.svg?style=flat-square)](https://www.php.net/)
[![许可证](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)
[![总下载量](https://img.shields.io/packagist/dt/tourze/wechat-mini-program-tracking-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-tracking-bundle)

微信小程序追踪包提供了用于跟踪微信小程序用户交互和页面访问的实体和服务。

## 安装

```bash
composer require tourze/wechat-mini-program-tracking-bundle
```

## 功能特性

- 页面访问追踪
- 跳转追踪日志
- 页面未找到日志记录
- 用户代理和IP追踪
- 自动日志清理，可配置保留期限

## 实体

### PageVisitLog
追踪微信小程序中的页面访问，包含会话追踪。

### JumpTrackingLog
记录跳转追踪事件，包含全面的设备和用户信息。

### PageNotFoundLog
记录404错误和页面未找到事件。

## 控制台命令

### wechat-mini-program:refine-page-log-info
定期通过匹配会话ID来修正页面访问日志的创建者信息。

```bash
php bin/console wechat-mini-program:refine-page-log-info
```

此命令每10分钟通过定时任务运行，用于更新页面访问日志中缺失的 `createdBy` 信息。

## JSON-RPC 过程

- `ApiReportWeappVisitPage` - 报告微信小程序页面访问
- `ReportJumpTrackingLog` - 报告跳转追踪事件
- `ReportWechatMiniProgramPageNotFound` - 报告404错误

## 配置

该包使用环境变量进行日志保留配置：

- `PAGE_VISIT_LOG_PERSIST_DAY_NUM` - 保留页面访问日志的天数（默认：3天）
- `JUMP_TRACKING_LOG_PERSIST_DAY_NUM` - 保留跳转追踪日志的天数（默认：30天）

## 快速开始

### 1. 注册包

```php
<?php
// config/bundles.php

return [
    // ...
    WechatMiniProgramTrackingBundle\WechatMiniProgramTrackingBundle::class => ['all' => true],
];
```

### 2. 配置Doctrine

该包提供Doctrine实体的自动配置。基本使用无需额外配置。

### 3. 使用JSON-RPC过程

```php
// 示例：报告页面访问
$response = $jsonRpcClient->call('ApiReportWeappVisitPage', [
    'page' => 'pages/home/index',
    'routeId' => 1001,
    'sessionId' => 'user-session-123',
    'query' => ['param1' => 'value1']
]);

// 示例：报告跳转追踪事件
$response = $jsonRpcClient->call('ReportJumpTrackingLog', [
    'page' => 'pages/product/detail',
    'openId' => 'user-openid',
    'eventName' => 'click_product'
]);
```

## 使用方法

1. 在您的Symfony应用程序中注册该包
2. 配置实体管理器和仓储
3. 使用JSON-RPC过程收集追踪数据
4. 通过提供的实体和仓储监控日志

## 贡献

欢迎贡献！请随时提交Pull Request。

## 许可证

MIT许可证。详情请查看[许可证文件](LICENSE)。
