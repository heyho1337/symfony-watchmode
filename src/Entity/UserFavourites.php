<?php

namespace App\Entity;

use App\Repository\UserFavouritesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: UserFavouritesRepository::class)]
#[Broadcast]
class UserFavourites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $favId = null;

    #[ORM\Column]
    private ?int $favUserId = null;

    #[ORM\Column(length: 255)]
    private ?string $favStreamId = null;

    #[ORM\Column(length: 100)]
    private ?string $favType = null;

    public function getFavId(): ?int
    {
        return $this->favId;
    }

    public function getFavUserId(): ?int
    {
        return $this->favUserId;
    }

    public function setFavUserId(int $favUserId): static
    {
        $this->favUserId = $favUserId;

        return $this;
    }

    public function getFavStreamId(): ?string
    {
        return $this->favStreamId;
    }

    public function setFavStreamId(string $favStreamId): static
    {
        $this->favStreamId = $favStreamId;

        return $this;
    }

    public function getFavType(): ?string
    {
        return $this->favType;
    }

    public function setFavType(string $favType): static
    {
        $this->favType = $favType;

        return $this;
    }
}
