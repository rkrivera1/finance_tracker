<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\FinanceTracker\Service\CurrencyService;
use OCP\AppFramework\Http;

class CurrencyController extends Controller {
    private $currencyService;

    public function __construct(
        $AppName,
        IRequest $request,
        CurrencyService $currencyService
    ) {
        parent::__construct($AppName, $request);
        $this->currencyService = $currencyService;
    }

    /**
     * @NoAdminRequired
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return DataResponse
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency) {
        try {
            $convertedAmount = $this->currencyService->convert(
                $amount, 
                $fromCurrency, 
                $toCurrency
            );

            return new DataResponse([
                'originalAmount' => $amount,
                'originalCurrency' => $fromCurrency,
                'convertedAmount' => $convertedAmount,
                'targetCurrency' => $toCurrency
            ]);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function getSupportedCurrencies() {
        return new DataResponse(
            $this->currencyService->getSupportedCurrencies()
        );
    }

    /**
     * @NoAdminRequired
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return DataResponse
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency) {
        try {
            $rate = $this->currencyService->getCurrencyRate(
                $fromCurrency, 
                $toCurrency
            );

            return new DataResponse([
                'baseCurrency' => $fromCurrency,
                'targetCurrency' => $toCurrency,
                'exchangeRate' => $rate
            ]);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }
}
