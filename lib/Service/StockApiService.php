<?php

namespace OCA\FinanceTracker\Service;

use OCP\Http\Client\IClientService;
use OCP\IConfig;

class StockApiService {
    private $client;
    private $config;
    private $apiKey;
    private $baseUrl;

    public function __construct(IClientService $clientService, IConfig $config) {
        $this->client = $clientService->newClient();
        $this->config = $config;
        
        // Get API key from config
        $this->apiKey = $this->config->getAppValue('finance_tracker', 'alpha_vantage_api_key', '');
        $this->baseUrl = 'https://www.alphavantage.co/query';
    }

    public function searchStocks($query) {
        $response = $this->client->get($this->baseUrl, [
            'query' => [
                'function' => 'SYMBOL_SEARCH',
                'keywords' => $query,
                'apikey' => $this->apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        
        if (isset($data['bestMatches'])) {
            return array_map(function($match) {
                return [
                    'symbol' => $match['1. symbol'],
                    'name' => $match['2. name'],
                    'type' => $match['3. type'],
                    'region' => $match['4. region']
                ];
            }, $data['bestMatches']);
        }

        return [];
    }

    public function getStockPrices($symbols) {
        $prices = [];
        
        foreach ($symbols as $symbol) {
            $response = $this->client->get($this->baseUrl, [
                'query' => [
                    'function' => 'GLOBAL_QUOTE',
                    'symbol' => $symbol,
                    'apikey' => $this->apiKey
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (isset($data['Global Quote'])) {
                $quote = $data['Global Quote'];
                $prices[$symbol] = [
                    'price' => floatval($quote['05. price']),
                    'change' => floatval($quote['09. change']),
                    'changePercent' => floatval(rtrim($quote['10. change percent'], '%'))
                ];
            }
        }

        return $prices;
    }

    public function getStockDetails($symbol) {
        $response = $this->client->get($this->baseUrl, [
            'query' => [
                'function' => 'OVERVIEW',
                'symbol' => $symbol,
                'apikey' => $this->apiKey
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
} 