<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * 微信小程序页面不存在上报请求 DTO
 *
 * 分离请求数据的验证和转换逻辑
 */
class ReportWechatMiniProgramPageNotFoundRequest
{
    /**
     * @param array<string, mixed> $error
     * @param array<string, mixed>|null $launchOptions
     * @param array<string, mixed>|null $enterOptions
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'array')]
        public readonly array $error,

        #[Assert\Type(type: 'array')]
        public readonly ?array $launchOptions = null,

        #[Assert\Type(type: 'array')]
        public readonly ?array $enterOptions = null,
    ) {
    }

    /**
     * 获取错误路径
     */
    public function getErrorPath(): string
    {
        return is_string($this->error['path'] ?? null) ? $this->error['path'] : '';
    }

    /**
     * 获取打开类型
     */
    public function getErrorOpenType(): ?string
    {
        return is_string($this->error['openType'] ?? null) ? $this->error['openType'] : null;
    }

    /**
     * 获取查询参数
     *
     * @return array<string, mixed>
     */
    public function getErrorQuery(): array
    {
        return isset($this->error['query']) && is_array($this->error['query']) ? $this->error['query'] : [];
    }

    /**
     * 检查是否为应用启动
     */
    public function isAppLaunch(): bool
    {
        return $this->getErrorOpenType() === 'appLaunch';
    }

    /**
     * 从 Procedure 属性创建请求 DTO
     *
     * 这个方法充当 Procedure 和 DTO 之间的适配器
     */
    public static function fromProcedure(object $procedure): self
    {
        $error = [];
        $launchOptions = null;
        $enterOptions = null;

        // 安全地获取 error 属性
        if (property_exists($procedure, 'error')) {
            $errorValue = $procedure->error;
            if (is_array($errorValue)) {
                $error = $errorValue;
            }
        }

        // 安全地获取 launchOptions
        if (method_exists($procedure, 'getLaunchOptions')) {
            $launchOptions = $procedure->getLaunchOptions();
        }

        // 安全地获取 enterOptions
        if (method_exists($procedure, 'getEnterOptions')) {
            $enterOptions = $procedure->getEnterOptions();
        }

        return new self(
            $error,
            $launchOptions,
            $enterOptions,
        );
    }

    /**
     * 验证请求数据
     *
     * @throws \InvalidArgumentException 当请求数据无效时
     */
    public function validate(): void
    {
        if ($this->error === []) {
            throw new \InvalidArgumentException('错误信息不能为空');
        }

        // 这里可以添加更复杂的验证逻辑
        // 目前主要依赖 Symfony Validator 注解
    }
}