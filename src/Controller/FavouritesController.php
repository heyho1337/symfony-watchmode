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
class FavouritesController extends AbstractController
{
    
	public function __construct(
		protected WmService $wm, 
		protected EntityManagerInterface $entityManager, 
		protected LoggerInterface $logger, 
		protected UserFavouritesRepository $userFavouritesRepository)
    {
		
    }

	#[Route('/favourites', name: 'app_favourites')]
    public function index(): Response
    {
		$favourites = $this->userFavouritesRepository->findByGroupedFavSourceId($this->user->getUserId());
		foreach($favourites as &$groups){
			foreach($groups as &$title){
				$details = $this->wm->details($title->getFavStreamId());
				$form = $this->createForm(RemoveFromFavouriteType::class, null, ['action' => $this->generateUrl('remove_from_favourites', ['id' => $title->getFavId()])]);
				$formHtml = $this->renderView('favourites/RemoveFromFavourites.html.twig', [
					'form' => $form->createView(),
				]);
				$title->form = $formHtml;
				$title->details = $details;
			}
		}
		//var_dump($favourites);
		return $this->render('favourites/index.html.twig', [
			'favourites' => $favourites,
			'user' => $this->user
        ]);
    }

	#[Route('/details/{id}/remove', name: 'remove_from_favourites', methods: ['POST'])]
	public function removeFromFavourites(string $id): Response
	{
		try {
			// Find the UserFavourites entity with the given favUserId and favStreamId
			$userFavourite = $this->entityManager->getRepository(UserFavourites::class)->findOneBy([
				'favUserId' => $this->user->getUserId(),
				'favId' => $id,
			]);

			// If the entity exists, remove it from the database
			if ($userFavourite) {
				$this->entityManager->remove($userFavourite);
				$this->entityManager->flush();

				$dialog = new DialogController();
				return $this->json(['result' => $dialog->showDialog('Removed from Favourites')]);
			} else {
				return $this->json(['result' => 'Favourite not found'], Response::HTTP_NOT_FOUND);
			}
		} catch (\Exception $e) {
			// Log the exception
			$this->logger->error('An error occurred: ' . $e->getMessage());

			// Return a JSON response with an error message
			return $this->json(['result' => 'An error occurred while processing the request'], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	#[Route('/details/{sourceId}/{id}/save', name: 'save_to_favourites', methods: ['POST'])]
    public function addToFavourites(Request $request, string $sourceId, string $id): Response
    {
		try {
            // Create a new instance of the form
			$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['sourceId' => $sourceId, 'id' => $id])]);
            // Handle form submission
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
				$details = $this->wm->details($id);
				$streamingRow = $this->wm->row($sourceId);
                $userFavourite = new UserFavourites();
                // Set properties of the UserFavourites entity
                $userFavourite->setFavUserId($this->user->getUserId());
                $userFavourite->setFavStreamId($id);
				$userFavourite->setFavSourceId($sourceId);
				$userFavourite->setFavSourceName($streamingRow['name']);
                $userFavourite->setFavType(json_encode($details['genres'])); // Assuming getGenres() returns the correct value

                // Persist the entity
                $this->entityManager->persist($userFavourite);
                $this->entityManager->flush();

				$dialog = new DialogController();
                return $this->json(['result' => $dialog->showDialog('Added to Favourites')]);
            }

            // If form submission fails or is invalid, return a JSON response with errors
            return $this->json(['result' => 'Form submission failed'], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Log the exception
            $this->logger->error('An error occurred: ' . $e->getMessage());

            // Return a JSON response with an error message
            return $this->json(['result' => 'An error occurred while processing the form'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
