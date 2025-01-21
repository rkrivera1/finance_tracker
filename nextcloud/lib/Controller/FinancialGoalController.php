<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\FinanceTracker\Service\FinancialGoalService;
use OCP\AppFramework\Http;

class FinancialGoalController extends Controller {
    private $service;
    private $userId;

    public function __construct(
        $AppName,
        IRequest $request,
        FinancialGoalService $service,
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
     * @return DataResponse
     */
    public function activeGoals() {
        return new DataResponse(
            $this->service->findActiveGoals($this->userId)
        );
    }

    /**
     * @NoAdminRequired
     * @param string $title
     * @param float $targetAmount
     * @param string $category
     * @param string $startDate
     * @param string $targetDate
     * @return DataResponse
     */
    public function create(
        string $title, 
        float $targetAmount, 
        string $category,
        string $startDate, 
        string $targetDate
    ) {
        try {
            $goal = $this->service->create(
                $this->userId, 
                $title, 
                $targetAmount, 
                $category,
                new \DateTime($startDate), 
                new \DateTime($targetDate)
            );
            return new DataResponse($goal, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @param int $goalId
     * @return DataResponse
     */
    public function updateProgress(int $goalId) {
        try {
            $goal = $this->service->updateGoalProgress($goalId, $this->userId);
            return new DataResponse($goal);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @param int $goalId
     * @return DataResponse
     */
    public function destroy(int $goalId) {
        try {
            $this->service->delete($goalId, $this->userId);
            return new DataResponse(null, Http::STATUS_NO_CONTENT);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_NOT_FOUND);
        }
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function analyzeGoals() {
        try {
            $analysis = $this->service->analyzeGoalAchievement($this->userId);
            return new DataResponse($analysis);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
