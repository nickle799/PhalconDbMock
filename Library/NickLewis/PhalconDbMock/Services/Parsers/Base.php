<?php
namespace NickLewis\PhalconDbMock\Services\Parsers;
use NickLewis\PhalconDbMock\Models\Database;
use Phalcon\Db\ResultInterface;

abstract class Base {
    /** @var  Database */
    private $database;
    /** @var  array */
    private $parsed;

    /**
     * Parser constructor.
     * @param Database $database
     */
    public function __construct(Database $database) {
        $this->setDatabase($database);
    }

    /**
     * parse
     * @param array $parsed
     * @return ResultInterface
     */
    public function process(array $parsed) {
        $this->setParsed($parsed);
    }

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

    /**
     * Getter
     * @return array
     */
    public function getParsed() {
        return $this->parsed;
    }

    /**
     * Setter
     * @param array $parsed
     * @return Base
     */
    public function setParsed(array $parsed) {
        $this->parsed = $parsed;
        return $this;
    }



}