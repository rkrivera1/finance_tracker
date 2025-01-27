<?php
namespace OCA\FinanceTracker\Lib;

use Exception;
use GuzzleHttp\Client;
use OCP\IConfig;
use RuntimeException;
use Throwable;
use InvalidArgumentException;

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
        // Validate input
        if (empty($symbol) || !preg_match('/^[A-Z]+$/', $symbol)) {
            throw new InvalidArgumentException("Invalid stock symbol: $symbol");
        }

        // Limit number of providers to prevent excessive API calls
        $providers = array_slice(['alpha_vantage', 'finnhub', 'twelve_data'], 0, 2);
        
        $errors = [];
        foreach ($providers as $provider) {
            try {
                $result = $this->fetchPriceFromProvider($symbol, $provider);
                
                // Additional validation of result
                if (!is_array($result) || empty($result)) {
                    $errors[] = "Empty result from $provider";
                    continue;
                }
                
                return $result;
            } catch (Throwable $e) {
                // Log detailed error information
                \OC::$server->getLogger()->error(
                    "Stock price fetch failed for $symbol from $provider: " . $e->getMessage(),
                    [
                        'app' => 'finance_tracker',
                        'exception' => $e,
                        'symbol' => $symbol,
                        'provider' => $provider
                    ]
                );
                $errors[] = $e->getMessage();
            }
        }

        // If all providers fail, throw a comprehensive exception
        throw new RuntimeException(
            "Failed to fetch stock price for $symbol. Errors: " . implode('; ', $errors)
        );
    }

    /**
     * Safely fetch price from a specific provider
     * 
     * @param string $symbol Stock symbol
     * @param string $provider Provider name
     * @return array Stock price information
     * @throws Exception If API call fails
     */
    private function fetchPriceFromProvider(string $symbol, string $provider): array {
        // Timeout and connection settings
        $timeout = 10; // 10 seconds
        
        try {
            switch ($provider) {
                case 'alpha_vantage':
                    $apiKey = $this->config->getAppValue('finance_tracker', 'alpha_vantage_api_key', '');
                    if (empty($apiKey)) {
                        throw new RuntimeException("Alpha Vantage API key not configured");
                    }
                    
                    $response = $this->httpClient->request('GET', self::API_PROVIDERS['alpha_vantage']['base_url'], [
                        'timeout' => $timeout,
                        'query' => [
                            'function' => 'GLOBAL_QUOTE',
                            'symbol' => $symbol,
                            'apikey' => $apiKey
                        ]
                    ]);
                    break;
                
                case 'finnhub':
                    $apiKey = $this->config->getAppValue('finance_tracker', 'finnhub_api_key', '');
                    if (empty($apiKey)) {
                        throw new RuntimeException("Finnhub API key not configured");
                    }
                    
                    $response = $this->httpClient->request('GET', self::API_PROVIDERS['finnhub']['base_url'], [
                        'timeout' => $timeout,
                        'query' => [
                            'symbol' => $symbol,
                            'token' => $apiKey
                        ]
                    ]);
                    break;
                
                case 'twelve_data':
                    $apiKey = $this->config->getAppValue('finance_tracker', 'twelve_data_api_key', '');
                    if (empty($apiKey)) {
                        throw new RuntimeException("Twelve Data API key not configured");
                    }
                    
                    $response = $this->httpClient->request('GET', self::API_PROVIDERS['twelve_data']['base_url'], [
                        'timeout' => $timeout,
                        'query' => [
                            'symbol' => $symbol,
                            'apikey' => $apiKey
                        ]
                    ]);
                    break;
                
                default:
                    throw new InvalidArgumentException("Unsupported provider: $provider");
            }

            $data = json_decode($response->getBody(), true);
            
            // Validate and transform data
            return $this->transformStockData($data, $provider);
        } catch (Throwable $e) {
            throw new RuntimeException(
                "Provider $provider stock fetch failed: " . $e->getMessage(), 
                $e->getCode(), 
                $e
            );
        }
    }

    /**
     * Transform stock data from different providers to a standard format
     * 
     * @param array $data Raw stock data
     * @param string $provider Provider name
     * @return array Standardized stock information
     */
    private function transformStockData(array $data, string $provider): array {
        switch ($provider) {
            case 'alpha_vantage':
                // Transform Alpha Vantage specific data
                return [
                    'symbol' => $data['Global Quote']['01. symbol'] ?? '',
                    'price' => $data['Global Quote']['05. price'] ?? 0,
                    'change' => $data['Global Quote']['09. change'] ?? 0,
                    'change_percent' => $data['Global Quote']['10. change percent'] ?? '0%',
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            
            case 'finnhub':
                // Transform Finnhub specific data
                return [
                    'symbol' => $symbol,
                    'price' => $data['c'],
                    'change' => $data['d'],
                    'change_percent' => $data['dp'],
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            
            case 'twelve_data':
                // Transform Twelve Data specific data
                return [
                    'symbol' => $symbol,
                    'price' => (float)$data['price'],
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            
            default:
                throw new InvalidArgumentException("Cannot transform data for provider: $provider");
        }
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
