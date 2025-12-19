<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\DTO;

/**
 * 微信小程序页面不存在上报响应 DTO
 *
 * 标准化响应格式，分离响应组装逻辑
 */
class ReportWechatMiniProgramPageNotFoundResponse
{
    /**
     * @param array<string, mixed>|null $reLaunch
     */
    public function __construct(
        public readonly int $time,
        public readonly ?array $reLaunch = null,
        public readonly bool $success = true,
        public readonly ?string $message = null,
    ) {
    }

    /**
     * 创建成功响应
     *
     * @param array<string, mixed>|null $reLaunch
     */
    public static function success(int $time, ?array $reLaunch = null, ?string $message = null): self
    {
        return new self($time, $reLaunch, true, $message);
    }

    /**
     * 创建失败响应
     */
    public static function failure(int $time, string $message): self
    {
        return new self($time, null, false, $message);
    }

    /**
     * 创建包含重启信息的响应
     */
    public static function withReLaunch(int $time, string $reLaunchUrl): self
    {
        return new self($time, ['url' => $reLaunchUrl]);
    }

    /**
     * 转换为数组格式
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'time' => $this->time,
            'success' => $this->success,
        ];

        if ($this->reLaunch !== null) {
            $result['__reLaunch'] = $this->reLaunch;
        }

        if ($this->message !== null) {
            $result['message'] = $this->message;
        }

        return $result;
    }

    /**
     * 保持向后兼容性的数组格式
     *
     * @return array<string, mixed>
     */
    public function toLegacyArray(): array
    {
        $result = [
            'time' => $this->time,
        ];

        if ($this->reLaunch !== null) {
            $result['__reLaunch'] = $this->reLaunch;
        }

        return $result;
    }
}