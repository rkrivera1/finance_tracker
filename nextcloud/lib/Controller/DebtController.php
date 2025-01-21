<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\FinanceTracker\Service\DebtManagementService;
use OCP\AppFramework\Http;

class DebtController extends Controller {
    private $debtService;
    private $userId;

    public function __construct(
        $AppName,
        IRequest $request,
        DebtManagementService $debtService,
        $UserId
    ) {
        parent::__construct($AppName, $request);
        $this->debtService = $debtService;
        $this->userId = $UserId;
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function index() {
        try {
            $debts = $this->debtService->findAll($this->userId);
            return new DataResponse($debts);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @param string $creditorName
     * @param string $type
     * @param float $totalAmount
     * @param float $interestRate
     * @param string $startDate
     * @param string $dueDate
     * @param float $minimumPayment
     * @return DataResponse
     */
    public function create(
        string $creditorName,
        string $type,
        float $totalAmount,
        float $interestRate,
        string $startDate,
        string $dueDate,
        float $minimumPayment
    ) {
        try {
            $debt = $this->debtService->createDebt(
                $this->userId,
                $creditorName,
                $type,
                $totalAmount,
                $interestRate,
                new \DateTime($startDate),
                new \DateTime($dueDate),
                $minimumPayment
            );

            return new DataResponse($debt, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     * @param int $debtId
     * @param float $payment
     * @return DataResponse
     */
    public function makePayment(int $debtId, float $payment) {
        try {
            $updatedDebt = $this->debtService->makePayment(
                $debtId, 
                $this->userId, 
                $payment
            );

            return new DataResponse($updatedDebt);
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
    public function analyzePortfolio() {
        try {
            $analysis = $this->debtService->analyzeDebtPortfolio($this->userId);
            return new DataResponse($analysis);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @NoAdminRequired
     * @return DataResponse
     */
    public function repaymentStrategy() {
        try {
            $strategy = $this->debtService->recommendDebtRepaymentStrategy($this->userId);
            return new DataResponse($strategy);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @NoAdminRequired
     * @param int $debtId
     * @return DataResponse
     */
    public function destroy(int $debtId) {
        try {
            $this->debtService->deleteDebt($debtId, $this->userId);
            return new DataResponse(null, Http::STATUS_NO_CONTENT);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_NOT_FOUND);
        }
    }
}
