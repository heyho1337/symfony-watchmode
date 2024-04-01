<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
use App\Message\Test;
use Symfony\Component\Messenger\MessageBusInterface;
class FavouritesController extends AbstractController
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
    public function index(MessageBusInterface $messageBus): Response
	{
		
        $messageBus->dispatch(new Test('1234567890', 'Hello from Symfony!'));
		$cacheKey = md5("favourites_cache");
		$favourites = $this->cache->get($cacheKey, function (ItemInterface $item){
            $item->expiresAfter((int)$_ENV['CACHE']);
            $this->logger->info('Cache miss: Populating favourites from database');
            return $this->setFavourites();
        });

		// Return the rendered template with the cached $favourites data
		return $this->render('favourites/index.html.twig', [
			'favourites' => $favourites,
			'user' => $this->user
		]);
	}

	public function setFavourites(){
		$this->logger->info('Cache miss: setting data');
		$data = $this->userFavouritesRepository->findByGroupedFavSourceId($this->user->getUserId());
		foreach ($data as &$groups) {
			foreach ($groups as &$title) {
				$details = $this->wm->details($title->getFavStreamId());
				$form = $this->createForm(RemoveFromFavouriteType::class, null, ['action' => $this->generateUrl('remove_from_favourites', ['id' => $title->getFavId()])]);
				$formHtml = $this->renderView('favourites/RemoveFromFavourites.html.twig', [
					'form' => $form->createView(),
				]);
				$title->form = $formHtml;
				$title->details = $details;
			}
		}
		return $data;
	}

	#[Route('/details/{id}/remove', name: 'remove_from_favourites', methods: ['POST'])]
	public function removeFromFavourites(string $id): Response
	{
		try {
			$userFavourite = $this->entityManager->getRepository(UserFavourites::class)->findOneBy([
				'favUserId' => $this->user->getUserId(),
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
    public function addToFavourites(Request $request, string $sourceId, string $id): Response
    {
		try {
			$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['sourceId' => $sourceId, 'id' => $id])]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
				$details = $this->wm->details($id);
				$streamingRow = $this->wm->row($sourceId);
                $userFavourite = new UserFavourites();
                $userFavourite->setFavUserId($this->user->getUserId());
                $userFavourite->setFavStreamId($id);
				$userFavourite->setFavSourceId($sourceId);
				$userFavourite->setFavSourceName($streamingRow['name']);
                $userFavourite->setFavType(json_encode($details['genres'])); // Assuming getGenres() returns the correct value

                $this->entityManager->persist($userFavourite);
                $this->entityManager->flush();
				$this->cache->delete('favourites_cache');

				$dialog = new DialogController();
                return $this->json(['result' => $dialog->showDialog('Added to Favourites')]);
            }
            return $this->json(['result' => 'Form submission failed'], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error('An error occurred: ' . $e->getMessage());
            return $this->json(['result' => 'An error occurred while processing the form'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
