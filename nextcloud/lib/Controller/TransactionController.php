<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCA\FinanceTracker\Service\TransactionService;
use OCP\AppFramework\Http;

class TransactionController extends Controller {
    private $service;
    private $userId;

    public function __construct(
        $AppName,
        IRequest $request,
        TransactionService $service,
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
    public function listByCategory(string $category) {
        return new DataResponse(
            $this->service->findByCategory($this->userId, $category)
        );
    }

    /**
     * @NoAdminRequired
     * @param float $amount
     * @param string $category
     * @param string|null $description
     * @param string|null $transactionDate
     * @return DataResponse
     */
    public function create(
        float $amount, 
        string $category, 
        ?string $description = null, 
        ?string $transactionDate = null
    ) {
        try {
            $transaction = $this->service->create(
                $this->userId, 
                $amount, 
                $category, 
                $description, 
                $transactionDate ? new \DateTime($transactionDate) : null
            );
            return new DataResponse($transaction, Http::STATUS_CREATED);
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
