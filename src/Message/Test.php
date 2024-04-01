<?php

namespace App\Message;

final class Test
{
    private $phoneNumber;
    private $text;

    public function __construct(string $phoneNumber, string $text)
    {
        $this->phoneNumber = $phoneNumber;
        $this->text = $text;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
