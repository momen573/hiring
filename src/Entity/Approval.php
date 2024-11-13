<?php

namespace App\Entity;

use App\Repository\ApprovalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ApprovalRepository::class)]
#[ORM\Table(name: 'approval')]
class Approval
{
    // Status constants
    final public const STATUS_REJECTED = 'rejected';
    final public const STATUS_ACCEPTED = 'accepted';
    final public const STATUS_DRAFT = 'draft';
    final public const STATUS_WAITING = 'waiting'; // Added waiting status

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $changedAt;

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\NotBlank]
    private string $changeTo;

    public function __construct()
    {
        // Initialize fields
        $this->changedAt = new \DateTimeImmutable();
        $this->changeTo = self::STATUS_DRAFT; // Default to draft
    }

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    public function getChangedAt(): \DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function setChangedAt(\DateTimeImmutable $changedAt): void
    {
        $this->changedAt = $changedAt;
    }

    public function getChangeTo(): string
    {
        return $this->changeTo;
    }

    public function setChangeTo(string $changeTo): void
    {
        // Validate the status
        if (!in_array($changeTo, [self::STATUS_REJECTED, self::STATUS_ACCEPTED, self::STATUS_DRAFT, self::STATUS_WAITING])) {
            throw new \InvalidArgumentException('Invalid status');
        }
        $this->changeTo = $changeTo;
    }

    public function __serialize(): array
    {
        return [$this->id, $this->user, $this->post, $this->changedAt, $this->changeTo];
    }

    public function __unserialize(array $data): void
    {
        [$this->id, $this->user, $this->post, $this->changedAt, $this->changeTo] = $data;
    }
}
