<?php

namespace OCA\FinanceTracker\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20250122000000 extends AbstractMigration {
	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema): void {
		$prefix = $this->connection->getPrefix();
		
		if (!$schema->hasTable($prefix . 'finance_accounts')) {
			$table = $schema->createTable($prefix . 'finance_accounts');
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
			]);
			$table->addColumn('balance', 'decimal', [
				'notnull' => true,
				'precision' => 15,
				'scale' => 2,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'finance_accounts_user_id_idx');
		}
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema): void {
		$prefix = $this->connection->getPrefix();
		
		if ($schema->hasTable($prefix . 'finance_accounts')) {
			$schema->dropTable($prefix . 'finance_accounts');
		}
	}
}