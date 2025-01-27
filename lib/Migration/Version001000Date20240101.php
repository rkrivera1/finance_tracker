<?php
namespace OCA\FinanceTracker\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version001000Date20240101 extends SimpleMigrationStep {
    /**
     * Create or modify database schema
     *
     * @param IOutput $output Migration output interface
     * @param Closure $schemaClosure Closure that returns schema wrapper
     * @param array $options Additional options
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
                'unsigned' => true,
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
                'default' => 0,
            ]);
            $table->addColumn('created_at', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('updated_at', 'datetime', [
                'notnull' => false,
                'default' => null,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['user_id', 'name'], 'ft_acct_user_name_idx');
            $table->addIndex(['user_id'], 'ft_acct_user_idx');
        }

        // Transactions Table
        if (!$schema->hasTable('finance_transactions')) {
            $table = $schema->createTable('finance_transactions');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('account_id', 'integer', [
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('amount', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('category', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('description', 'text', [
                'notnull' => false,
            ]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'tx_user_idx');
            $table->addIndex(['account_id'], 'tx_acc_idx');
            $table->addIndex(['date'], 'tx_date_idx');
        }

        // Investments Table
        if (!$schema->hasTable('finance_investments')) {
            $table = $schema->createTable('finance_investments');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('symbol', 'string', [
                'notnull' => true,
                'length' => 10,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 200,
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
            $table->addColumn('purchase_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'inv_user_idx');
            $table->addIndex(['symbol'], 'inv_sym_idx');
            $table->addUniqueIndex(['user_id', 'symbol', 'purchase_date'], 'inv_unique_idx');
        }

        // Budgets Table
        if (!$schema->hasTable('finance_budgets')) {
            $table = $schema->createTable('finance_budgets');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'unsigned' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('category', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('amount', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('period', 'string', [
                'notnull' => true,
                'length' => 20,
            ]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'budg_user_idx');
            $table->addUniqueIndex(['user_id', 'category', 'period'], 'budg_unique_idx');
        }

        return $schema;
    }
}
