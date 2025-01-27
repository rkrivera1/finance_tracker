<?php
namespace OCA\FinanceTracker\Db;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

abstract class BaseMapper {
    /** @var IDBConnection */
    protected $db;

    /** @var string */
    protected $tableName;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    /**
     * Find all records
     *
     * @return array
     */
    public function findAll() {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->tableName);

        return $qb->executeQuery()->fetchAll();
    }

    /**
     * Find a record by ID
     *
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
           ->from($this->tableName)
           ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));

        $result = $qb->executeQuery();
        $row = $result->fetch();
        $result->closeCursor();

        return $row ?: null;
    }

    /**
     * Insert a new record
     *
     * @param array $data
     * @return array Inserted record
     */
    public function insert(array $data) {
        $qb = $this->db->getQueryBuilder();
        $qb->insert($this->tableName);

        foreach ($data as $column => $value) {
            $qb->setValue($column, $qb->createNamedParameter($value));
        }

        $qb->executeStatement();
        $id = $qb->getLastInsertId();

        return $this->find($id);
    }

    /**
     * Update a record
     *
     * @param int $id
     * @param array $data
     * @return array Updated record
     */
    public function update($id, array $data) {
        $qb = $this->db->getQueryBuilder();
        $qb->update($this->tableName);

        foreach ($data as $column => $value) {
            $qb->set($column, $qb->createNamedParameter($value));
        }

        $qb->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        $qb->executeStatement();

        return $this->find($id);
    }

    /**
     * Delete a record
     *
     * @param int $id
     */
    public function delete($id) {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->tableName)
           ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        $qb->executeStatement();
    }
}
