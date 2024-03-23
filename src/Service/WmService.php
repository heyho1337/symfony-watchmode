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

    public function streamingList(): array
	{
		return $this->query("sources");
	}

	public function genreList(): array
	{
		return $this->query("genres");
	}

	public function row(string $id): array | false
	{
		$list = $this->streamingList();
		$result = array_filter($list, function($row) use ($id) {
			return $row['id'] == $id;
		});
		return $result ? reset($result) : false;
	}

	public function details(string $id): array
	{
		return $this->query("title/{$id}/details");
	}
	
	public function query(string $url): array
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
