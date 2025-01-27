<?php
namespace OCA\FinanceTracker\Migration;

use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use OCP\DB\IConnection;
use OCP\IUserManager;
use Psr\Log\LoggerInterface;

class Version001000Date20240101PostMigration implements IRepairStep {
    /** @var IConnection */
    private $db;

    /** @var IUserManager */
    private $userManager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Constructor with direct dependency injection
     *
     * @param IConnection $db Database connection
     * @param IUserManager $userManager User management service
     * @param LoggerInterface $logger Logging service
     */
    public function __construct(
        IConnection $db, 
        IUserManager $userManager, 
        LoggerInterface $logger
    ) {
        $this->db = $db;
        $this->userManager = $userManager;
        $this->logger = $logger;
    }

    /**
     * Get the name of this repair step
     *
     * @return string
     */
    public function getName() {
        return 'Finance Tracker Sample Data Seeder';
    }

    /**
     * Run the repair step
     *
     * @param IOutput $output Migration output interface
     * @throws \Exception If sample data seeding fails
     */
    public function run(IOutput $output) {
        try {
            // Create SampleDataSeeder with direct dependencies
            $sampleDataSeeder = new SampleDataSeeder($this->db, $this->userManager);
            $sampleDataSeeder->seedSampleData();

            $output->info('Successfully seeded sample data for Finance Tracker');
            $this->logger->info('Finance Tracker sample data seeded successfully');
        } catch (\Throwable $e) {
            // Comprehensive error logging
            $errorMessage = 'Finance Tracker Sample Data Seeding Failed: ' . $e->getMessage();
            
            $output->error($errorMessage);
            $this->logger->error($errorMessage, [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Rethrow to ensure Nextcloud is aware of the failure
            throw new \Exception($errorMessage, 0, $e);
        }
    }
}
