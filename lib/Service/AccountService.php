<?php
namespace OCA\FinanceTracker\Service;

use OCA\FinanceTracker\Db\AccountMapper;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

class AccountService {
    /** @var AccountMapper */
    private $mapper;

    /** @var IUserSession */
    private $userSession;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        AccountMapper $mapper,
        IUserSession $userSession,
        LoggerInterface $logger
    ) {
        $this->mapper = $mapper;
        $this->userSession = $userSession;
        $this->logger = $logger;
    }

    /**
     * Get all accounts for current user
     *
     * @param array $filters
     * @return array
     */
    public function findAll(array $filters = []): array {
        try {
            return $this->mapper->findAll($filters);
        } catch (\Exception $e) {
            $this->logger->error('Failed to retrieve accounts', [
                'exception' => $e,
                'filters' => $filters
            ]);
            throw $e;
        }
    }

    /**
     * Create a new account
     *
     * @param array $data
     * @return array
     * @throws \InvalidArgumentException
     */
    public function create(array $data): array {
        try {
            return $this->mapper->insert($data);
        } catch (\Exception $e) {
            $this->logger->error('Failed to create account', [
                'exception' => $e,
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing account
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update(int $id, array $data): array {
        try {
            return $this->mapper->update($id, $data);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update account', [
                'exception' => $e,
                'id' => $id,
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete an account
     *
     * @param int $id
     */
    public function delete(int $id): void {
        try {
            $this->mapper->delete($id);
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete account', [
                'exception' => $e,
                'id' => $id
            ]);
            throw $e;
        }
    }
}
