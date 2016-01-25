<?php
namespace NickLewis\PhalconDbMock\Models;

use NickLewis\PhalconDbMock\Services\DbAdapter;
use Phalcon\Db\ResultInterface;

class ResultSet implements ResultInterface, \Iterator {
    /** @var  array */
    private $rows;
    /** @var int  */
    private $position = 0;
    /** @var  PDOStatement */
    private $pdoStatement;

    /**
     * Phalcon\Db\Result\Pdo constructor
     *
     * @param DbAdapter $connection
     * @param PDOStatement                 $result
     * @param string                       $sqlStatement
     * @param array                        $bindParams
     * @param array                        $bindTypes
     */
    public function __construct(\Phalcon\Db\AdapterInterface $connection, \PDOStatement $result, $sqlStatement = null, $bindParams = null, $bindTypes = null) {
        $this->pdoStatement = $result;
        $this->rows = $result->getRows();
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current() {
        return $this->rows[$this->position];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next() {
        $this->position++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key() {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid() {
        return array_key_exists($this->position, $this->rows);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * Allows to executes the statement again. Some database systems don't support scrollable cursors,
     * So, as cursors are forward only, we need to execute the cursor again to fetch rows from the begining
     *
     * @return boolean
     */
    public function execute() {
        $this->rewind();
        return true;
    }

    /**
     * Fetches an array/object of strings that corresponds to the fetched row, or FALSE if there are no more rows.
     * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
     *
     * @return mixed
     */
    public function fetch() {
        return $this->current();
    }

    /**
     * Returns an array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
     * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
     *
     * @return mixed
     */
    public function fetchArray() {
        return $this->current();
    }

    /**
     * Returns an array of arrays containing all the records in the result
     * This method is affected by the active fetch flag set using Phalcon\Db\Result\Pdo::setFetchMode
     *
     * @return array
     */
    public function fetchAll() {
        return $this->rows;
    }

    /**
     * Gets number of rows returned by a resultset
     *
     * @return int
     */
    public function numRows() {
        return count($this->rows);
    }

    /**
     * Moves internal resultset cursor to another position letting us to fetch a certain row
     *
     * @param int $number
     */
    public function dataSeek($number) {
        $this->position = $number;
    }

    /**
     * Changes the fetching mode affecting Phalcon\Db\Result\Pdo::fetch()
     *
     * @param int $fetchMode
     * @return bool
     */
    public function setFetchMode($fetchMode) {
        // TODO: Implement setFetchMode() method.
    }

    /**
     * Gets the internal PDO result object
     *
     * @return \PDOStatement
     */
    public function getInternalResult() {
        return $this->pdoStatement;
    }

}