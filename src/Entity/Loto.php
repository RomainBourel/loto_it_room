<?php

namespace App\Entity;

use App\Repository\LotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LotoRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Loto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $additionalNumber = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $drawingAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $ballArray = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdditionalNumber(): ?int
    {
        return $this->additionalNumber;
    }

    public function setAdditionalNumber(int $additionalNumber): self
    {
        $this->additionalNumber = $additionalNumber;

        return $this;
    }

    public function getDrawingAt(): ?\DateTimeImmutable
    {
        return $this->drawingAt;
    }

    public function setDrawingAt(\DateTimeImmutable $drawingAt): self
    {
        $this->drawingAt = $drawingAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getBallArray(): array
    {
        return $this->ballArray;
    }

    public function setBallArray(array $ballArray): self
    {
        $this->ballArray = $ballArray;

        return $this;
    }
}
