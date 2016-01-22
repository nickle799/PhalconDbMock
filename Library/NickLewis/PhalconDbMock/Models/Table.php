<?php
namespace NickLewis\PhalconDbMock\Models;
use Phalcon\Db\Column;
class Table {
    /** @var  string */
    private $name;
    /** @var  Column[] */
    private $columns = [];
    /** @var Row[] */
    private $rows = [];
    /** @var int  */
    private $autoIncrementValue = 1;
    /** @var  Database */
    private $database;

    /**
     * Table constructor.
     * @param Database $database
     * @param string   $name
     */
    public function __construct(Database $database, $name) {
        $this->setDatabase($database);
        $this->setName($name);
    }

    /**
     * @return Database
     */
    private function getDatabase() {
        return $this->database;
    }

    /**
     * @param Database $database
     * @return Table
     */
    private function setDatabase(Database $database) {
        $this->database = $database;
        return $this;
    }



    /**
     * getNextAutoIncrementValue
     * @return int
     */
    private function getNextAutoIncrementValue() {
        $returnVar = $this->getAutoIncrementValue();
        $this->setAutoIncrementValue($returnVar+1);
        $this->getDatabase()->setLastInsertId($returnVar);
        return $returnVar;
    }

    /**
     * Getter
     * @return int
     */
    public function getAutoIncrementValue() {
        return $this->autoIncrementValue;
    }

    /**
     * Setter
     * @param int $autoIncrementValue
     * @return Table
     */
    public function setAutoIncrementValue($autoIncrementValue) {
        $this->autoIncrementValue = $autoIncrementValue;
        return $this;
    }



    /**
     * addRowData
     * @param array $data
     * @return void
     */
    public function addRowData(array $data) {
        //TODO - unique constraint
        $columns = $this->getColumns();
        $actualData = [];
        //TODO - auto increment
        foreach($columns as $column) {
            $value = $column->getDefault();
            if(array_key_exists($column->getName(), $data)) {
                $value = $data[$column->getName()];
            }
            if(is_null($value) && $column->isAutoIncrement()) {
                $value = $this->getNextAutoIncrementValue();
            }
            $actualData[$column->getName()] = $value;
        }
        $row = new Row($actualData);
        $rows = $this->getRows();
        $rows[] = $row;
        $this->setRows($rows);
    }

    /**
     * @return Row[]
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * @param Row[] $rows
     * @return Table
     */
    private function setRows(array $rows) {
        $this->rows = $rows;
        return $this;
    }



    /**
     * addColumn
     * @param Column $column
     * @return void
     * @throws DbException
     */
    public function addColumn(Column $column) {
        $columns = $this->getColumns();
        if(array_key_exists($column->getName(), $columns)) {
            throw new DbException('Table ('.$this->getName().': Duplicate Column Name ('.$column->getName().')');
        }
        $columns[$column->getName()] = $column;
        $this->setColumns($columns);
    }

    /**
     * Getter
     * @return Column[]
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * Setter
     * @param Column[] $columns
     * @return Table
     */
    private function setColumns(array $columns) {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * setName
     * @param string $name
     * @return $this
     * @throws DbException
     */
    public function setName($name) {
        if($name=='') {
            throw new DbException('Table: Name cannot be blank');
        }
        $this->name = $name;
        return $this;
    }


}