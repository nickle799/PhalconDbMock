<?php
namespace NickLewis\PhalconDbMock\Models;
class Database {
    /** @var  string[] */
    private $descriptor;
    /** @var  Table[] */
    private $tables = [];
    /** @var  int */
    private $lastInsertId;
    private $random;

    /**
     * Database constructor.
     * @param \string[] $descriptor
     */
    public function __construct(array $descriptor) {
        $this->random = rand(1,100000);
        $this->setDescriptor($descriptor);
    }

    /**
     * @return int
     */
    public function getLastInsertId() {
        return $this->lastInsertId;
    }

    /**
     * @param int $lastInsertId
     * @return Database
     */
    public function setLastInsertId($lastInsertId) {
        $this->lastInsertId = $lastInsertId;
        return $this;
    }



    /**
     * getTable
     * @param $name
     * @return false|Table
     */
    public function getTable($name) {
        if(!array_key_exists($name, $this->getTables())) {
            return false;
        }
        return $this->getTables()[$name];
    }

    /**
     * @return Table[]
     */
    public function getTables() {
        return $this->tables;
    }

    /**
     * addTable
     * @param Table $table
     * @return void
     * @throws DbException
     */
    public function addTable(Table $table) {
        $tables = $this->getTables();
        if(array_key_exists($table->getName(), $tables)) {
            throw new DbException('Database ('.implode(',', $this->getDescriptor()).': Duplicate Table Name ('.$table->getName().')');
        }
        $tables[$table->getName()] = $table;
        $this->setTables($tables);
    }

    /**
     * @param Table[] $tables
     * @return Database
     */
    private function setTables(array $tables) {
        $this->tables = $tables;
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getDescriptor() {
        return $this->descriptor;
    }

    /**
     * @param \string[] $descriptor
     * @return Database
     */
    private function setDescriptor(array $descriptor) {
        $this->descriptor = $descriptor;
        return $this;
    }


}