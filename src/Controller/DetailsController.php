<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\WmService;
use App\Form\AddToFavouriteType;

class DetailsController extends AbstractController
{

	public function __construct(protected WmService $wm)
    {
		
    }
	
	#[Route('/details/{id}', name: 'app_details')]
    public function index(string $id): Response
    {
		$details = $this->wm->details($id);
		$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['id' => $id])]);
		return $this->render('details/index.html.twig', [
            'controller_name' => 'DetailsController',
			'id' => $id,
			'details' => $details,
			'form' => $form->createView(),
        ]);
    }
}
