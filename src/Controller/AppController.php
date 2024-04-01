<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Service\WmService;
class AppController extends AbstractController
{

    public function __construct(protected Security $security, protected WmService $wm)
    {
       
    }
	
	#[Route('/', name: 'app_home')]
    public function index(): Response
    {
        
	//phpinfo();
	// Check if the user is authenticated
        if ($this->security->isGranted('ROLE_USER')) {
            // If the user is authenticated, render authenticated homepage
			$streamingList = $this->wm->streamingList();
			
            return $this->render('app/index.html.twig', [
                'controller_name' => 'AppController',
				'streamingList' => $streamingList,
				'user' => $this->user
            ]);
        } else {
            // If the user is not authenticated, render non-authenticated homepage
            return $this->render('app/nonauth.html.twig', [
                'controller_name' => 'AppController',
            ]);
        }
    }
}
