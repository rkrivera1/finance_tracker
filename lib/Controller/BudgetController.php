<?php
namespace OCA\FinanceTracker\Controller;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\FinanceTracker\Db\BudgetMapper;

class BudgetController extends BaseController {
    /** @var BudgetMapper */
    private $mapper;

    public function __construct(
        IRequest $request,
        BudgetMapper $mapper
    ) {
        parent::__construct('finance_tracker', $request);
        $this->mapper = $mapper;
    }

    /**
     * Get all budgets
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param array $filters Optional filters for budgets
     */
    public function index($filters = []) {
        try {
            $budgets = $this->mapper->findAll($filters);
            return $this->success($budgets);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Get budget progress
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param array $filters Optional filters for budget progress
     */
    public function progress($filters = []) {
        try {
            $progress = $this->mapper->getBudgetProgress($filters);
            return $this->success($progress);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Create a new budget
     *
     * @NoAdminRequired
     * @param array $budgetData Budget details
     */
    public function create($budgetData) {
        try {
            $budget = $this->mapper->insert($budgetData);
            return $this->success($budget, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update an existing budget
     *
     * @NoAdminRequired
     * @param int $id Budget ID
     * @param array $budgetData Updated budget details
     */
    public function update($id, $budgetData) {
        try {
            $budget = $this->mapper->update($id, $budgetData);
            return $this->success($budget);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Delete a budget
     *
     * @NoAdminRequired
     * @param int $id Budget ID to delete
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
