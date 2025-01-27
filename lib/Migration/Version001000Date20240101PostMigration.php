<?php
namespace OCA\FinanceTracker\Migration;

use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use OCP\IContainer;

class Version001000Date20240101PostMigration implements IRepairStep {
    private $container;

    public function __construct(IContainer $container) {
        $this->container = $container;
    }

    public function getName() {
        return 'Finance Tracker Sample Data Seeder';
    }

    public function run(IOutput $output) {
        try {
            $sampleDataSeeder = $this->container->get(SampleDataSeeder::class);
            $sampleDataSeeder->seedSampleData();
            $output->info('Successfully seeded sample data for Finance Tracker');
        } catch (\Throwable $e) {
            $output->error('Failed to seed sample data: ' . $e->getMessage());
        }
    }
}
