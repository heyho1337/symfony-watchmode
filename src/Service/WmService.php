<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

class WmService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = $_ENV['WM_APIKEY'];
        $this->apiUrl = $_ENV['WM_APIURL'];
    }

    public function list(): array{
		return $this->query("sources");
	}
	
	protected function query(string $url): array
    {
        $client = HttpClient::create();
        
        try {
            $response = $client->request('GET', "{$this->apiUrl}/{$url}", [
                'query' => [
                    'apiKey' => $this->apiKey,
                ],
            ]);
            
            $content = $response->getContent();
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                return $response->toArray();
            } else {
                throw new \RuntimeException('Error fetching data from Watchmode API: ' . $content);
            }
        } catch (ExceptionInterface $e) {
            throw new \RuntimeException('Error connecting to Watchmode API: ' . $e->getMessage());
        }
    }

}
