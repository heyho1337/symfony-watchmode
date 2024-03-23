<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\WmService;

class DetailsController extends AbstractController
{
    
	public function __construct(protected WmService $wm)
    {
       
    }
	
	#[Route('/details/{id}', name: 'app_details')]
    public function index(string $id): Response
    {
        
		$details = $this->wm->details($id);
		return $this->render('details/index.html.twig', [
            'controller_name' => 'DetailsController',
			'id' => $id,
			'details' => $details
        ]);
    }
}
