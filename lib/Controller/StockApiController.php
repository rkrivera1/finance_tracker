<?php

namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCA\FinanceTracker\Service\StockApiService;

class StockApiController extends Controller {
    private $stockService;

    public function __construct($appName, IRequest $request, StockApiService $stockService) {
        parent::__construct($appName, $request);
        $this->stockService = $stockService;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function search($query) {
        try {
            $results = $this->stockService->searchStocks($query);
            return new JSONResponse($results);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getPrices($symbols) {
        try {
            $prices = $this->stockService->getStockPrices($symbols);
            return new JSONResponse($prices);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getDetails($symbol) {
        try {
            $details = $this->stockService->getStockDetails($symbol);
            return new JSONResponse($details);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }
} 