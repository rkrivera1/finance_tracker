<?php
namespace OCA\FinanceTracker\Controller;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\FinanceTracker\Db\TransactionMapper;

class TransactionController extends BaseController {
    /** @var TransactionMapper */
    private $mapper;

    public function __construct(
        IRequest $request,
        TransactionMapper $mapper
    ) {
        parent::__construct('finance_tracker', $request);
        $this->mapper = $mapper;
    }

    /**
     * Get all transactions
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param array $filters Optional filters for transactions
     */
    public function index($filters = []) {
        try {
            $transactions = $this->mapper->findAll($filters);
            return $this->success($transactions);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get transaction summary
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param array $filters Optional filters for summary
     */
    public function summary($filters = []) {
        try {
            $summary = $this->mapper->getSummary($filters);
            return $this->success($summary);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Create a new transaction
     *
     * @NoAdminRequired
     * @param array $transactionData Transaction details
     */
    public function create($transactionData) {
        try {
            $transaction = $this->mapper->insert($transactionData);
            return $this->success($transaction, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update an existing transaction
     *
     * @NoAdminRequired
     * @param int $id Transaction ID
     * @param array $transactionData Updated transaction details
     */
    public function update($id, $transactionData) {
        try {
            $transaction = $this->mapper->update($id, $transactionData);
            return $this->success($transaction);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Delete a transaction
     *
     * @NoAdminRequired
     * @param int $id Transaction ID to delete
     */
    public function delete($id) {
        try {
            $this->mapper->delete($id);
            return $this->success(null, Http::STATUS_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
