<?php
namespace OCA\FinanceTracker\Service;

use OCA\FinanceTracker\Db\DebtMapper;
use OCA\FinanceTracker\Db\Debt;

class DebtManagementService {
    private $debtMapper;

    public function __construct(DebtMapper $debtMapper) {
        $this->debtMapper = $debtMapper;
    }

    /**
     * Create a new debt
     * @param string $userId
     * @param string $creditorName
     * @param string $type
     * @param float $totalAmount
     * @param float $interestRate
     * @param \DateTime $startDate
     * @param \DateTime $dueDate
     * @param float $minimumPayment
     * @return Debt
     */
    public function createDebt(
        string $userId,
        string $creditorName,
        string $type,
        float $totalAmount,
        float $interestRate,
        \DateTime $startDate,
        \DateTime $dueDate,
        float $minimumPayment
    ): Debt {
        // Validate input
        if ($totalAmount <= 0) {
            throw new \InvalidArgumentException('Total amount must be positive');
        }

        if ($interestRate < 0) {
            throw new \InvalidArgumentException('Interest rate cannot be negative');
        }

        if ($startDate > $dueDate) {
            throw new \InvalidArgumentException('Start date must be before due date');
        }

        $debt = new Debt();
        $debt->setUserId($userId);
        $debt->setCreditorName($creditorName);
        $debt->setType($type);
        $debt->setTotalAmount($totalAmount);
        $debt->setRemainingBalance($totalAmount);
        $debt->setInterestRate($interestRate);
        $debt->setStartDate($startDate);
        $debt->setDueDate($dueDate);
        $debt->setMinimumPayment($minimumPayment);
        $debt->setStatus('active');

        return $this->debtMapper->insert($debt);
    }

    /**
     * Make a payment towards a debt
     * @param int $debtId
     * @param string $userId
     * @param float $payment
     * @return Debt
     */
    public function makePayment(int $debtId, string $userId, float $payment): Debt {
        try {
            $debt = $this->debtMapper->find($debtId);

            // Verify user ownership
            if ($debt->getUserId() !== $userId) {
                throw new \Exception('Not authorized to modify this debt');
            }

            // Validate payment amount
            if ($payment <= 0) {
                throw new \InvalidArgumentException('Payment must be positive');
            }

            if ($payment > $debt->getRemainingBalance()) {
                $payment = $debt->getRemainingBalance();
            }

            return $this->debtMapper->makePayment($debtId, $payment);
        } catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
            throw new \Exception('Debt not found');
        }
    }

    /**
     * Analyze debt portfolio
     * @param string $userId
     * @return array
     */
    public function analyzeDebtPortfolio(string $userId): array {
        $debts = $this->debtMapper->findAll($userId);

        $analysis = [
            'totalDebt' => 0,
            'totalMonthlyPayments' => 0,
            'debtTypes' => [],
            'overdueDebts' => [],
            'payoffTimeline' => []
        ];

        foreach ($debts as $debt) {
            $analysis['totalDebt'] += $debt->getRemainingBalance();
            $analysis['totalMonthlyPayments'] += $debt->getMinimumPayment();

            // Debt type breakdown
            $type = $debt->getType();
            $analysis['debtTypes'][$type] = 
                ($analysis['debtTypes'][$type] ?? 0) + $debt->getRemainingBalance();

            // Overdue debts
            if ($debt->determineStatus() === 'overdue') {
                $analysis['overdueDebts'][] = $debt;
            }

            // Payoff timeline
            $analysis['payoffTimeline'][] = [
                'creditor' => $debt->getCreditorName(),
                'remainingBalance' => $debt->getRemainingBalance(),
                'monthsToPayoff' => $debt->estimateTimeToPayoff($debt->getMinimumPayment())
            ];
        }

        // Sort payoff timeline by months to payoff
        usort($analysis['payoffTimeline'], function($a, $b) {
            return $a['monthsToPayoff'] <=> $b['monthsToPayoff'];
        });

        return $analysis;
    }

    /**
     * Recommend debt repayment strategy
     * @param string $userId
     * @return array
     */
    public function recommendDebtRepaymentStrategy(string $userId): array {
        $analysis = $this->analyzeDebtPortfolio($userId);
        $debts = $this->debtMapper->findAll($userId);

        $recommendations = [];

        // Snowball method (pay smallest debts first)
        $snowballStrategy = array_map(function($debt) {
            return [
                'creditor' => $debt->getCreditorName(),
                'remainingBalance' => $debt->getRemainingBalance(),
                'interestRate' => $debt->getInterestRate()
            ];
        }, $debts);

        usort($snowballStrategy, function($a, $b) {
            return $a['remainingBalance'] <=> $b['remainingBalance'];
        });

        // Avalanche method (pay highest interest first)
        $avalancheStrategy = array_map(function($debt) {
            return [
                'creditor' => $debt->getCreditorName(),
                'remainingBalance' => $debt->getRemainingBalance(),
                'interestRate' => $debt->getInterestRate()
            ];
        }, $debts);

        usort($avalancheStrategy, function($a, $b) {
            return $b['interestRate'] <=> $a['interestRate'];
        });

        $recommendations = [
            'totalDebt' => $analysis['totalDebt'],
            'snowballStrategy' => array_slice($snowballStrategy, 0, 3),
            'avalancheStrategy' => array_slice($avalancheStrategy, 0, 3),
            'recommendedMethod' => $analysis['totalDebt'] > 10000 ? 'avalanche' : 'snowball'
        ];

        return $recommendations;
    }
}
