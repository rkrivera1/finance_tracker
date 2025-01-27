<?php
namespace OCA\FinanceTracker\Db;

use OCP\IDBConnection;
use OCP\IUserSession;
use OCP\DB\QueryBuilder\IQueryBuilder;

class BudgetMapper extends BaseMapper {
    /** @var IUserSession */
    private $userSession;

    public function __construct(
        IDBConnection $db, 
        IUserSession $userSession
    ) {
        parent::__construct($db);
        $this->tableName = 'finance_tracker_budgets';
        $this->userSession = $userSession;
    }

    /**
     * Find budgets for the current user
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
        if (isset($filters['category'])) {
            $qb->andWhere($qb->expr()->eq('category', $qb->createNamedParameter($filters['category'])));
        }

        if (isset($filters['start_date'])) {
            $qb->andWhere($qb->expr()->gte('start_date', $qb->createNamedParameter($filters['start_date'])));
        }

        if (isset($filters['end_date'])) {
            $qb->andWhere($qb->expr()->lte('end_date', $qb->createNamedParameter($filters['end_date'])));
        }

        // Optional sorting
        $qb->orderBy('start_date', 'DESC');

        return $qb->executeQuery()->fetchAll();
    }

    /**
     * Create a new budget
     *
     * @param array $data Budget data
     * @return array Created budget
     */
    public function insert(array $data) {
        $userId = $this->userSession->getUser()->getUID();
        $data['user_id'] = $userId;

        // Validate required fields
        $this->validateBudgetData($data);

        return parent::insert($data);
    }

    /**
     * Validate budget data before insertion
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    private function validateBudgetData(array &$data) {
        $requiredFields = ['category', 'amount', 'start_date', 'end_date'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }

        // Ensure amount is a valid number
        $data['amount'] = floatval($data['amount']);

        // Ensure dates are valid datetime
        $data['start_date'] = $this->ensureDateTime($data['start_date']);
        $data['end_date'] = $this->ensureDateTime($data['end_date']);

        // Validate date range
        if ($data['start_date'] > $data['end_date']) {
            throw new \InvalidArgumentException("Start date must be before end date");
        }

        // Optional description
        $data['description'] = $data['description'] ?? '';
    }

    /**
     * Ensure input is a DateTime object
     *
     * @param mixed $date
     * @return \DateTime
     */
    private function ensureDateTime($date): \DateTime {
        if (!$date instanceof \DateTime) {
            return new \DateTime($date);
        }
        return $date;
    }

    /**
     * Get budget progress for the current user
     *
     * @param array $filters
     * @return array Budget progress statistics
     */
    public function getBudgetProgress(array $filters = []) {
        $userId = $this->userSession->getUser()->getUID();
        
        $qb = $this->db->getQueryBuilder();
        $qb->select([
            'b.id', 
            'b.category', 
            'b.amount AS budget_amount', 
            $qb->func()->sum('t.amount')->setAlias('spent_amount')
        ])
           ->from($this->tableName, 'b')
           ->leftJoin('b', 'finance_tracker_transactions', 't', 
               $qb->expr()->andX(
                   $qb->expr()->eq('b.user_id', 't.user_id'),
                   $qb->expr()->eq('b.category', 't.category'),
                   $qb->expr()->gte('t.date', 'b.start_date'),
                   $qb->expr()->lte('t.date', 'b.end_date')
               )
           )
           ->where($qb->expr()->eq('b.user_id', $qb->createNamedParameter($userId)))
           ->groupBy('b.id', 'b.category', 'b.amount');

        // Apply optional filters
        if (isset($filters['start_date'])) {
            $qb->andWhere($qb->expr()->gte('b.start_date', $qb->createNamedParameter($filters['start_date'])));
        }

        if (isset($filters['end_date'])) {
            $qb->andWhere($qb->expr()->lte('b.end_date', $qb->createNamedParameter($filters['end_date'])));
        }

        return $qb->executeQuery()->fetchAll();
    }
}
