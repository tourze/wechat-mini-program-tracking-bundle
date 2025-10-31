# wechat-mini-program-tracking-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-mini-program-tracking-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-tracking-bundle)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue.svg?style=flat-square)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-mini-program-tracking-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-mini-program-tracking-bundle)

WeChat Mini Program Tracking Bundle provides entities and services for tracking WeChat mini program user interactions and page visits.

## Installation

```bash
composer require tourze/wechat-mini-program-tracking-bundle
```

## Features

- Page visit tracking
- Jump tracking logs
- Page not found logging
- User agent and IP tracking
- Automatic log cleaning with configurable retention periods

## Entities

### PageVisitLog
Tracks page visits in WeChat mini programs with session tracking.

### JumpTrackingLog
Records jump tracking events with comprehensive device and user information.

### PageNotFoundLog
Logs 404 errors and page not found events.

## Console Commands

### wechat-mini-program:refine-page-log-info
Periodically refines page visit log creator information by matching session IDs.

```bash
php bin/console wechat-mini-program:refine-page-log-info
```

This command runs every 10 minutes via cron job to update missing `createdBy` information in page visit logs.

## JSON-RPC Procedures

- `ApiReportWeappVisitPage` - Reports WeChat mini program page visits
- `ReportJumpTrackingLog` - Reports jump tracking events
- `ReportWechatMiniProgramPageNotFound` - Reports 404 errors

## Configuration

The bundle uses environment variables for log retention configuration:

- `PAGE_VISIT_LOG_PERSIST_DAY_NUM` - Days to keep page visit logs (default: 3)
- `JUMP_TRACKING_LOG_PERSIST_DAY_NUM` - Days to keep jump tracking logs (default: 30)

## Quick Start

### 1. Register the Bundle

```php
<?php
// config/bundles.php

return [
    // ...
    WechatMiniProgramTrackingBundle\WechatMiniProgramTrackingBundle::class => ['all' => true],
];
```

### 2. Configure Doctrine

The bundle provides auto-configuration for Doctrine entities. No additional configuration is needed for basic usage.

### 3. Use JSON-RPC Procedures

```php
// Example: Report a page visit
$response = $jsonRpcClient->call('ApiReportWeappVisitPage', [
    'page' => 'pages/home/index',
    'routeId' => 1001,
    'sessionId' => 'user-session-123',
    'query' => ['param1' => 'value1']
]);

// Example: Report a jump tracking event
$response = $jsonRpcClient->call('ReportJumpTrackingLog', [
    'page' => 'pages/product/detail',
    'openId' => 'user-openid',
    'eventName' => 'click_product'
]);
```

## Usage

1. Register the bundle in your Symfony application
2. Configure the entity manager and repositories
3. Use the JSON-RPC procedures to collect tracking data
4. Monitor logs via the provided entities and repositories

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.