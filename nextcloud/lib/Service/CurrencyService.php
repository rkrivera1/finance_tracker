<?php
namespace OCA\FinanceTracker\Service;

use Exception;
use GuzzleHttp\Client;
use OCA\FinanceTracker\Db\CurrencyRateMapper;
use OCP\IConfig;

class CurrencyService {
    private $currencyRateMapper;
    private $config;
    private $httpClient;

    // List of supported currencies
    private const SUPPORTED_CURRENCIES = [
        'USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF', 
        'CNY', 'HKD', 'SGD', 'INR', 'BRL', 'RUB'
    ];

    public function __construct(
        CurrencyRateMapper $currencyRateMapper,
        IConfig $config
    ) {
        $this->currencyRateMapper = $currencyRateMapper;
        $this->config = $config;
        $this->httpClient = new Client([
            'timeout' => 10.0
        ]);
    }

    /**
     * Convert amount from one currency to another
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     * @throws Exception
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float {
        // Validate currencies
        $this->validateCurrencies($fromCurrency, $toCurrency);

        // If converting to same currency, return original amount
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Try to get cached rate
        $rate = $this->getCurrencyRate($fromCurrency, $toCurrency);

        return $amount * $rate;
    }

    /**
     * Get exchange rate between two currencies
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     * @throws Exception
     */
    public function getCurrencyRate(string $fromCurrency, string $toCurrency): float {
        // Check if multi-currency is enabled
        $multiCurrencyEnabled = $this->config->getAppValue(
            'finance_tracker', 
            'enable_multi_currency', 
            'false'
        );

        if ($multiCurrencyEnabled !== 'true') {
            throw new Exception('Multi-currency support is disabled');
        }

        // Check cached rate
        $cachedRate = $this->currencyRateMapper->findRate($fromCurrency, $toCurrency);
        
        // If cached rate exists and is recent (less than 24 hours old), use it
        if ($cachedRate && !$cachedRate->isStale(new \DateInterval('P1D'))) {
            return $cachedRate->getExchangeRate();
        }

        // Fetch latest rate
        $rate = $this->fetchExchangeRate($fromCurrency, $toCurrency);

        // Cache the rate
        $this->currencyRateMapper->saveOrUpdateRate(
            $fromCurrency, 
            $toCurrency, 
            $rate, 
            'exchange_api'
        );

        return $rate;
    }

    /**
     * Fetch exchange rate from external API
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     * @throws Exception
     */
    private function fetchExchangeRate(string $fromCurrency, string $toCurrency): float {
        try {
            // Use Exchange Rates API (free tier)
            $apiKey = $this->config->getAppValue(
                'finance_tracker', 
                'exchange_rates_api_key', 
                ''
            );

            $url = "https://openexchangerates.org/api/latest.json?app_id={$apiKey}&base={$fromCurrency}&symbols={$toCurrency}";

            $response = $this->httpClient->request('GET', $url);
            $data = json_decode($response->getBody(), true);

            if (!isset($data['rates'][$toCurrency])) {
                throw new Exception('Unable to fetch exchange rate');
            }

            return $data['rates'][$toCurrency];
        } catch (\Exception $e) {
            // Fallback to a default rate if API fails
            return $this->getDefaultRate($fromCurrency, $toCurrency);
        }
    }

    /**
     * Get a default exchange rate (for testing/fallback)
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    private function getDefaultRate(string $fromCurrency, string $toCurrency): float {
        // Predefined default rates for common currencies
        $defaultRates = [
            'USD_EUR' => 0.92,
            'EUR_USD' => 1.09,
            'USD_GBP' => 0.79,
            'GBP_USD' => 1.27,
            // Add more default rates as needed
        ];

        $key = "{$fromCurrency}_{$toCurrency}";
        
        return $defaultRates[$key] ?? 1.0;
    }

    /**
     * Validate currency codes
     * @param string $fromCurrency
     * @param string $toCurrency
     * @throws Exception
     */
    private function validateCurrencies(string $fromCurrency, string $toCurrency) {
        $fromCurrency = strtoupper($fromCurrency);
        $toCurrency = strtoupper($toCurrency);

        if (!in_array($fromCurrency, self::SUPPORTED_CURRENCIES)) {
            throw new Exception("Unsupported base currency: {$fromCurrency}");
        }

        if (!in_array($toCurrency, self::SUPPORTED_CURRENCIES)) {
            throw new Exception("Unsupported target currency: {$toCurrency}");
        }
    }

    /**
     * Get list of supported currencies
     * @return array
     */
    public function getSupportedCurrencies(): array {
        return self::SUPPORTED_CURRENCIES;
    }
}
