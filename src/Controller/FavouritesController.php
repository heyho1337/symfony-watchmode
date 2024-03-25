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
class FavouritesController extends AbstractController
{
    
	public function __construct(protected WmService $wm, protected EntityManagerInterface $entityManager, protected LoggerInterface $logger)
    {
		
    }

	#[Route('/details/{id}/save', name: 'save_to_favourites', methods: ['POST'])]
    public function sendDetailData(Request $request, string $id): Response
    {
		try {
            // Create a new instance of the form
			$form = $this->createForm(AddToFavouriteType::class, null, ['action' => $this->generateUrl('save_to_favourites', ['id' => $id])]);
            // Handle form submission
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
				$details = $this->wm->details($id);
                $userFavourite = new UserFavourites();
                // Set properties of the UserFavourites entity
                $userFavourite->setFavUserId($this->user->getUserId());
                $userFavourite->setFavStreamId($id);
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
