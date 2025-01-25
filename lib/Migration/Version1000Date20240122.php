<?php
namespace OCA\FinanceTracker\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1000Date20240122 extends SimpleMigrationStep {
    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        // Accounts Table
        if (!$schema->hasTable('finance_tracker_accounts')) {
            $table = $schema->createTable('finance_tracker_accounts');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('type', 'string', [
                'notnull' => true,
                'length' => 50,
            ]);
            $table->addColumn('balance', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('created_at', 'datetime', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'ft_accounts_user');
        }

        // Budgets Table
        if (!$schema->hasTable('finance_tracker_budgets')) {
            $table = $schema->createTable('finance_tracker_budgets');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('amount', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('category', 'string', [
                'notnull' => true,
                'length' => 50,
            ]);
            $table->addColumn('start_date', 'date', [
                'notnull' => true,
            ]);
            $table->addColumn('end_date', 'date', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'ft_budgets_user');
        }

        // Transactions Table
        if (!$schema->hasTable('finance_tracker_transactions')) {
            $table = $schema->createTable('finance_tracker_transactions');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('account_id', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('description', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('amount', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('type', 'string', [
                'notnull' => true,
                'length' => 50,
            ]);
            $table->addColumn('date', 'datetime', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'ft_trans_user');
            $table->addIndex(['account_id'], 'ft_trans_account');
        }

        // Investments Table
        if (!$schema->hasTable('finance_tracker_investments')) {
            $table = $schema->createTable('finance_tracker_investments');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('ticker', 'string', [
                'notnull' => true,
                'length' => 10,
            ]);
            $table->addColumn('shares', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 4,
            ]);
            $table->addColumn('purchase_price', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('purchase_date', 'date', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'ft_invest_user');
        }

        return $schema;
    }
}
