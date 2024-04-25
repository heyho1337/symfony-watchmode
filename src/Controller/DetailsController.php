<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\WmService;
use App\Form\AddToFavouriteType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class DetailsController extends BaseController
{

	public function __construct(protected WmService $wm,
		protected Security $security, 
		protected RequestStack $requestStack)
    {
		parent::__construct($security, $requestStack);
    }
	
	#[Route('/details/{sourceId}/{id}', name: 'app_details')]
    public function index(string $sourceId, string $id): Response
    {
		if ($response = $this->validAuth()) {
            return $response;
        }
		$details = $this->wm->details($id);
		$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['id' => $id, 'sourceId' => $sourceId])]);
		return $this->render('details/index.html.twig', [
            'controller_name' => 'DetailsController',
			'id' => $id,
			'details' => $details,
			'form' => $form->createView(),
			'user' => $this->getUser()
        ]);
    }
}
