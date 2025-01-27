<?php
namespace OCA\FinanceTracker\Controller;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\FinanceTracker\Db\AccountMapper;

class AccountController extends BaseController {
    /** @var AccountMapper */
    private $mapper;

    public function __construct(
        IRequest $request,
        AccountMapper $mapper
    ) {
        parent::__construct('finance_tracker', $request);
        $this->mapper = $mapper;
    }

    /**
     * Get all accounts
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        try {
            $accounts = $this->mapper->findAll();
            return $this->success($accounts);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Create a new account
     *
     * @NoAdminRequired
     * @param array $accountData Account details
     */
    public function create($accountData) {
        try {
            $account = $this->mapper->insert($accountData);
            return $this->success($account, Http::STATUS_CREATED);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Update an existing account
     *
     * @NoAdminRequired
     * @param int $id Account ID
     * @param array $accountData Updated account details
     */
    public function update($id, $accountData) {
        try {
            $account = $this->mapper->update($id, $accountData);
            return $this->success($account);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Delete an account
     *
     * @NoAdminRequired
     * @param int $id Account ID to delete
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
