<?php
namespace NickLewis\PhalconDbMock\Services\Parsers;
use NickLewis\PhalconDbMock\Models\DbException;

class Insert extends Base {
    /** @var  string */
    private $tableName;
    /** @var  string[] */
    private $columns;
    /** @var  array */
    private $data;

    /**
     * parse
     * @param array $parsed
     * @return bool
     */
    public function process(array $parsed) {
        parent::process($parsed);
        foreach($parsed['INSERT'] as $subPart) {
            switch($subPart['expr_type']) {
                case 'table':
                    $this->setTableName($subPart['no_quotes']['parts'][0]);
                    break;
                case 'column-list':
                    $columns = [];
                    foreach($subPart['sub_tree'] as $column) {
                        $columns[] = $column['no_quotes']['parts'][0];
                    }
                    $this->setColumns($columns);
                    break;
            }
        }
        if(is_null($this->getColumns())) {
            //Wildcard
            $columns = [];
            foreach($this->getDatabase()->getTable($this->getTableName())->getColumns() as $column) {
                $columns[] = $column->getName();
            }
            $this->setColumns($columns);
        }
        $rows = [];
        foreach($parsed['VALUES'] as $value) {
            $row = [];
            foreach($value['data'] as $cell) {
                $value = $cell['base_expr'];
                if($value=='DEFAULT') {
                    $value = null;
                } elseif(!is_null($value)) {
                    if(in_array($value[0], ['"', "'"])) {
                        $value = substr($value, 1, -1); //Strip off quotes
                    }
                }
                $row[] = $value;
            }
            $rows[] = $row;
        }
        $this->setData($rows);
        $this->insert();
    }

    private function insert() {
        $table = $this->getDatabase()->getTable($this->getTableName());
        foreach($this->getData() as $row) {
            $table->addRowData(array_combine($this->getColumns(), $row));
        }
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