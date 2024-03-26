<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\WmService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\AddToFavouriteType;
use Psr\Log\LoggerInterface;

class StreamingController extends AbstractController
{
    
	public function __construct(protected WmService $wm, protected LoggerInterface $logger)
    {
       
    }
	
	#[Route('/streaming/{id}', name: 'app_streaming')]
    public function index(string $id): Response
    {
        
		$streamingRow = $this->wm->row($id);
		$genreList = $this->wm->genreList();
		$recent = $this->wm->recent($id);
		foreach($recent as &$row){
			$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['id' => $row['id']])]);
			$formHtml = $this->renderView('favourites/AddToFavourites.html.twig', [
				'form' => $form->createView(),
			]);
			$row['form'] = $formHtml;
		}
		return $this->render('streaming/index.html.twig', [
            'controller_name' => 'StreamingController',
			'streamingRow' => $streamingRow,
			'genreList' => $genreList,
			'sourceId' => $id,
			'recent' => $recent 
        ]);
    }

	#[Route('/content/{sourceIds}', name: 'app_contentSourceList', methods:['GET'])]
	public function contentSourceList(string $sourceIds): JsonResponse
	{
		$result = $this->wm->query("list-titles?source_ids={$sourceIds}");
		return $this->setTitles(($result['titles']));
	}

	#[Route('/content/{sourceIds}/{genreIds}', name: 'app_contentSourceGenreList', methods:['get'])]
	public function contentSourceGenreList(string $sourceIds, string $genreIds): JsonResponse
	{
		$result = $this->wm->query("list-titles?source_ids={$sourceIds}&genres={$genreIds}");
		return $this->setTitles(($result['titles']));
	}

	protected function setTitles(array $titles): JsonResponse
	{
		foreach($titles as &$title){
			$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['id' => $title['id']])]);
			$formHtml = $this->renderView('favourites/AddToFavourites.html.twig', [
				'form' => $form->createView(),
			]);
			$title['twig'] = $this->render('title/title.html.twig', [
				'item' => $title,
				'form' => $formHtml
			])->getContent();
		}
		$this->logger->error($this->json($titles));
		return $this->json($titles);
	}

}
