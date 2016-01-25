<?php
namespace NickLewis\PhalconDbMock\Services\Parsers;
use NickLewis\PhalconDbMock\Models\DbException;
use NickLewis\PhalconDbMock\Models\PDOStatement;
use NickLewis\PhalconDbMock\Services\Parsers\Select\Cell;
use NickLewis\PhalconDbMock\Services\Parsers\Select\Row;

class Select extends Base {
    /** @var  Row[] */
    private $rows;

    /**
     * parse
     * @param array $parsed
     * @return PDOStatement
     */
    public function process(array $parsed) {
        parent::process($parsed);
        $this->loadModels();
        $this->filterWhere();
        $this->filterCells();
        return new PDOStatement($this->buildResults());
    }

    /**
     * buildResults
     * @return array
     */
    private function buildResults() {
        $results = [];
        foreach($this->getRows() as $row) {
            $result = [];
            foreach($row->getCells() as $cell) {
                $result[$cell->getCellName()] = $cell->getValue();
            }
            $results[] = $result;
        }
        return $results;
    }

    /**
     * loadModels
     * @return void
     */
    private function loadModels() {
        $parsed = $this->getParsed();
        $tableName = $parsed['FROM'][0]['no_quotes']['parts'][0]; //TODO - alias
        $table = $this->getDatabase()->getTable($tableName);
        $alias = $table->getName();
        $rows = [];
        foreach($table->getRows() as $tableRow) {
            $row = new Row();
            $cells = [];
            foreach($tableRow->toArray() as $name=>$value) {
                $cell = new Cell();
                $cell->setAlias($alias);
                $cell->setCellName($name);
                $cell->setTableName($table->getName());
                $cell->setValue($value);
                $cells[] = $cell;
            }
            $row->setCells($cells);
            $rows[] = $row;
        }
        $this->setRows($rows);
    }

    /**
     * filterWhere
     * @return void
     */
    private function filterWhere() {
        $rows = $this->getRows();
        $rows = array_filter($rows, function(Row $row) {
            $evalString = 'return ';
            /** @var Cell|null $currentField */
            $currentField = null;
            $currentOperator = null;
            //TODO - if no where, always return true
            foreach($this->getParsed()['WHERE'] as $where) {
                switch($where['expr_type']) {
                    case 'colref':
                        $currentField = $this->findCell($row, $where['no_quotes']['parts']);
                        break;
                    case 'operator':
                        $currentOperator = $where['base_expr'];
                        break;
                    case 'const':
                        $value = $where['base_expr'];
                        $evalString .= $this->evaluateWhere($currentField, $currentOperator, $value)?'true':'false';
                        break;
                }
            }
            $evalString .= ';';
            return eval($evalString);
        });
        $this->setRows($rows);
    }

    /**
     * filterCells
     * @return void
     * @throws DbException
     */
    private function filterCells() {
        foreach($this->getRows() as $row) {
            $cells = [];
            foreach($this->getParsed()['SELECT'] as $sqlColumn) { //TODO - cell alias
                $cells[] = $this->findCell($row, $sqlColumn['no_quotes']['parts']);
            }
            $row->setCells($cells);
        }
    }

    /**
     * evaluateWhere
     * @param Cell $cell
     * @param      $operator
     * @param      $value
     * @return bool
     * @throws \Exception
     */
    private function evaluateWhere(Cell $cell, $operator, $value) {
        switch($operator) {
            case '=':
                return $cell->getValue()==$value;
                break;
            default:
                throw new \Exception('Operator not handled yet: '.$operator);
                break;
        }
    }

    /**
     * findCell
     * @param Row      $row
     * @param string[] $parts
     * @return Cell
     * @throws DbException
     */
    private function findCell(Row $row, array $parts) {
        list($tableName, $cellName) = $parts;
        foreach($row->getCells() as $cell) {
            if($cell->getCellName()==$cellName && in_array($tableName, [$cell->getAlias(), $cell->getTableName()])) {
                return $cell;
            }
        }
        throw new DbException('Select: Could not find cell: '.implode('.', $parts));
    }

    /**
     * Getter
     * @return Select\Row[]
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * Setter
     * @param Select\Row[] $rows
     * @return Select
     */
    public function setRows(array $rows) {
        $this->rows = $rows;
        return $this;
    }




}