<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\WmService;

use function PHPUnit\Framework\fileExists;

class ImageController extends AbstractController
{
    
	public function __construct(protected WmService $wm)
    {
       
    }

	#[Route('/imagewebp', name: 'app_webp')]
	public function webp(): Response
	{
		//$streamingList = $this->wm->streamingList();
		$publicDirectory = $this->getParameter('kernel.project_dir') . '/public/';
		$dir = $publicDirectory . 'images/streamLogos/';
		$pngFiles = glob($dir . '*.png');

		foreach ($pngFiles as $pngFile) {
			// Generate the WebP file path
			$webpFile = pathinfo($pngFile, PATHINFO_DIRNAME) . '/' . pathinfo($pngFile, PATHINFO_FILENAME) . '.webp';
		
			// Check if the WebP file doesn't already exist
			if (!file_exists($webpFile)) {
				// Convert the image to true-color format before converting to WebP
				$image = @imagecreatefrompng($pngFile);
				if (!$image) {
					// Skip to the next image if PNG creation fails
					continue;
				}
				// Proceed with WebP conversion
				$trueColorImage = imagecreatetruecolor(imagesx($image), imagesy($image));
				imagecopy($trueColorImage, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
				imagedestroy($image);
		
				// Convert the true-color image to WebP format
				imagewebp($trueColorImage, $webpFile, 80); // 80 is the quality parameter (0-100)
				imagedestroy($trueColorImage);
			}
		}
		return $this->render('image/index.html.twig', [
			'controller_name' => 'ImageController',
		]);
	}
	
	#[Route('/imagepng', name: 'app_png')]
	public function png(): Response
	{
		$streamingList = $this->wm->streamingList();
		$publicDirectory = $this->getParameter('kernel.project_dir') . '/public/';
		$dir = $publicDirectory . 'images/streamLogos/';

		foreach ($streamingList as $row) {
			$logoUrl = $row['logo_100px'];
			$target = $dir.$row['ios_scheme']."_".$row['id'].".png";
			if (!file_exists($target)) {
				if (!@file_put_contents($target, file_get_contents($logoUrl))) {
                    continue;
                }
			}
		}

		return $this->render('image/index.html.twig', [
			'controller_name' => 'ImageController',
		]);
	}
}
