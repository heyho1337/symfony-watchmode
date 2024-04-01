<?php

namespace App\MessageHandler;

use App\Message\Test;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TestHandler
{
    public function __invoke(Test $message)
    {
        // do something with your message
    }
}
