<?php

namespace App\Message;

use Symfony\Component\HttpFoundation\Request;

final class AddToFavourites
{
    public function __construct(protected string $userId, protected string $sourceId, protected string $id)
    {
        // Constructor logic here
    }

    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    public function getId(): string
    {
        return $this->id;
    }

	public function getUserId(): string
    {
        return $this->userId;
    }
}
