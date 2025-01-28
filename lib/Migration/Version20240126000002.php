<?php

namespace OCA\FinanceTracker\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version20240126000002 extends SimpleMigrationStep {

	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

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
			$table->addColumn('category_id', 'integer', [
				'notnull' => true,
			]);
			$table->addColumn('description', 'string', [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn('amount', 'decimal', [
				'notnull' => true,
				'precision' => 15,
				'scale' => 2,
			]);
			$table->addColumn('date', 'datetime', [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'finance_transactions_user_idx');
			$table->addForeignKeyConstraint(
				$schema->getTable('finance_accounts'),
				['account_id'],
				['id'],
				['onDelete' => 'CASCADE']
			);
			$table->addForeignKeyConstraint(
				$schema->getTable('finance_categories'),
				['category_id'],
				['id'],
				['onDelete' => 'CASCADE']
			);
		}

		return $schema;
	}
}