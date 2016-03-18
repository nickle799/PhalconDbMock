<?php
namespace NickLewis\PhalconDbMock\Services;
use NickLewis\PhalconDbMock\Models\Database;
use NickLewis\PhalconDbMock\Models\DbException;
use NickLewis\PhalconDbMock\Models\ResultSet;
use NickLewis\PhalconDbMock\Services\Parsers\Insert;
use NickLewis\PhalconDbMock\Services\Parsers\Select;
use Phalcon\Db\Adapter;
use Phalcon\Db\AdapterInterface;
use Phalcon\Db\ColumnInterface;
use Phalcon\Db\columnList;
use Phalcon\Db\dataTypes;
use Phalcon\Db\descriptor;
use Phalcon\Db\Dialect\MySQL;
use Phalcon\Db\DialectInterface;
use Phalcon\Db\IndexInterface;
use Phalcon\Db\RawValue;
use Phalcon\Db\ReferenceInterface;
use Phalcon\Db\ResultInterface;
use Phalcon\Db\sqlQuery;
use Phalcon\Db\table;
use Phalcon\Db\whereCondition;
use Phalcon\Di\InjectionAwareInterface;
use PHPSQLParser\PHPSQLParser;

class DbAdapter extends Adapter implements AdapterInterface, InjectionAwareInterface {
    use DependencyInjection;
    /** @var  Database */
    private $database;

    /**
     * Constructor for Phalcon\Db\Adapter
     *
     * @param array $descriptor
     */
    public function __construct(array $descriptor) {
        $this->setDatabase(new Database($descriptor));
    }

    /**
     * Returns the first row in a SQL query result
     *
     * @param string $sqlQuery
     * @param int    $fetchMode
     * @param int    $placeholders
     * @return array
     */
    public function fetchOne($sqlQuery, $fetchMode = 2, $bindParams = null, $placeholders = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement fetchOne() method.
    }

    /**
     * Dumps the complete result of a query into an array
     *
     * @param string $sqlQuery
     * @param int    $fetchMode
     * @param int    $placeholders
     * @return array
     */
    public function fetchAll($sqlQuery, $fetchMode = 2, $bindParams = null, $placeholders = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement fetchAll() method.
    }

    /**
     * Updates data on a table using custom RBDM SQL syntax
     *
     * @param mixed $table
     * @param mixed $fields
     * @param mixed $values
     * @param mixed $whereCondition
     * @param mixed $dataTypes
     * @param       $string whereCondition
     * @param       $array dataTypes
     * @return
     */
    public function update($table, $fields, $values, $whereCondition = null, $dataTypes = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement update() method.
    }

    /**
     * Deletes data from a table using custom RBDM SQL syntax
     *
     * @param string $table
     * @param string $whereCondition
     * @param array  $placeholders
     * @param array  $dataTypes
     * @return boolean
     */
    public function delete($table, $whereCondition = null, $placeholders = null, $dataTypes = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement delete() method.
    }

