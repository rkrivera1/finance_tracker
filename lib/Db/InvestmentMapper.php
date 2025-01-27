<?php
namespace OCA\FinanceTracker\Db;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IUserSession;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

class InvestmentMapper extends BaseMapper {
    /** @var IUserSession */
    private $userSession;

    public function __construct(
        IDBConnection $db, 
        IUserSession $userSession
    ) {
        parent::__construct($db);
        $this->tableName = 'finance_tracker_investments';
        $this->userSession = $userSession;
    }

    /**
     * Find investments for the current user
     *
     * @param array $filters Optional filters
     * @return array
     * @throws \OCP\DB\Exception
     */
    public function findAll(array $filters = []): array {
        $userId = $this->userSession->getUser()->getUID();
        
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->tableName)
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

        // Apply optional filters
        if (isset($filters['type'])) {
            $qb->andWhere($qb->expr()->eq('type', $qb->createNamedParameter($filters['type'])));
        }

        if (isset($filters['start_date'])) {
            $qb->andWhere($qb->expr()->gte('purchase_date', $qb->createNamedParameter($filters['start_date'])));
        }

        // Optional sorting
        $qb->orderBy('purchase_date', 'DESC');

        return $qb->executeQuery()->fetchAll();
    }

    /**
     * Create a new investment
     *
     * @param array $data Investment data
     * @return array Created investment
     * @throws \InvalidArgumentException
     * @throws \OCP\DB\Exception
     */
    public function insert(array $data): array {
        $userId = $this->userSession->getUser()->getUID();
        $data['user_id'] = $userId;

        // Validate required fields
        $this->validateInvestmentData($data);

        return parent::insert($data);
    }

    /**
     * Validate investment data before insertion
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    private function validateInvestmentData(array &$data): void {
        $requiredFields = ['symbol', 'quantity', 'purchase_price', 'purchase_date', 'type'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }

        // Validate investment types
        $validTypes = ['stock', 'bond', 'etf', 'mutual_fund', 'cryptocurrency'];
        if (!in_array($data['type'], $validTypes)) {
            throw new \InvalidArgumentException("Invalid investment type");
        }

        // Ensure numeric values
        $data['quantity'] = floatval($data['quantity']);
        $data['purchase_price'] = floatval($data['purchase_price']);

        // Ensure date is valid
        $data['purchase_date'] = $this->ensureDateTime($data['purchase_date']);
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
     * Calculate investment performance
     *
     * @param array $filters Optional filters
     * @return array Investment performance statistics
     * @throws \OCP\DB\Exception
     */
    public function getPerformance(array $filters = []): array {
        $userId = $this->userSession->getUser()->getUID();
        
        $qb = $this->db->getQueryBuilder();
        $qb->select([
            'symbol', 
            'type', 
            $qb->func()->sum('quantity')->setAlias('total_quantity'),
            $qb->func()->avg('purchase_price')->setAlias('avg_purchase_price')
        ])
           ->from($this->tableName)
           ->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
           ->groupBy('symbol', 'type');

        // Apply optional filters
        if (isset($filters['type'])) {
            $qb->andWhere($qb->expr()->eq('type', $qb->createNamedParameter($filters['type'])));
        }

        return $qb->executeQuery()->fetchAll();
    }
}
