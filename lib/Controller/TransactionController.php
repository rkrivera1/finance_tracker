<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;

use OCA\FinanceTracker\Db\TransactionMapper;

class TransactionController extends Controller {
    private $transactionMapper;
    private $userId;

    public function __construct(
        $appName,
        IRequest $request,
        TransactionMapper $transactionMapper,
        $userId
    ) {
        parent::__construct($appName, $request);
        $this->transactionMapper = $transactionMapper;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function index() {
        return new DataResponse(
            $this->transactionMapper->findByUser($this->userId)
        );
    }

    /**
     * @NoAdminRequired
     */
    public function create($accountId, $description, $amount, $type) {
        try {
            $transaction = $this->transactionMapper->create(
                $this->userId, 
                intval($accountId), 
                $description, 
                floatval($amount), 
                $type
            );
            return new DataResponse($transaction, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }
}
