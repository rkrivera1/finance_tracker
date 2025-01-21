<?php
namespace OCA\FinanceTracker\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version1000Date20231215 extends SimpleMigrationStep {
    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        // Transactions table
        if (!$schema->hasTable('finance_transactions')) {
            $table = $schema->createTable('finance_transactions');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'primary' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('amount', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('category', 'string', [
                'notnull' => false,
                'length' => 100,
            ]);
            $table->addColumn('description', 'text', [
                'notnull' => false,
            ]);
            $table->addColumn('transaction_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('created_at', 'datetime', [
                'notnull' => true,
            ]);
            $table->addIndex(['user_id'], 'finance_trans_user_idx');
        }

        // Budgets table
        if (!$schema->hasTable('finance_budgets')) {
            $table = $schema->createTable('finance_budgets');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'primary' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('category', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('budget_amount', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('start_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('end_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addIndex(['user_id'], 'finance_budget_user_idx');
        }

        // Financial Goals table
        if (!$schema->hasTable('finance_financial_goals')) {
            $table = $schema->createTable('finance_financial_goals');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'primary' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('title', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('target_amount', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('current_amount', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
                'default' => 0,
            ]);
            $table->addColumn('category', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('start_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('target_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('status', 'string', [
                'notnull' => true,
                'length' => 20,
                'default' => 'in_progress',
            ]);
            $table->addIndex(['user_id'], 'finance_goal_user_idx');
            $table->addIndex(['status'], 'finance_goal_status_idx');
        }

        // Currency Rates table
        if (!$schema->hasTable('finance_currency_rates')) {
            $table = $schema->createTable('finance_currency_rates');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'primary' => true,
            ]);
            $table->addColumn('base_currency', 'string', [
                'notnull' => true,
                'length' => 3,
            ]);
            $table->addColumn('target_currency', 'string', [
                'notnull' => true,
                'length' => 3,
            ]);
            $table->addColumn('exchange_rate', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 4,
            ]);
            $table->addColumn('last_updated', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('source', 'string', [
                'notnull' => true,
                'length' => 50,
                'default' => 'default',
            ]);
            $table->addUniqueIndex(['base_currency', 'target_currency'], 'finance_currency_rate_unique');
            $table->addIndex(['last_updated'], 'finance_currency_rate_updated_idx');
        }

        // Debts table
        if (!$schema->hasTable('finance_debts')) {
            $table = $schema->createTable('finance_debts');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'primary' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('creditor_name', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('type', 'string', [
                'notnull' => true,
                'length' => 50,
            ]);
            $table->addColumn('total_amount', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('remaining_balance', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('interest_rate', 'decimal', [
                'notnull' => true,
                'precision' => 5,
                'scale' => 2,
            ]);
            $table->addColumn('start_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('due_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('minimum_payment', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('status', 'string', [
                'notnull' => true,
                'length' => 20,
                'default' => 'active',
            ]);
            $table->addIndex(['user_id'], 'finance_debt_user_idx');
            $table->addIndex(['type'], 'finance_debt_type_idx');
            $table->addIndex(['status'], 'finance_debt_status_idx');
        }

        // Investments table
        if (!$schema->hasTable('finance_investments')) {
            $table = $schema->createTable('finance_investments');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
                'primary' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('type', 'string', [
                'notnull' => true,
                'length' => 50,
            ]);
            $table->addColumn('purchase_price', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('quantity', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 4,
            ]);
            $table->addColumn('current_price', 'decimal', [
                'notnull' => true,
                'precision' => 10,
                'scale' => 2,
            ]);
            $table->addColumn('purchase_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('last_update_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('sector', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('risk_level', 'string', [
                'notnull' => true,
                'length' => 20,
                'default' => 'medium',
            ]);
            $table->addIndex(['user_id'], 'finance_investment_user_idx');
            $table->addIndex(['type'], 'finance_investment_type_idx');
            $table->addIndex(['sector'], 'finance_investment_sector_idx');
        }

        return $schema;
    }
}
