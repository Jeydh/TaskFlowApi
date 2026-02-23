<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\State\TaskStateProcessor;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Enum\TaskStatus;
use App\Enum\TaskPriority;

#[ApiResource(
    operations: [
        new Get(
            security: 
            "is_granted('ROLE_USER') and 
            (object.getCreatedBy() == user or object.getAssignedTo() == user)",
            securityMessage: "You can only access tasks you created or that are assigned to you."
        ),
        new GetCollection(
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            security: "is_granted('ROLE_USER')"
        ),
        new Patch(
            security: 
            "is_granted('ROLE_USER') and 
            (object.getCreatedBy() == user)",
            securityMessage: "You can only edit tasks you created."
        ),
        new Delete(
            security: 
            "is_granted('ROLE_USER') and
            (object.getCreatedBy() == user)",
            securityMessage: "You can only delete tasks you created."
        ),
    ],
    normalizationContext: ['groups' => ['task:read']],
    denormalizationContext: ['groups' => ['task:write']],
    processor: TaskStateProcessor::class
)]

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['task:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['task:read', 'task:write'])]
    #[Assert\NotBlank(message: "Title should not be blank.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Title must be at least {{ limit }} characters long.",
        maxMessage: "Title cannot be longer than {{ limit }} characters."
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['task:read', 'task:write'])]
    #[Assert\Length(
        min: 5,
        max: 5000,
        minMessage: "Description must be at least {{ limit }} characters long.",
        maxMessage: "Description cannot be longer than {{ limit }} characters."
    )]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['task:read', 'task:write'])]
    #[Assert\NotBlank(message: "Status should not be blank.")]
    #[Assert\Choice(
        choices: TaskStatus::VALUES,
        message: "Status must be one of: {{ choices }}."
    )]
    private ?string $status = null;

    #[ORM\Column(length: 50)]
    #[Groups(['task:read', 'task:write'])]
    #[Assert\NotBlank(message: "Priority should not be blank.")]
    #[Assert\Choice(
        choices: TaskPriority::VALUES,
        message: "Priority must be one of: {{ choices }}."
    )]
    private ?string $priority = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['task:read', 'task:write'])]
    #[Assert\Type("\DateTimeInterface", message: "Due date must be a valid datetime.")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "Due date cannot be in the past."
    )]
    private ?\DateTimeImmutable $dueDate = null;

    #[ORM\Column]
    #[Groups(['task:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['task:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'assignedTasks')]
    #[Groups(['task:read', 'task:write'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    private ?User $assignedTo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = trim($title);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description !== null ? trim($description) : null;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status !== null ? TaskStatus::normalize($status) : null;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): static
    {
        $this->priority = $priority !== null ? TaskPriority::normalize($priority) : null;

        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeImmutable $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }
    
    #[Groups(['task:read'])]
    public function getCreatedById(): ?int
    {
        return $this->createdBy?->getId();
    } 

    #[Groups(['task:read'])]
    public function getCreatedByEmail(): ?string
    {
        return $this->createdBy?->getEmail();
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }
}
