<?php

// src/EventListener/UserDataListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Bundle\SecurityBundle\Security;
use App\Controller\BaseController;

class UserDataListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $user = $this->security->getUser();
		
        $controller = $event->getController();
        if (is_array($controller) && isset($controller[0]) && $controller[0] instanceof BaseController) {
            $controller[0]->setUser($user);
        }
    }
}

