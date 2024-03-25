<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\WmService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\AddToFavouriteType;

class StreamingController extends AbstractController
{
    
	public function __construct(protected WmService $wm)
    {
       
    }
	
	#[Route('/streaming/{id}', name: 'app_streaming')]
    public function index(string $id): Response
    {
        
		$streamingRow = $this->wm->row($id);
		$genreList = $this->wm->genreList();
		return $this->render('streaming/index.html.twig', [
            'controller_name' => 'StreamingController',
			'streamingRow' => $streamingRow,
			'genreList' => $genreList,
			'sourceId' => $id
        ]);
    }

	#[Route('/content/{sourceIds}', name: 'app_contentSourceList', methods:['GET'])]
	public function contentSourceList(string $sourceIds): JsonResponse
	{
		$result = $this->wm->query("list-titles?source_ids={$sourceIds}");
		$titles = $this->setAddToFavouritesForm($result['titles']);
		return $this->json($titles);
	}

	#[Route('/content/{sourceIds}/{genreIds}', name: 'app_contentSourceGenreList', methods:['get'])]
	public function contentSourceGenreList(string $sourceIds, string $genreIds): JsonResponse
	{
		$result = $this->wm->query("list-titles?source_ids={$sourceIds}&genres={$genreIds}");
		$titles = $this->setAddToFavouritesForm($result['titles']);
		return $this->json($titles);
	}

	protected function setAddToFavouritesForm($titles){
		foreach($titles as &$title){
			$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['id' => $title['id']])]);
			$formHtml = $this->renderView('favourites/AddToFavourites.html.twig', [
				'form' => $form->createView(),
			]);
			$title['form'] = $formHtml;
		}
		return $titles;
	}

}
