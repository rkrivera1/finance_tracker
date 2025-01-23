<?php
namespace OCA\FinanceTracker\Migrations;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version20250122000000 extends SimpleMigrationStep {
    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('finance_tracker_accounts')) {
            $table = $schema->createTable('finance_tracker_accounts');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'length' => 4,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('type', 'string', [
                'notnull' => true,
                'length' => 50,
            ]);
            $table->addColumn('balance', 'float', [
                'notnull' => true,
                'default' => 0,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('created_at', 'datetime', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'finance_tracker_user_idx');
        }

        if (!$schema->hasTable('finance_tracker_budgets')) {
            $table = $schema->createTable('finance_tracker_budgets');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'length' => 4,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('amount', 'float', [
                'notnull' => true,
            ]);
            $table->addColumn('category', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('start_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('end_date', 'datetime', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'finance_tracker_budget_user_idx');
        }

        return $schema;
    }
}
