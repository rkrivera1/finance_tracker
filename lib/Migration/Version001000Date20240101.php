<?php
namespace OCA\FinanceTracker\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version001000Date20240101 extends SimpleMigrationStep {
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
        if (!$schema->hasTable('finance_accounts')) {
            $table = $schema->createTable('finance_accounts');
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
                'default' => 0,
            ]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'finance_accounts_user_idx');
        }

        // Transactions Table
        if (!$schema->hasTable('finance_transactions')) {
            $table = $schema->createTable('finance_transactions');
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
            $table->addIndex(['user_id'], 'finance_transactions_user_idx');
            $table->addIndex(['account_id'], 'finance_transactions_account_idx');
        }

        // Investments Table
        if (!$schema->hasTable('finance_investments')) {
            $table = $schema->createTable('finance_investments');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
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
            $table->addIndex(['user_id'], 'finance_investments_user_idx');
            $table->addIndex(['symbol'], 'finance_investments_symbol_idx');
        }

        // Budgets Table
        if (!$schema->hasTable('finance_budgets')) {
            $table = $schema->createTable('finance_budgets');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
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
            $table->addIndex(['user_id'], 'finance_budgets_user_idx');
        }

        return $schema;
    }
}
