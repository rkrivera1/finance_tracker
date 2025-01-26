<?php
namespace OCA\FinanceTracker\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1001Date20240123 extends SimpleMigrationStep {
    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        // Ensure tables exist before modifying
        if ($schema->hasTable('finance_tracker_accounts')) {
            $table = $schema->getTable('finance_tracker_accounts');
            
            // Remove existing user_id index if it exists
            if ($table->hasIndex('user_id')) {
                $table->dropIndex('user_id');
            }
        }

        if ($schema->hasTable('finance_tracker_budgets')) {
            $table = $schema->getTable('finance_tracker_budgets');
            
            // Remove existing user_id index if it exists
            if ($table->hasIndex('user_id')) {
                $table->dropIndex('user_id');
            }
        }

        if ($schema->hasTable('finance_tracker_transactions')) {
            $table = $schema->getTable('finance_tracker_transactions');
            
            // Remove existing user_id and account_id indexes if they exist
            if ($table->hasIndex('user_id')) {
                $table->dropIndex('user_id');
            }
            if ($table->hasIndex('account_id')) {
                $table->dropIndex('account_id');
            }
        }

        if ($schema->hasTable('finance_tracker_investments')) {
            $table = $schema->getTable('finance_tracker_investments');
            
            // Remove existing user_id index if it exists
            if ($table->hasIndex('user_id')) {
                $table->dropIndex('user_id');
            }
        }

        return $schema;
    }
}
