<?php
namespace OCA\FinanceTracker\Lib;

use Exception;
use GuzzleHttp\Client;
use OCP\IConfig;

class StockService {
    private $httpClient;
    private $config;

    // API providers configuration
    private const API_PROVIDERS = [
        'alpha_vantage' => [
            'base_url' => 'https://www.alphavantage.co/query',
            'function' => 'GLOBAL_QUOTE'
        ],
        'finnhub' => [
            'base_url' => 'https://finnhub.io/api/v1/quote',
        ],
        'twelve_data' => [
            'base_url' => 'https://api.twelvedata.com/price'
        ]
    ];

    public function __construct(
        Client $httpClient, 
        IConfig $config
    ) {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Fetch real-time stock price
     * 
     * @param string $symbol Stock symbol
     * @return array Stock price information
     * @throws Exception If API call fails
     */
    public function getRealTimePrice(string $symbol): array {
        // Prioritize API providers
        $providers = ['alpha_vantage', 'finnhub', 'twelve_data'];
        
        foreach ($providers as $provider) {
            try {
                return $this->fetchPriceFromProvider($symbol, $provider);
            } catch (Exception $e) {
                // Log the error, continue to next provider
                \OC::$server->getLogger()->error(
                    "Stock price fetch failed for $symbol from $provider: " . $e->getMessage()
                );
            }
        }

        throw new Exception("Unable to fetch stock price for $symbol");
    }

    /**
     * Fetch stock price from a specific provider
     * 
     * @param string $symbol Stock symbol
     * @param string $provider API provider
     * @return array Stock price details
     */
    private function fetchPriceFromProvider(string $symbol, string $provider): array {
        $apiKey = $this->getApiKey($provider);
        
        switch ($provider) {
            case 'alpha_vantage':
                return $this->fetchAlphaVantagePrice($symbol, $apiKey);
            case 'finnhub':
                return $this->fetchFinnhubPrice($symbol, $apiKey);
            case 'twelve_data':
                return $this->fetchTwelveDataPrice($symbol, $apiKey);
            default:
                throw new Exception("Unsupported provider: $provider");
        }
    }

    /**
     * Fetch price from Alpha Vantage API
     */
    private function fetchAlphaVantagePrice(string $symbol, string $apiKey): array {
        $response = $this->httpClient->request('GET', self::API_PROVIDERS['alpha_vantage']['base_url'], [
            'query' => [
                'function' => self::API_PROVIDERS['alpha_vantage']['function'],
                'symbol' => $symbol,
                'apikey' => $apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        
        if (!isset($data['Global Quote']['05. price'])) {
            throw new Exception('Invalid Alpha Vantage response');
        }

        return [
            'symbol' => $symbol,
            'price' => (float)$data['Global Quote']['05. price'],
            'change' => (float)$data['Global Quote']['09. change'],
            'change_percent' => (float)$data['Global Quote']['10. change percent'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Fetch price from Finnhub API
     */
    private function fetchFinnhubPrice(string $symbol, string $apiKey): array {
        $response = $this->httpClient->request('GET', self::API_PROVIDERS['finnhub']['base_url'], [
            'query' => [
                'symbol' => $symbol,
                'token' => $apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        
        if (!isset($data['c'])) {
            throw new Exception('Invalid Finnhub response');
        }

        return [
            'symbol' => $symbol,
            'price' => $data['c'],
            'change' => $data['d'],
            'change_percent' => $data['dp'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Fetch price from Twelve Data API
     */
    private function fetchTwelveDataPrice(string $symbol, string $apiKey): array {
        $response = $this->httpClient->request('GET', self::API_PROVIDERS['twelve_data']['base_url'], [
            'query' => [
                'symbol' => $symbol,
                'apikey' => $apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        
        if (!isset($data['price'])) {
            throw new Exception('Invalid Twelve Data response');
        }

        return [
            'symbol' => $symbol,
            'price' => (float)$data['price'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Retrieve API key for a provider
     * 
     * @param string $provider API provider name
     * @return string API key
     * @throws Exception If API key is not configured
     */
    private function getApiKey(string $provider): string {
        $apiKey = $this->config->getAppValue(
            'finance_tracker', 
            "{$provider}_api_key"
        );

        if (empty($apiKey)) {
            throw new Exception("No API key configured for $provider");
        }

        return $apiKey;
    }

    /**
     * Batch fetch prices for multiple stocks
     * 
     * @param array $symbols List of stock symbols
     * @return array Prices for each symbol
     */
    public function getBatchPrices(array $symbols): array {
        $prices = [];
        
        foreach ($symbols as $symbol) {
            try {
                $prices[$symbol] = $this->getRealTimePrice($symbol);
            } catch (Exception $e) {
                $prices[$symbol] = [
                    'error' => $e->getMessage(),
                    'symbol' => $symbol
                ];
            }
        }

        return $prices;
    }
}
