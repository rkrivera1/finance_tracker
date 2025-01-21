<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\FinanceTracker\Service\ReportingService;
use OCP\AppFramework\Http;

class ReportController extends Controller {
    private $reportingService;
    private $userId;

    public function __construct(
        $AppName,
        IRequest $request,
        ReportingService $reportingService,
        $UserId
    ) {
        parent::__construct($AppName, $request);
        $this->reportingService = $reportingService;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     * @param int $year
     * @param int $month
     * @return DataResponse
     */
    public function monthlyReport(int $year, int $month) {
        try {
            $report = $this->reportingService->generateMonthlyReport(
                $this->userId, 
                $year, 
                $month
            );

            return new DataResponse($report);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @param int $year
     * @return DataResponse
     */
    public function annualTrends(int $year) {
        try {
            $trends = $this->reportingService->generateAnnualTrends(
                $this->userId, 
                $year
            );

            return new DataResponse($trends);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @param int $months
     * @return DataResponse
     */
    public function spendingPrediction(int $months = 3) {
        try {
            $predictions = $this->reportingService->predictFutureSpending(
                $this->userId, 
                $months
            );

            return new DataResponse($predictions);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }
}
