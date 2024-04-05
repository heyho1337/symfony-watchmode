<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    
	private $user;
	
	public function setUser($user)
    {
        $this->user = $user;
    }
}
