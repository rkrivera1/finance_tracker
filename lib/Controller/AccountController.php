<?php
namespace OCA\FinanceTracker\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;

use OCA\FinanceTracker\Db\AccountMapper;

class AccountController extends Controller {
    private $accountMapper;
    private $userId;

    public function __construct(
        $appName,
        IRequest $request,
        AccountMapper $accountMapper,
        $userId
    ) {
        parent::__construct($appName, $request);
        $this->accountMapper = $accountMapper;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
     */
    public function index() {
        return new DataResponse(
            $this->accountMapper->findByUser($this->userId)
        );
    }

    /**
     * @NoAdminRequired
     */
    public function create($name, $type, $balance) {
        try {
            $account = $this->accountMapper->create(
                $name, 
                $type, 
                floatval($balance), 
                $this->userId
            );
            return new DataResponse($account, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return new DataResponse([
                'message' => $e->getMessage()
            ], Http::STATUS_BAD_REQUEST);
        }
    }
}
