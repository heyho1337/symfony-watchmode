<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\WmService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\AddToFavouriteType;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\SecurityBundle\Security;

class StreamingController extends BaseController
{
    
	public function __construct(protected WmService $wm, 
		protected LoggerInterface $logger,
		protected Security $security, 
		protected RequestStack $requestStack)
    {
		parent::__construct($security, $requestStack);
    }
	
	#[Route('/streaming/{id}', name: 'app_streaming')]
    public function index(string $id): Response
    {
        
		if ($response = $this->validAuth()) {
            return $response;
        }
		
		$streamingRow = $this->wm->row($id);
		$genreList = $this->wm->genreList();
		$recent = $this->wm->recent($id);
		foreach($recent as &$row){
			$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['sourceId' => $id, 'id' => $row['id']])]);
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
			'recent' => $recent,
			'user' => $this->getUser()
        ]);
    }

	#[Route('/content/{sourceIds}', name: 'app_contentSourceList', methods:['GET'])]
	public function contentSourceList(string $sourceIds): JsonResponse
	{
		$result = $this->wm->query("list-titles?source_ids={$sourceIds}");
		return $this->setTitles($result['titles'],$sourceIds);
	}

	#[Route('/content/{sourceIds}/{genreIds}', name: 'app_contentSourceGenreList', methods:['GET'])]
	public function contentSourceGenreList(string $sourceIds, string $genreIds): JsonResponse
	{
		$result = $this->wm->query("list-titles?source_ids={$sourceIds}&genres={$genreIds}");
		return $this->setTitles($result['titles'],$sourceIds);
	}

	protected function setTitles(array $titles,string $id): JsonResponse
	{
		foreach($titles as &$title){
			$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['sourceId' => $id, 'id' => $title['id']])]);
			$formHtml = $this->renderView('favourites/AddToFavourites.html.twig', [
				'form' => $form->createView(),
			]);
			$title['twig'] = $this->render('title/title.html.twig', [
				'item' => $title,
				'form' => $formHtml,
				'sourceId' => $id,
			])->getContent();
		}
		$this->logger->error($this->json($titles));
		return $this->json($titles);
	}

}
