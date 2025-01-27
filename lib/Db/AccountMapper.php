<?php
namespace OCA\FinanceTracker\Db;

use OCP\IDBConnection;
use OCP\IUserSession;
use OCA\FinanceTracker\Db\BaseMapper;

class AccountMapper extends BaseMapper {
    /** @var IUserSession */
    private $userSession;

    public function __construct(
        IDBConnection $db, 
        IUserSession $userSession
    ) {
        parent::__construct($db);
        $this->tableName = 'finance_tracker_accounts';
        $this->userSession = $userSession;
    }

    /**
     * Find accounts for the current user
     *
     * @return array
     */
    public function findAll() {
        $userId = $this->userSession->getUser()->getUID();
        
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->tableName)
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

        return $qb->executeQuery()->fetchAll();
    }

    /**
     * Create a new account
     *
     * @param array $data Account data
     * @return array Created account
     */
    public function insert(array $data) {
        $userId = $this->userSession->getUser()->getUID();
        $data['user_id'] = $userId;

        return parent::insert($data);
    }

    /**
     * Validate account data before insertion
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    private function validateAccountData(array $data) {
        $requiredFields = ['name', 'type', 'balance'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }

        $validTypes = ['checking', 'savings', 'credit', 'investment'];
        if (!in_array($data['type'], $validTypes)) {
            throw new \InvalidArgumentException("Invalid account type");
        }

        $data['balance'] = floatval($data['balance']);
    }
}