    /**
     * Gets a list of columns
     *
     * @param       array columnList
     * @return    string
     * @param mixed $columnList
     */
    public function getColumnList($columnList) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getColumnList() method.
    }

    /**
     * Appends a LIMIT clause to sqlQuery argument
     *
     * @param mixed $sqlQuery
     * @param mixed $number
     * @param       $string sqlQuery
     * @param       $int number
     * @return
     */
    public function limit($sqlQuery, $number) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement limit() method.
    }

    /**
     * Generates SQL checking for the existence of a schema.table
     *
     * @param string $tableName
     * @param string $schemaName
     * @return bool
     */
    public function tableExists($tableName, $schemaName = null) {
        return array_key_exists($tableName, $this->getDatabase()->getTables());
    }

    /**
     * Generates SQL checking for the existence of a schema.view
     *
     * @param string $viewName
     * @param string $schemaName
     * @return bool
     */
    public function viewExists($viewName, $schemaName = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement viewExists() method.
    }

    /**
     * Returns a SQL modified with a FOR UPDATE clause
     *
     * @param string $sqlQuery
     * @return string
     */
    public function forUpdate($sqlQuery) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement forUpdate() method.
    }

    /**
     * Returns a SQL modified with a LOCK IN SHARE MODE clause
     *
     * @param string $sqlQuery
     * @return string
     */
    public function sharedLock($sqlQuery) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement sharedLock() method.
    }

    /**
     * Creates a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @param array  $definition
     * @return bool
     */
    public function createTable($tableName, $schemaName, array $definition) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement createTable() method.
    }

    /**
     * Drops a table from a schema/database
     *
     * @param string $tableName
     * @param string $schemaName
     * @param bool   $ifExists
     * @return bool
     */
    public function dropTable($tableName, $schemaName = null, $ifExists = true) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement dropTable() method.
    }

    /**
     * Creates a view
     *
     * @param string $viewName
     * @param array  $definition
     * @param string $schemaName
     * @return bool
     */
    public function createView($viewName, array $definition, $schemaName = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement createView() method.
    }

    /**
     * Drops a view
     *
     * @param string $viewName
     * @param string $schemaName
     * @param bool   $ifExists
     * @return bool
     */
    public function dropView($viewName, $schemaName = null, $ifExists = true) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement dropView() method.
    }

    /**
     * Adds a column to a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @param mixed  $column
     * @return bool
     */
    public function addColumn($tableName, $schemaName, ColumnInterface $column) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement addColumn() method.
    }

    /**
     * Modifies a table column based on a definition
     *
     * @param string $tableName
     * @param string $schemaName
     * @param mixed  $column
     * @param mixed  $currentColumn
     * @return bool
     */
    public function modifyColumn($tableName, $schemaName, ColumnInterface $column, ColumnInterface $currentColumn = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement modifyColumn() method.
    }

    /**
     * Drops a column from a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @param string $columnName
     * @return bool
     */
    public function dropColumn($tableName, $schemaName, $columnName) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement dropColumn() method.
    }

    /**
     * Adds an index to a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @param mixed  $index
     * @return bool
     */
    public function addIndex($tableName, $schemaName, IndexInterface $index) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement addIndex() method.
    }

    /**
     * Drop an index from a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @param string $indexName
     * @return bool
     */
    public function dropIndex($tableName, $schemaName, $indexName) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement dropIndex() method.
    }

    /**
     * Adds a primary key to a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @param mixed  $index
     * @return bool
     */
    public function addPrimaryKey($tableName, $schemaName, IndexInterface $index) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement addPrimaryKey() method.
    }

    /**
     * Drops primary key from a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @return bool
     */
    public function dropPrimaryKey($tableName, $schemaName) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement dropPrimaryKey() method.
    }

    /**
     * Adds a foreign key to a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @param mixed  $reference
     * @return bool
     */
    public function addForeignKey($tableName, $schemaName, ReferenceInterface $reference) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement addForeignKey() method.
    }

    /**
     * Drops a foreign key from a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @param string $referenceName
     * @return bool
     */
    public function dropForeignKey($tableName, $schemaName, $referenceName) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement dropForeignKey() method.
    }

    /**
     * Returns the SQL column definition from a column
     *
     * @param mixed $column
     * @return string
     */
    public function getColumnDefinition(ColumnInterface $column) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getColumnDefinition() method.
    }

    /**
     * List all tables on a database
     *
     * @param string $schemaName
     * @return array
     */
    public function listTables($schemaName = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement listTables() method.
    }

    /**
     * List all views on a database
     *
     * @param string $schemaName
     * @return array
     */
    public function listViews($schemaName = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement listViews() method.
    }

    /**
     * Return descriptor used to connect to the active database
     *
     * @return array
     */
    public function getDescriptor() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getDescriptor() method.
    }

    /**
     * Gets the active connection unique identifier
     *
     * @return string
     */
    public function getConnectionId() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getConnectionId() method.
    }

    /**
     * Active SQL statement in the object
     *
     * @return string
     */
    public function getSQLStatement() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getSQLStatement() method.
    }

    /**
     * Active SQL statement in the object without replace bound paramters
     *
     * @return string
     */
    public function getRealSQLStatement() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getRealSQLStatement() method.
    }

    /**
     * Active SQL statement in the object
     *
     * @return array
     */
    public function getSQLVariables() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getSQLVariables() method.
    }

    /**
     * Active SQL statement in the object
     *
     * @return array
     */
    public function getSQLBindTypes() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getSQLBindTypes() method.
    }

    /**
     * Returns type of database system the adapter is used for
     *
     * @return string
     */
    public function getType() {
        return 'PhalconDbMock';
    }

    /**
     * Returns the name of the dialect used
     *
     * @return string
     */
    public function getDialectType() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getDialectType() method.
    }

    /**
     * Returns internal dialect instance
     *
     * @return DialectInterface
     */
    public function getDialect() {
        return new MySQL();
    }

    /**
     * This method is automatically called in Phalcon\Db\Adapter\Pdo constructor.
     * Call it when you need to restore a database connection
     *
     * @param mixed $descriptor
     * @param       $array descriptor
     * @return
     */
    public function connect($descriptor = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement connect() method.
    }

    /**
     * Sends SQL statements to the database server returning the success state.
     * Use this method only when the SQL statement sent to the server return rows
     *
     * @param string $sqlStatement
     * @param mixed  $placeholders
     * @param mixed  $dataTypes
     * @return bool|ResultInterface
     */
    public function query($sqlStatement, $placeholders = null, $dataTypes = null) {
        if(!is_null($placeholders)) {
            $sql = $this->replaceParams($sqlStatement, $placeholders);
        } else {
            $sql = $sqlStatement;
        }

        $parser = new PHPSQLParser();
        $parsed = $parser->parse($sql);

        $type = array_keys($parsed)[0];
        switch($type) {
            case 'SELECT':
                $insert = new Select($this->getDatabase());
                $result = $insert->process($parsed);
                break;
            default:
                throw new DbException('DbAdapter: Query Type not allowed: '.$type);
                break;
        }
        return new ResultSet($this, $result, $sqlStatement, $placeholders);
    }

    private function mysqlEscapeString($value) {
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", '\'', '\"', "\\Z");

        return "'".str_replace($search, $replace, $value)."'";
    }

    /**
     * replaceParams
     * @param       $sql
     * @param array $params
     * @return string
     */
    private function replaceParams($sql, array $params) {
        foreach($params as $key=>$value) {
            if(is_numeric($key)) {
                $search = '?';
            } else {
                $search = $key;
                if($search[0] != ':') {
                    $search = ':' . $search;
                }
            }
            $strPos = strPos($sql, $search);
            $sql = substr($sql, 0, $strPos).$this->mysqlEscapeString($value).substr($sql, $strPos+strlen($search));
        }
        return $sql;
    }

    /**
     * Sends SQL statements to the database server returning the success state.
     * Use this method only when the SQL statement sent to the server doesn't return any rows
     *
     * @param string $sqlStatement
     * @param mixed  $placeholders
     * @param mixed  $dataTypes
     * @return bool
     */
    public function execute($sqlStatement, $placeholders = null, $dataTypes = null) {
        if(!is_null($placeholders)) {
            $sqlStatement = $this->replaceParams($sqlStatement, $placeholders);
        }
        $parser = new PHPSQLParser();
        $parsed = $parser->parse($sqlStatement);

        $type = array_keys($parsed)[0];
        switch($type) {
            case 'INSERT':
                $insert = new Insert($this->getDatabase());
                $insert->process($parsed);
                break;
            default:
                throw new DbException('DbAdapter: Execute Type not allowed: '.$type);
                break;
        }
        return true;
    }

    /**
     * Returns the number of affected rows by the last INSERT/UPDATE/DELETE reported by the database system
     *
     * @return int
     */
    public function affectedRows() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement affectedRows() method.
    }

    /**
     * Closes active connection returning success. Phalcon automatically closes and destroys active connections within Phalcon\Db\Pool
     *
     * @return bool
     */
    public function close() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement close() method.
    }

    /**
     * Escapes a column/table/schema name
     *
     * @param string $identifier
     * @return string
     */
    public function escapeIdentifier($identifier) {
        if(is_array($identifier)) {
            list($domain, $name) = $identifier;
            return '`'.$domain.'`.`'.$name.'`';
        }
        return '`'.$identifier.'`';
    }

    /**
     * Escapes a value to avoid SQL injections
     *
     * @param string $str
     * @return string
     */
    public function escapeString($str) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement escapeString() method.
    }

    /**
     * Returns insert id for the auto_increment column inserted in the last SQL statement
     *
     * @param string $sequenceName
     * @return int
     */
    public function lastInsertId($sequenceName = null) {
        return $this->getDatabase()->getLastInsertId();
    }

    /**
     * Starts a transaction in the connection
     *
     * @param bool $nesting
     * @return bool
     */
    public function begin($nesting = true) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement begin() method.
    }

    /**
     * Rollbacks the active transaction in the connection
     *
     * @param bool $nesting
     * @return bool
     */
    public function rollback($nesting = true) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement rollback() method.
    }

    /**
     * Commits the active transaction in the connection
     *
     * @param bool $nesting
     * @return bool
     */
    public function commit($nesting = true) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement commit() method.
    }

    /**
     * Checks whether connection is under database transaction
     *
     * @return bool
     */
    public function isUnderTransaction() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement isUnderTransaction() method.
    }

    /**
     * Return internal PDO handler
     *
     * @return \Pdo
     */
    public function getInternalHandler() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getInternalHandler() method.
    }

    /**
     * Lists table indexes
     *
     * @param string $table
     * @param string $schema
     * @return IndexInterface
     */
    public function describeIndexes($table, $schema = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement describeIndexes() method.
    }

    /**
     * Lists table references
     *
     * @param string $table
     * @param string $schema
     * @return ReferenceInterface
     */
    public function describeReferences($table, $schema = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement describeReferences() method.
    }

    /**
     * Gets creation options from a table
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function tableOptions($tableName, $schemaName = null) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement tableOptions() method.
    }

    /**
     * Creates a new savepoint
     *
     * @param string $name
     * @return bool
     */
    public function createSavepoint($name) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement createSavepoint() method.
    }

    /**
     * Releases given savepoint
     *
     * @param string $name
     * @return bool
     */
    public function releaseSavepoint($name) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement releaseSavepoint() method.
    }

    /**
     * Rollbacks given savepoint
     *
     * @param string $name
     * @return bool
     */
    public function rollbackSavepoint($name) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement rollbackSavepoint() method.
    }

    /**
     * Set if nested transactions should use savepoints
     *
     * @param bool $nestedTransactionsWithSavepoints
     * @return AdapterInterface
     */
    public function setNestedTransactionsWithSavepoints($nestedTransactionsWithSavepoints) {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement setNestedTransactionsWithSavepoints() method.
    }

    /**
     * Returns if nested transactions should use savepoints
     *
     * @return bool
     */
    public function isNestedTransactionsWithSavepoints() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement isNestedTransactionsWithSavepoints() method.
    }

    /**
     * Returns the savepoint name to use for nested transactions
     *
     * @return string
     */
    public function getNestedTransactionSavepointName() {
        throw new \Exception('To Implement: '.__CLASS__.'::'.__FUNCTION__);// TODO: Implement getNestedTransactionSavepointName() method.
    }

    /**
     * Returns an array of Phalcon\Db\Column objects describing a table
     *
     * @param string $table
     * @param string $schema
     * @return ColumnInterface
     */
    public function describeColumns($table, $schema = null) {
        $table = $this->getDatabase()->getTable($table);
        return $table->getColumns();
    }

    /**
     * Getter
     * @return Database
     */
    public function getDatabase() {
        return $this->database;
    }

    /**
     * Setter
     * @param Database $database
     * @return DbAdapter
     */
    private function setDatabase(Database $database) {
        $this->database = $database;
        return $this;
    }



}