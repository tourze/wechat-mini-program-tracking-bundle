<?php

namespace WechatMiniProgramTrackingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\UpdateUserAgentColumn;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Entity\LaunchOptionsAware;
use WechatMiniProgramTrackingBundle\Repository\PageNotFoundLogRepository;

#[ORM\Entity(repositoryClass: PageNotFoundLogRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_page_not_found_log', options: ['comment' => '404页面日志'])]
class PageNotFoundLog implements Stringable
{
    use TimestampableAware;
    use LaunchOptionsAware;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[CreateUserAgentColumn]
    private ?string $createdFromUa = null;

    #[UpdateUserAgentColumn]
    private ?string $updatedFromUa = null;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Account $account = null;

    #[ORM\Column(type: Types::STRING, length: 1000, options: ['comment' => '路径'])]
    private ?string $path = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '打开类型'])]
    private ?string $openType = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '参数'])]
    private array $query = [];

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '原始错误'])]
    private ?string $rawError = null;

#[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '字段说明'])]
    private ?string $openId = null;

#[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '字段说明'])]
    private ?string $unionId = null;

    #[CreateIpColumn]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    private ?string $updatedFromIp = null;

    public function getId(): ?string
    {
        return $this->id;
    }

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

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getOpenType(): ?string
    {
        return $this->openType;
    }

    public function setOpenType(?string $openType): self
    {
        $this->openType = $openType;

        return $this;
    }

    public function getQuery(): ?array
    {
        return $this->query;
    }

    public function setQuery(?array $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getRawError(): ?string
    {
        return $this->rawError;
    }

    public function setRawError(?string $rawError): self
    {
        $this->rawError = $rawError;

        return $this;
    }

    public function getOpenId(): ?string
    {
        return $this->openId;
    }

    public function setOpenId(?string $openId): self
    {
        $this->openId = $openId;

        return $this;
    }

    public function getUnionId(): ?string
    {
        return $this->unionId;
    }

    public function setUnionId(?string $unionId): self
    {
        $this->unionId = $unionId;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
