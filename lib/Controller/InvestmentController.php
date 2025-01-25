<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;

use OCA\FinanceTracker\Db\InvestmentMapper;

class InvestmentController extends Controller {
    private $investmentMapper;
    private $userId;

    public function __construct(
        $appName,
        IRequest $request,
        InvestmentMapper $investmentMapper,
        $userId
    ) {
        parent::__construct($appName, $request);
        $this->investmentMapper = $investmentMapper;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function index() {
        return new DataResponse(
            $this->investmentMapper->findByUser($this->userId)
        );
    }

    /**
     * @NoAdminRequired
     */
    public function create($name, $ticker, $shares, $purchasePrice) {
        try {
            $investment = $this->investmentMapper->create(
                $this->userId, 
                $name, 
                $ticker, 
                floatval($shares), 
                floatval($purchasePrice)
            );
            return new DataResponse($investment, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }
}
