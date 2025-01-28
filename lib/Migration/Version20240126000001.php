<?php

namespace OCA\FinanceTracker\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version20240126000001 extends SimpleMigrationStep {

	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('finance_categories')) {
			$table = $schema->createTable('finance_categories');
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
				'length' => 64,
			]);
			$table->addColumn('type', 'string', [
				'notnull' => true,
				'length' => 32,
				'default' => 'expense',
			]);
			$table->addColumn('color', 'string', [
				'notnull' => false,
				'length' => 7,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'finance_categories_user_idx');
		}

		return $schema;
	}
}