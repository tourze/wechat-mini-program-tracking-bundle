<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Tests\Factory;

/**
 * PageVisitLog 实体测试数据工厂
 * 提供各种测试场景的数据创建方法
 */
final class PageVisitLogFactory
{
    /**
     * 创建基础测试数据
     * @return array<string, mixed>
     */
    public static function createBasicData(): array
    {
        $uniqueId = uniqid('', true);
        return [
            'page' => 'pages/home/index',
            'routeId' => 1001,
            'sessionId' => 'test-session-' . $uniqueId,
            'createdBy' => 'test-user',
            'query' => ['param1' => 'value1', 'param2' => 'value2'],
            'createTime' => new \DateTimeImmutable(),
            'createdFromIp' => '192.168.1.100',
        ];
    }

    /**
     * 创建带 null 字段的测试数据
     * @return array<string, mixed>
     */
    public static function createWithNullFields(): array
    {
        $uniqueId = uniqid('', true);
        return [
            'page' => 'pages/null/test',
            'routeId' => 2001,
            'sessionId' => 'null-session-' . $uniqueId,
            'createdBy' => null,
            'query' => null,
            'createTime' => null, // 这个应该是null来匹配测试期望
            'createdFromIp' => null,
        ];
    }

    /**
     * 创建复杂查询测试数据
     * @return array<string, mixed>
     */
    public static function createForComplexQuery(): array
    {
        $uniqueId = uniqid('', true);
        return [
            'page' => 'pages/complex/query',
            'routeId' => 3001,
            'sessionId' => 'complex-session-' . $uniqueId,
            'createdBy' => 'complex-user',
            'query' => ['foo' => 'bar', 'nested' => ['key' => 'value']],
            'createTime' => (new \DateTimeImmutable())->modify('-2 days'),
            'createdFromIp' => '10.0.0.1',
        ];
    }

    /**
     * 创建多个相似实体的数据（用于批量测试）
     * @return array<int, array<string, mixed>>
     */
    public static function createMultipleData(int $count = 5): array
    {
        $data = [];
        for ($i = 1; $i <= $count; ++$i) {
            $uniqueId = uniqid('', true);
            $data[] = [
                'page' => "pages/test{$i}/test{$i}",
                'routeId' => 1000 + $i,
                'sessionId' => "session-{$i}-{$uniqueId}",
                'createdBy' => "user{$i}",
                'query' => ['index' => $i],
                'createTime' => (new \DateTimeImmutable())->modify("+{$i} minutes"),
                'createdFromIp' => "192.168.1.{$i}",
            ];
        }

        return $data;
    }

    /**
     * 创建日期范围测试数据
     * @return array<int, array<string, mixed>>
     */
    public static function createDateRangeData(): array
    {
        $now = new \DateTimeImmutable();
        $yesterday = $now->modify('-1 day');
        $tomorrow = $now->modify('+1 day');

        $uniqueId = uniqid('', true);

        return [
            [
                'page' => 'pages/today/test',
                'routeId' => 1001,
                'sessionId' => "today-session-{$uniqueId}",
                'createdBy' => 'today-user',
                'createTime' => $now,
            ],
            [
                'page' => 'pages/yesterday/test',
                'routeId' => 1002,
                'sessionId' => "yesterday-session-{$uniqueId}",
                'createdBy' => 'yesterday-user',
                'createTime' => $yesterday,
            ],
            [
                'page' => 'pages/tomorrow/test',
                'routeId' => 1003,
                'sessionId' => "tomorrow-session-{$uniqueId}",
                'createdBy' => 'tomorrow-user',
                'createTime' => $tomorrow,
            ],
        ];
    }

    /**
     * 创建会话修复场景测试数据（模拟 RefinePageLogInfoCommand 使用场景）
     * @return array<int, array<string, mixed>>
     */
    public static function createSessionFixData(): array
    {
        $uniqueId = uniqid('', true);
        return [
            [
                'page' => 'pages/login/login',
                'routeId' => 1001,
                'sessionId' => "test-session-{$uniqueId}",
                'createdBy' => 'user123',
            ],
            [
                'page' => 'pages/home/home',
                'routeId' => 1002,
                'sessionId' => "test-session-{$uniqueId}",
                'createdBy' => null, // 需要被修复的记录
            ],
            [
                'page' => 'pages/profile/profile',
                'routeId' => 1003,
                'sessionId' => "other-session-{$uniqueId}",
                'createdBy' => 'other-user',
            ],
        ];
    }

