<?php

namespace WechatMiniProgramTrackingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Exportable;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;
use WechatMiniProgramTrackingBundle\Repository\PageVisitLogRepository;

#[AsScheduleClean(expression: '30 2 * * *', defaultKeepDay: 3, keepDayEnv: 'PAGE_VISIT_LOG_PERSIST_DAY_NUM')]
#[Exportable]
#[ORM\Entity(repositoryClass: PageVisitLogRepository::class)]
#[ORM\Table(name: 'json_rpc_page_log', options: ['comment' => '页面请求日志'])]
#[ORM\Index(columns: ['page'], name: 'idx_page')]
#[ORM\UniqueConstraint(name: 'json_rpc_page_log_idx_uniq', columns: ['session_id', 'route_id'])]
class PageVisitLog implements Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    private string $page;

    private ?int $routeId;

    private string $sessionId;

    #[ORM\Column(type: Types::JSON, length: 255, nullable: true, options: ['comment' => '参数'])]
    private ?array $query = null;

    #[CreateUserAgentColumn]
    private ?string $createdFromUa = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[ORM\Column(length: 45, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[CreatedByColumn]
    private ?string $createdBy = null;

    public function setCreatedBy(?string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function setPage(string $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getRouteId(): int
    {
        return $this->routeId;
    }

    public function setRouteId(int $routeId): self
    {
        $this->routeId = $routeId;

        return $this;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): static
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getQuery(): ?array
    {
        return $this->query;
    }

    public function setQuery(?array $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function getCreatedFromUa(): ?string
    {
        return $this->createdFromUa;
    }

    public function setCreatedFromUa(?string $createdFromUa): static
    {
        $this->createdFromUa = $createdFromUa;

        return $this;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): self
    {
        $this->createTime = $createdAt;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): void
    {
        $this->createdFromIp = $createdFromIp;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
