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

	#[ORM\Column(length: 255)]
    private ?string $favSourceId = null;

	#[ORM\Column(length: 255)]
    private ?string $favSourceName = null;

    #[ORM\Column(length: 100)]
    private ?string $favType = null;

	public ?string $form = null;
    public ?array $details = null;

    public function getFavId(): ?int
    {
        return $this->favId;
    }

	public function getForm(): ?string
    {
        return $this->form;
    }

    public function setForm(string $form): static
    {
        $this->form = $form;
        return $this;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(array $details): static
    {
        $this->details = $details;
        return $this;
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

	public function getFavSourceId(): ?string
    {
        return $this->favSourceId;
    }

    public function setFavSourceId(string $favSourceId): static
    {
        $this->favSourceId = $favSourceId;

        return $this;
    }

	public function getFavSourceName(): ?string
    {
        return $this->favSourceName;
    }

    public function setFavSourceName(string $favSourceName): static
    {
        $this->favSourceName = $favSourceName;

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
