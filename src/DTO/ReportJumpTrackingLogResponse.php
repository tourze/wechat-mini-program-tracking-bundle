<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\DTO;

/**
 * 跳转追踪日志上报响应 DTO
 *
 * 标准化响应格式，分离响应组装逻辑
 */
class ReportJumpTrackingLogResponse
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly bool $success = true,
        public readonly ?string $message = null,
    ) {
    }

    /**
     * 创建成功响应
     */
    public static function success(?int $id = null, ?string $message = null): self
    {
        return new self($id, true, $message);
    }

    /**
     * 创建失败响应
     */
    public static function failure(string $message): self
    {
        return new self(null, false, $message);
    }

    /**
     * 转换为数组格式
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'success' => $this->success,
        ];

        if ($this->id !== null) {
            $result['id'] = $this->id;
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
        if ($this->success && $this->id !== null) {
            return ['id' => $this->id];
        }

        return $this->toArray();
    }
}