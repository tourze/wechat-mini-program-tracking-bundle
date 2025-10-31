<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\UpdateUserAgentColumn;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Entity\LaunchOptionsAware;
use WechatMiniProgramTrackingBundle\Repository\PageNotFoundLogRepository;

#[ORM\Entity(repositoryClass: PageNotFoundLogRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_page_not_found_log', options: ['comment' => '404页面日志'])]
class PageNotFoundLog implements \Stringable
{
    use TimestampableAware;
    use LaunchOptionsAware;
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[Assert\Length(max: 500)]
    #[CreateUserAgentColumn]
    private ?string $createdFromUa = null;

    #[Assert\Length(max: 500)]
    #[UpdateUserAgentColumn]
    private ?string $updatedFromUa = null;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Account $account = null;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::STRING, length: 1000, options: ['comment' => '路径'])]
    private ?string $path = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '打开类型'])]
    private ?string $openType = null;

    /**
     * @var array<string, mixed>
     */
    #[Assert\Valid]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数'])]
    private array $query = [];

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '原始错误'])]
    private ?string $rawError = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '字段说明'])]
    private ?string $openId = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '字段说明'])]
    private ?string $unionId = null;

    public function setCreatedFromUa(?string $createdFromUa): void
    {
        $this->createdFromUa = $createdFromUa;
    }

    public function getCreatedFromUa(): ?string
    {
        return $this->createdFromUa;
    }

    public function setUpdatedFromUa(?string $updatedFromUa): void
    {
        $this->updatedFromUa = $updatedFromUa;
    }

    public function getUpdatedFromUa(): ?string
    {
        return $this->updatedFromUa;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getOpenType(): ?string
    {
        return $this->openType;
    }

    public function setOpenType(?string $openType): void
    {
        $this->openType = $openType;
    }

    /**
     * @return array<string, mixed>
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    public function getQueryAsJson(): string
    {
        if ([] === $this->query) {
            return '';
        }

        $encoded = json_encode($this->query, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        return false === $encoded ? '' : $encoded;
    }

    /**
     * @param array<string, mixed>|null $query
     */
    public function setQuery(?array $query): void
    {
        $this->query = $query ?? [];
    }

    public function getRawError(): ?string
    {
        return $this->rawError;
    }

    public function setRawError(?string $rawError): void
    {
        $this->rawError = $rawError;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(?string $openId): void
    {
        $this->openId = $openId;
    }

    public function getUnionId(): ?string
    {
        return $this->unionId;
    }

    public function setUnionId(?string $unionId): void
    {
        $this->unionId = $unionId;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
