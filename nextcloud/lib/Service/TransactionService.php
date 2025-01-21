<?php
namespace OCA\FinanceTracker\Service;

use Exception;
use OCA\FinanceTracker\Db\Transaction;
use OCA\FinanceTracker\Db\TransactionMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

class TransactionService {
    private $mapper;

    public function __construct(TransactionMapper $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * Get all transactions for a user
     * @param string $userId
     * @return Transaction[]
     */
    public function findAll(string $userId): array {
        return $this->mapper->findAll($userId);
    }

    /**
     * Find transactions by category
     * @param string $userId
     * @param string $category
     * @return Transaction[]
     */
    public function findByCategory(string $userId, string $category): array {
        return $this->mapper->findByCategory($userId, $category);
    }

    /**
     * Create a new transaction
     * @param string $userId
     * @param float $amount
     * @param string $category
     * @param string|null $description
     * @param \DateTime|null $transactionDate
     * @return Transaction
     * @throws Exception
     */
    public function create(
        string $userId, 
        float $amount, 
        string $category, 
        ?string $description = null, 
        ?\DateTime $transactionDate = null
    ): Transaction {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        $transaction = new Transaction();
        $transaction->setUserId($userId);
        $transaction->setAmount($amount);
        $transaction->setCategory($category);
        $transaction->setDescription($description);
        $transaction->setTransactionDate($transactionDate ?? new \DateTime());
        $transaction->setCreatedAt(new \DateTime());

        return $this->mapper->insert($transaction);
    }

    /**
     * Delete a transaction
     * @param int $id
     * @param string $userId
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     */
    public function delete(int $id, string $userId) {
        try {
            $transaction = $this->mapper->findEntity($id);
            
            // Ensure the transaction belongs to the user
            if ($transaction->getUserId() !== $userId) {
                throw new Exception('Not authorized to delete this transaction');
            }

            $this->mapper->delete($transaction);
        } catch (DoesNotExistException $e) {
            throw $e;
        }
    }
}
