<?php
namespace NickLewis\PhalconDbMock\Services\Parsers;
use NickLewis\PhalconDbMock\Models\DbException;

class Insert extends Base {
    /** @var  string */
    private $sql;
    /** @var  array */
    private $params;
    /** @var  string */
    private $tableName;
    /** @var  string[] */
    private $columns;
    /** @var  array */
    private $data;

    /**
     * parse
     * @param string $sql
     * @param array  $params
     * @return bool
     */
    public function parse($sql, array $params) {
        $this->setSql($sql);
        $this->setParams($params);
        $this->parseTableName();
        $this->parseColumns();
        $this->parseData();
        $this->insert();
//        $this->getDatabase()->getTable('MockModel')->addRowData(['columnA'=>'aw','columnB'=>'man']);
    }

    private function insert() {
        $table = $this->getDatabase()->getTable($this->getTableName());
        foreach($this->getData() as $row) {
            $table->addRowData(array_combine($this->getColumns(), $row));
        }
    }

    /**
     * parseTableName
     * @return void
     * @throws DbException
     */
    private function parseTableName() {
        $sql = trim($this->getSql());
        if(!preg_match('@^INSERT\s+INTO\s+(`(?P<tableNameA>[^`]+)`|(?P<tableNameB>[^\s]+))\s+@i', $sql, $matches)) {
            throw new DbException('Insert: Could not parse tableName: '.$sql);
        }
        if(array_key_exists('tableNameB', $matches)) {
            $tableName = $matches['tableNameB'];
        } else {
            $tableName = $matches['tableNameA'];
        }
        $this->setTableName($tableName);
        //Use up insert
        $sql = substr($sql, strlen($matches[0]));
        $this->setSql($sql);
    }

    private function parseData() {
        $sql = trim($this->getSql());

        if(!preg_match('@^VALUES\s+\(@', $sql, $matches)) {
            throw new DbException('Insert: Cannot parse data: '.$sql);
        }
        $sql = substr($sql, strlen($matches[0])-1); //-1 to keep the opening parenthesis
        $rows = [];
        $currentRow = [];
        $currentData = null;
        $inTick = false;
        $tick = null;
        $possibleTicks = ['`', '"', '\''];
        $insideRow = false;
        for($i=0;$i<strlen($sql);$i++) {
            $char = $sql[$i];
            if($inTick) {
                if($char==$tick) {
                    if($tick=='`') {
                        throw new DbException('Insert: Not able to parse ` columns yet');
                    }
                    $inTick = false;
                    $currentRow[] = $currentData;
                    $currentData = null;
                } else {
                    $currentData .= $char;
                }
            } else {
                if($char=='(') {
                    $insideRow = true;
                } elseif($char==')') {
                    if(!is_null($currentData)) {
                        $currentRow[] = $this->parseCurrentData($currentData, sizeOf($currentRow));
                        $currentData = null;
                    }
                    $rows[] = $currentRow;
                    $currentRow = [];
                } elseif($char==',') {
                    if($insideRow) {
                        if (!is_null($currentData)) {
                            $currentRow[] = $this->parseCurrentData($currentData, sizeOf($currentRow));
                            $currentData = null;
                        }
                    }
                } elseif(in_array($char, $possibleTicks)) {
                    $tick = $char;
                    $inTick = true;
                } elseif($char==' ') {
                    continue;
                } else {
                    $currentData .= $char;
                }
            }
        }
        $this->setData($rows);
    }

    private function parseCurrentData($currentData, $columnIndex) {
        if($currentData=='?') {
            $params = $this->getParams();
            $currentData = array_shift($params);
            $this->setParams($params);
        } elseif($currentData=='DEFAULT') {
            $columnName = $this->getColumns()[$columnIndex];
            $column = $this->getDatabase()->getTable($this->getTableName())->getColumns()[$columnName];
            $currentData = $column->getDefault();
        } else {
            throw new DbException('Insert: Not able to handle raw value of: '.$currentData);
        }
        return $currentData;
    }

    private function parseColumns() {
        $sql = trim($this->getSql());
        if(preg_match('@^\(SELECT\s+@', $sql)) {
            throw new DbException('Insert: Cannot parse sub selects yet');
        }
        $columns = [];
        $fullString = '(';
        if(preg_match('@^\(@', $sql)) {
            $inTick = false;
            $column = '';
            for ($i = 1; $i < strlen($sql); $i++) {
                $char = $sql[$i];
                $fullString .= $char;
                if($inTick) {
                    if($char=='`') {
                        $columns[] = $column;
                        $column = '';
                        $inTick = false;
                    } else {
                        $column .= $char;
                    }
                } else {
                    if($char==')') {
                        if($column!='') {
                            $columns[] = $column;
                        }
                        break;
                    } elseif($char==',') {
                        if($column!='') {
                            $columns[] = $column;
                            $column = '';
                        }
                    } elseif($char=='`') {
                        $inTick = true;
                    } elseif($char==' ') {
                        continue;
                    } else {
                        $column .= $char;
                    }
                }
            }
            //Use up insert
            $sql = trim(substr($sql, strlen($fullString)));
            $this->setSql($sql);
        } else {
            //Columns not defined
            foreach($this->getDatabase()->getTable($this->getTableName())->getColumns() as $column) {
                $columns[] = $column->getName();
            }
        }
        $this->setColumns($columns);
    }

    /**
     * @return string
     */
    public function getSql() {
        return $this->sql;
    }

    /**
     * @param string $sql
     * @return Insert
     */
    public function setSql($sql) {
        $this->sql = $sql;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * @param array $params
     * @return Insert
     */
    public function setParams(array $params) {
        $this->params = $params;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     * @return Insert
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return \string[]
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * @param \string[] $columns
     * @return Insert
     */
    public function setColumns(array $columns) {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param array $data
     * @return Insert
     */
    public function setData(array $data) {
        $this->data = $data;
        return $this;
    }



}