<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;

use OCA\FinanceTracker\Db\BudgetMapper;

class BudgetController extends Controller {
    private $budgetMapper;
    private $userId;

    public function __construct(
        $appName,
        IRequest $request,
        BudgetMapper $budgetMapper,
        $userId
    ) {
        parent::__construct($appName, $request);
        $this->budgetMapper = $budgetMapper;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function index() {
        return new DataResponse(
            $this->budgetMapper->findByUser($this->userId)
        );
    }

    /**
     * @NoAdminRequired
     */
    public function create($name, $amount, $category, $startDate, $endDate) {
        try {
            $budget = $this->budgetMapper->create(
                $this->userId,
                $name, 
                floatval($amount), 
                $category, 
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
     */
    public function update($id, $name = null, $amount = null, $category = null, $startDate = null, $endDate = null) {
        try {
            $budget = $this->budgetMapper->find($id);
            
            if ($name !== null) $budget->setName($name);
            if ($amount !== null) $budget->setAmount(floatval($amount));
            if ($category !== null) $budget->setCategory($category);
            if ($startDate !== null) $budget->setStartDate(new \DateTime($startDate));
            if ($endDate !== null) $budget->setEndDate(new \DateTime($endDate));
            
            return new DataResponse(
                $this->budgetMapper->update($budget)
            );
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function destroy($id) {
        try {
            $budget = $this->budgetMapper->find($id);
            $this->budgetMapper->delete($budget);
            return new DataResponse(null, Http::STATUS_NO_CONTENT);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_NOT_FOUND);
        }
    }
}
