<?php

// src/EventListener/UserDataListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Bundle\SecurityBundle\Security;

class UserDataListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelController(ControllerEvent $event)
    {
        // Retrieve the currently authenticated user (if any)
        $user = $this->security->getUser();

        // Inject the user data into the controller arguments
        $controller = $event->getController();
        if (is_array($controller) && isset($controller[0])) {
            $controller[0]->user = $user;
        }
    }
}
