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
                $name, 
                floatval($amount), 
                $category, 
                $this->userId,
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
}
