<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use DateTime;
class WmService extends AbstractController
{
    protected string $apiKey;
    protected string$apiUrl;
	protected FilesystemAdapter $cache;
	protected string $dir;

    public function __construct(ParameterBagInterface $params)
    {
        $this->apiKey = $_ENV['WM_APIKEY'];
        $this->apiUrl = $_ENV['WM_APIURL'];
		$this->cache = new FilesystemAdapter();
		$this->dir = $params->get('kernel.project_dir') . '/public/images/streamLogos/';
    }

    public function streamingList(): array
	{
		$sources = $this->query("sources");
		foreach($sources as &$row){
			$this->checkLogo($row);
		}
		return $sources;
	}

	public function genreList(): array
	{
		return $this->query("genres");
	}

	public function row(string $id): array | false
	{
		$list = $this->streamingList();
		$result = array_filter($list, function($row) use ($id) {
			$this->checkLogo($row);
			return $row['id'] == $id;
		});
		return $result ? reset($result) : false;
	}

	public function details(string $id): array
	{
		return $this->query("title/{$id}/details");
	}

	public function recent(string $id): array
	{
		$startDate = (new DateTime())->modify('-30 days')->format('Ymd');
		$endDate = (new DateTime())->modify('+30 days')->format('Ymd');
		
		$limit = 60;
		$list = $this->query("releases?start_date={$startDate}&end_date={$endDate}&limit={$limit}")['releases'];
		$result = array_filter($list, function($row) use ($id) {
			return $row['source_id'] == $id;
		});
		
		$result = array_slice($result, 0, 20);
		return $result ?: [];
	}

	public function query(string $url): array
    {
        $cacheKey = md5($url);
        $cachedData = $this->cache->get($cacheKey, function (ItemInterface $item) use ($url) {
            $item->expiresAfter(3600 * 24 * 10);
            return $this->fetchDataFromApi($url);
        });

        return $cachedData;
    }

	private function checkLogo(&$row)
	{
		$img = $this->dir.$row['ios_scheme']."_".$row['id'].".png";
		if(file_exists($img)){
			$row['logo'] = true;
		}
		else{
			$row['logo'] = false;
		}
	}

	private function fetchDataFromApi(string $url): array
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
