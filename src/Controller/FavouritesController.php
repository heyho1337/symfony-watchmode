<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Controller\DialogController;
use App\Entity\UserFavourites;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Service\WmService;
use App\Form\AddToFavouriteType;
use App\Form\RemoveFromFavouriteType;
use App\Repository\UserFavouritesRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use App\Message\AddToFavourites;
use Symfony\Component\Messenger\MessageBusInterface;
class FavouritesController extends BaseController
{
    
	protected FilesystemAdapter $cache;
	
	public function __construct(
		protected WmService $wm, 
		protected EntityManagerInterface $entityManager, 
		protected LoggerInterface $logger, 
		protected UserFavouritesRepository $userFavouritesRepository)
    {
		$this->cache = new FilesystemAdapter();
    }

	#[Route('/favourites', name: 'app_favourites')]
    public function index(): Response
	{
		$cacheKey = md5("favourites_cache");
		$favourites = $this->cache->get($cacheKey, function (ItemInterface $item){
            $item->expiresAfter((int)$_ENV['CACHE']);
            $this->logger->info('Cache miss: Populating favourites from database');
            return $this->setFavourites();
        });

		// Return the rendered template with the cached $favourites data
		return $this->render('favourites/index.html.twig', [
			'favourites' => $favourites,
			'user' => $this->getUser()
		]);
	}

	public function setFavourites(){
		$this->logger->info('Cache miss: setting data');
		$data = $this->userFavouritesRepository->findByGroupedFavSourceId($this->getUser()->getUserId());
		foreach ($data as &$groups) {
			foreach ($groups as &$title) {
				$details = $this->wm->details($title->getFavStreamId());
				$form = $this->createForm(RemoveFromFavouriteType::class, null, ['action' => $this->generateUrl('remove_from_favourites', ['id' => $title->getFavId()])]);
				$formHtml = $this->renderView('favourites/RemoveFromFavourites.html.twig', [
					'form' => $form->createView(),
				]);
				$title->setForm($formHtml);
				$title->setDetails($details);
			}
		}
		return $data;
	}

	#[Route('/details/{id}/remove', name: 'remove_from_favourites', methods: ['POST'])]
	public function removeFromFavourites(string $id): Response
	{
		try {
			$userFavourite = $this->entityManager->getRepository(UserFavourites::class)->findOneBy([
				'favUserId' => $this->getUser()->getUserId(),
				'favId' => $id,
			]);

			if ($userFavourite) {
				$this->entityManager->remove($userFavourite);
				$this->entityManager->flush();
				$this->cache->delete('favourites_cache');

				$dialog = new DialogController();
				return $this->json(['result' => $dialog->showDialog('Removed from Favourites')]);
			} else {
				return $this->json(['result' => 'Favourite not found'], Response::HTTP_NOT_FOUND);
			}
		} catch (\Exception $e) {
			$this->logger->error('An error occurred: ' . $e->getMessage());

			return $this->json(['result' => 'An error occurred while processing the request'], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	#[Route('/details/{sourceId}/{id}/save', name: 'save_to_favourites', methods: ['POST'])]
    public function addToFavourites(MessageBusInterface $messageBus, Request $request, string $sourceId, string $id): Response
    {
		$form = $this->createForm(AddToFavouriteType::class);
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
			$userId = $this->getUser()->getUserId();
			$messageBus->dispatch(new AddToFavourites($userId, $sourceId, $id));
			$dialog = new DialogController();
            return $this->json(['result' => $dialog->showDialog('Added to Favourites')]);
		}
    }
}
