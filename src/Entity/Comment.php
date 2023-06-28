<?php
/**
 * Comment Entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: "comments")]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: '\DateTimeInterface')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: '\DateTimeInterface')]
    private DateTimeInterface $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: true)]
    private $author;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'comments', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private $task;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 3, max: 255)]
    private $content;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $nickname = null;

    #[ORM\Column(nullable: true)]
    private ?bool $bestAnswer = null;

    /**
     * Getter for Id.
     *
     * @return int|null Result
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for Created At.
     *
     * @return \DateTimeInterface|null Created at
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for Created at.
     *
     * @param \DateTimeInterface $createdAt Created at
     */
    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for Updated at.
     *
     * @return \DateTimeInterface|null Updated at
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }


    /**
     * Setter for Updated at.
     *
     * @param \DateTimeInterface $updatedAt Updated at
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for author.
     *
     * @return \App\Entity\User|null User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param \App\Entity\User|null $author User
     *
     * @return \App\Entity\User|null User
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    /**
     * Getter for task.
     *
     * @return \App\Entity\Task|null Task
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * Setter for task.
     *
     * @param \App\Entity\Task|null $task Task
     */
    public function setTask(?Task $task): void
    {
        $this->task = $task;
    }

    /**
     * Getter for Content
     *
     * @return string|null Content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for Content.
     *
     * @param string $content Content
     *
     * @return string|null Content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function isBestAnswer(): ?bool
    {
        return $this->bestAnswer;
    }

    public function setBestAnswer(?bool $bestAnswer): static
    {
        $this->bestAnswer = $bestAnswer;

        return $this;
    }
}