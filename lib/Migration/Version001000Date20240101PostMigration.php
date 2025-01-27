<?php
namespace OCA\FinanceTracker\Migration;

use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use OCP\IContainer;

class Version001000Date20240101PostMigration implements IRepairStep {
    /** @var SampleDataSeeder */
    private $sampleDataSeeder;

    /**
     * Constructor
     *
     * @param SampleDataSeeder $sampleDataSeeder Dependency for seeding sample data
     */
    public function __construct(SampleDataSeeder $sampleDataSeeder) {
        $this->sampleDataSeeder = $sampleDataSeeder;
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
     */
    public function run(IOutput $output) {
        try {
            $this->sampleDataSeeder->seedSampleData();
            $output->info('Successfully seeded sample data for Finance Tracker');
        } catch (\Throwable $e) {
            $output->error('Failed to seed sample data: ' . $e->getMessage());
            // Log the full exception for debugging
            $output->error('Exception trace: ' . $e->getTraceAsString());
        }
    }
}
