<?php

namespace App\MessageHandler;

use App\Message\AddToFavourites;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\WmService;
use App\Entity\UserFavourites;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddToFavouritesHandler
{
    protected FilesystemAdapter $cache;
	
	public function __construct(
        protected WmService $wm,
        protected EntityManagerInterface $entityManager,
        protected LoggerInterface $logger
    ) {
		$this->cache = new FilesystemAdapter();
    }

    public function __invoke(AddToFavourites $message)
    {
		$this->logger->info('AddToFavourites invoked');
		// Extract data from the message
        $sourceId = $message->getSourceId();
        $id = $message->getId();
		$userId = $message->getUserId();

        // Fetch details and row from WmService
        $details = $this->wm->details($id);
        $streamingRow = $this->wm->row($sourceId);
        $genres = $details['genres'];

        // Create and persist UserFavourites entity
        $userFavourite = new UserFavourites();
        $userFavourite->setFavUserId($userId);
        $userFavourite->setFavStreamId($id);
        $userFavourite->setFavSourceId($sourceId);
        $userFavourite->setFavSourceName($streamingRow['name']);
        $userFavourite->setFavType(json_encode($genres));

        $this->entityManager->persist($userFavourite);
        $this->entityManager->flush();

        // Clear cache
        $this->cache->delete('favourites_cache');

        // Log success message
        $this->logger->info('Added to Favourites: Source ID: ' . $sourceId . ', Stream ID: ' . $id);

        // Return response if needed
    }
}
