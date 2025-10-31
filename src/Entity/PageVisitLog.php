<?php

declare(strict_types=1);

namespace WechatMiniProgramTrackingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserBundle\Traits\CreatedByAware;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;
use WechatMiniProgramTrackingBundle\Repository\PageVisitLogRepository;

#[AsScheduleClean(expression: '30 2 * * *', defaultKeepDay: 3, keepDayEnv: 'PAGE_VISIT_LOG_PERSIST_DAY_NUM')]
#[ORM\Entity(repositoryClass: PageVisitLogRepository::class)]
#[ORM\Table(name: 'json_rpc_page_log', options: ['comment' => '页面请求日志'])]
#[ORM\UniqueConstraint(name: 'json_rpc_page_log_idx_uniq', columns: ['session_id', 'route_id'])]
class PageVisitLog implements \Stringable
{
    use CreatedByAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[IndexColumn]
    #[ORM\Column(length: 255, options: ['comment' => '页面路径'])]
    private string $page;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '路由ID'])]
    private int $routeId;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, options: ['comment' => '会话ID'])]
    private string $sessionId;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Valid]
    #[ORM\Column(type: Types::JSON, length: 255, nullable: true, options: ['comment' => '参数'])]
    private ?array $query = null;

    #[Assert\Length(max: 500)]
    #[CreateUserAgentColumn]
    private ?string $createdFromUa = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeImmutable $createTime = null;

    #[Assert\Length(max: 45)]
    #[ORM\Column(length: 45, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    public function getPage(): string
    {
        return $this->page;
    }

    public function setPage(string $page): void
    {
        $this->page = $page;
    }

    public function getRouteId(): int
    {
        return $this->routeId;
    }

    public function setRouteId(int $routeId): void
    {
        $this->routeId = $routeId;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getQuery(): ?array
    {
        return $this->query;
    }

    public function getQueryAsJson(): string
    {
        if (null === $this->query || [] === $this->query) {
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
        $this->query = $query;
    }

    public function getCreatedFromUa(): ?string
    {
        return $this->createdFromUa;
    }

    public function setCreatedFromUa(?string $createdFromUa): void
    {
        $this->createdFromUa = $createdFromUa;
    }

    public function setCreateTime(?\DateTimeImmutable $createTime): void
    {
        $this->createTime = $createTime;
    }

    public function getCreateTime(): ?\DateTimeImmutable
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
