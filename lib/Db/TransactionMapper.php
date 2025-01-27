<?php
namespace OCA\FinanceTracker\Db;

use OCP\IDBConnection;
use OCP\IUserSession;
use OCP\DB\QueryBuilder\IQueryBuilder;

class TransactionMapper extends BaseMapper {
    /** @var IUserSession */
    private $userSession;

    public function __construct(
        IDBConnection $db, 
        IUserSession $userSession
    ) {
        parent::__construct($db);
        $this->tableName = 'finance_tracker_transactions';
        $this->userSession = $userSession;
    }

    /**
     * Find transactions for the current user
     *
     * @param array $filters Optional filters
     * @return array
     */
    public function findAll(array $filters = []) {
        $userId = $this->userSession->getUser()->getUID();
        
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->tableName)
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

        // Apply optional filters
        if (isset($filters['account_id'])) {
            $qb->andWhere($qb->expr()->eq('account_id', $qb->createNamedParameter($filters['account_id'], IQueryBuilder::PARAM_INT)));
        }

        if (isset($filters['start_date'])) {
            $qb->andWhere($qb->expr()->gte('date', $qb->createNamedParameter($filters['start_date'])));
        }

        if (isset($filters['end_date'])) {
            $qb->andWhere($qb->expr()->lte('date', $qb->createNamedParameter($filters['end_date'])));
        }

        if (isset($filters['category'])) {
            $qb->andWhere($qb->expr()->eq('category', $qb->createNamedParameter($filters['category'])));
        }

        // Optional sorting
        $qb->orderBy('date', 'DESC');

        return $qb->executeQuery()->fetchAll();
    }

    /**
     * Create a new transaction
     *
     * @param array $data Transaction data
     * @return array Created transaction
     */
    public function insert(array $data) {
        $userId = $this->userSession->getUser()->getUID();
        $data['user_id'] = $userId;

        // Validate required fields
        $this->validateTransactionData($data);

        return parent::insert($data);
    }

    /**
     * Validate transaction data before insertion
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    private function validateTransactionData(array &$data) {
        $requiredFields = ['account_id', 'amount', 'date'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }

        // Ensure amount is a valid number
        $data['amount'] = floatval($data['amount']);

        // Ensure date is a valid datetime
        if (!$data['date'] instanceof \DateTime) {
            $data['date'] = new \DateTime($data['date']);
        }

        // Optional fields with defaults
        $data['description'] = $data['description'] ?? '';
        $data['category'] = $data['category'] ?? 'Uncategorized';
    }

    /**
     * Get transaction summary for a given period
     *
     * @param array $filters
     * @return array Summary statistics
     */
    public function getSummary(array $filters = []) {
        $userId = $this->userSession->getUser()->getUID();
        
        $qb = $this->db->getQueryBuilder();
        $qb->select([
            $qb->func()->sum('amount')->setAlias('total_amount'),
            $qb->func()->count('id')->setAlias('transaction_count'),
            'category'
        ])
           ->from($this->tableName)
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->groupBy('category');

        // Apply optional filters
        if (isset($filters['start_date'])) {
            $qb->andWhere($qb->expr()->gte('date', $qb->createNamedParameter($filters['start_date'])));
        }

        if (isset($filters['end_date'])) {
            $qb->andWhere($qb->expr()->lte('date', $qb->createNamedParameter($filters['end_date'])));
        }

        return $qb->executeQuery()->fetchAll();
    }
}