    /**
     * 创建大数据集测试数据
     * @return array<int, array<string, mixed>>
     */
    public static function createLargeDataset(int $count = 50): array
    {
        $data = [];
        $uniqueId = uniqid('', true);
        for ($i = 1; $i <= $count; ++$i) {
            $data[] = [
                'page' => "pages/large{$i}/large{$i}",
                'routeId' => 2000 + $i,
                'sessionId' => "large-session-{$i}-{$uniqueId}",
                'createdBy' => "large-user{$i}",
            ];
        }

        return $data;
    }

    /**
     * 创建 nullable 字段组合测试数据
     * @return array<int, array<string, mixed>>
     */
    public static function createNullableCombinations(): array
    {
        return [
            [
                'page' => 'pages/all-null/test',
                'routeId' => 22001,
                'sessionId' => 'all-null-session',
                'createdBy' => null,
                'query' => null,
                'createTime' => null, // 这个应该是null来匹配测试期望
                'createdFromIp' => null,
            ],
            [
                'page' => 'pages/partial-null/test',
                'routeId' => 22002,
                'sessionId' => 'partial-null-session',
                'createdBy' => 'test-user-2',
                'query' => null,
                'createTime' => null, // 这个应该是null来匹配测试期望
                'createdFromIp' => null,
            ],
            [
                'page' => 'pages/mixed-null/test',
                'routeId' => 22003,
                'sessionId' => 'mixed-null-session',
                'createdBy' => null,
                'query' => ['key' => 'value'],
                'createTime' => new \DateTimeImmutable(), // 这个有值
                'createdFromIp' => '192.168.1.1',
            ],
        ];
    }

    /**
     * 自定义数据提供器 - 基础CRUD测试
     * @return array<string, array{array<string, mixed>}>
     */
    public static function basicCrudProvider(): array
    {
        return [
            'basic data' => [self::createBasicData()],
            'data with nulls' => [self::createWithNullFields()],
        ];
    }

    /**
     * 自定义数据提供器 - 查找测试
     * @return array<string, array{array<string, mixed>, array<string, mixed>}>
     */
    public static function findByProvider(): array
    {
        $uniqueId = uniqid('', true);
        return [
            'find by session id' => [
                ['sessionId' => "test-session-provider-1-{$uniqueId}"],
                ['page' => 'pages/home/index', 'routeId' => 1001, 'sessionId' => "test-session-provider-1-{$uniqueId}", 'createdBy' => 'test-user'],
            ],
            'find by created by' => [
                ['createdBy' => 'test-user'],
                ['page' => 'pages/home/index', 'routeId' => 1002, 'sessionId' => "test-session-provider-2-{$uniqueId}", 'createdBy' => 'test-user'],
            ],
            'find by route id' => [
                ['routeId' => 1003],
                ['page' => 'pages/home/index', 'routeId' => 1003, 'sessionId' => "test-session-provider-3-{$uniqueId}", 'createdBy' => 'test-user'],
            ],
            'find by null created by' => [
                ['createdBy' => null],
                ['page' => 'pages/null/test', 'routeId' => 2002, 'sessionId' => "null-session-provider-{$uniqueId}", 'createdBy' => null],
            ],
        ];
    }

    /**
     * 自定义数据提供器 - 多条件查找测试
     * @return array<string, array{array<string, mixed>, array<string, mixed>}>
     */
    public static function multipleCriteriaProvider(): array
    {
        $uniqueId = uniqid('', true);
        return [
            'session and created by' => [
                ['sessionId' => "test-session-multi-1-{$uniqueId}", 'createdBy' => 'test-user'],
                ['page' => 'pages/home/index', 'routeId' => 1004, 'sessionId' => "test-session-multi-1-{$uniqueId}", 'createdBy' => 'test-user'],
            ],
            'route and session' => [
                ['routeId' => 1005, 'sessionId' => "test-session-multi-2-{$uniqueId}"],
                ['page' => 'pages/home/index', 'routeId' => 1005, 'sessionId' => "test-session-multi-2-{$uniqueId}"],
            ],
        ];
    }
}