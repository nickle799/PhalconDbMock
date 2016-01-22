<?php
namespace NickLewis\PhalconDbMock\Services\Parsers;
use NickLewis\PhalconDbMock\Models\Database;

abstract class Base {
    /** @var  Database */
    private $database;

    /**
     * Parser constructor.
     * @param Database $database
     */
    public function __construct(Database $database) {
        $this->setDatabase($database);
    }

    /**
     * parse
     * @param string $sql
     * @param array  $params
     * @return bool
     */
    abstract public function parse($sql, array $params);

    /**
     * @return Database
     */
    protected function getDatabase() {
        return $this->database;
    }

    /**
     * @param Database $database
     * @return $this
     */
    private function setDatabase(Database $database) {
        $this->database = $database;
        return $this;
    }


}