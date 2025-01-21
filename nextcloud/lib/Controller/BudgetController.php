<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\FinanceTracker\Service\BudgetService;
use OCP\AppFramework\Http;

class BudgetController extends Controller {
    private $service;
    private $userId;

    public function __construct(
        $AppName,
        IRequest $request,
        BudgetService $service,
        $UserId
    ) {
        parent::__construct($AppName, $request);
        $this->service = $service;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function index() {
        return new DataResponse(
            $this->service->findAll($this->userId)
        );
    }

    /**
     * @NoAdminRequired
     * @param string $category
     * @return DataResponse
     */
    public function status(string $category) {
        try {
            return new DataResponse(
                $this->service->getBudgetStatus($this->userId, $category)
            );
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @param string $category
     * @param float $budgetAmount
     * @param string $startDate
     * @param string $endDate
     * @return DataResponse
     */
    public function create(
        string $category, 
        float $budgetAmount, 
        string $startDate, 
        string $endDate
    ) {
        try {
            $budget = $this->service->create(
                $this->userId, 
                $category, 
                $budgetAmount, 
                new \DateTime($startDate), 
                new \DateTime($endDate)
            );
            return new DataResponse($budget, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @param int $id
     * @return DataResponse
     */
    public function destroy(int $id) {
        try {
            $this->service->delete($id, $this->userId);
            return new DataResponse(null, Http::STATUS_NO_CONTENT);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_NOT_FOUND);
        }
    }
}
