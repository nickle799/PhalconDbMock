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
            //TODO - if no where, always return true
            $wheres = $this->getParsed()['WHERE'];
            while(!empty($wheres)) {
                $colRef = array_shift($wheres);
                if($colRef['expr_type']!='colref') {
                    throw new DbException('Unable to parse where (unexpected expr_type for colref): '.print_r($this->getParsed()['WHERE'], true));
                }
                $cell = $this->findCell($row, $colRef['no_quotes']['parts']);
                $operator = array_shift($wheres);
                if($operator['expr_type']!='operator') {
                    throw new DbException('Unable to parse where (unexpected expr_type for operator): '.print_r($this->getParsed()['WHERE'], true));
                }
                switch($operator['base_expr']) {
                    case '=':
                        $value = $this->parseValue(array_shift($wheres));
                        $isTrue = $cell->getValue()==$value;
                        break;
                    case 'BETWEEN':
                        $firstValue = $this->parseValue(array_shift($wheres));
                        array_shift($wheres); //AND
                        $secondValue = $this->parseValue(array_shift($wheres));
                        $isTrue = $firstValue<=$cell->getValue() && $secondValue>=$cell->getValue();
                        break;
                    default:
                        throw new \Exception('Unhandled Operator type: '.$operator['base_expr']);
                        break;
                }
                $evalString .= $isTrue?'true':'false';
                if(!empty($wheres)) {
                    $operator = array_shift($wheres);
                    if($operator['expr_type']!='operator') {
                        throw new DbException('Unable to parse where (unexpected expr_type for operator): '.print_r($this->getParsed()['WHERE'], true));
                    }
                    switch($operator['base_expr']) {
                        case 'AND':
                            $evalString .= ' && ';
                            break;
                        case 'OR':
                            $evalString .= ' || ';
                            break;
                        default:
                            throw new DbException('Unexpected comparison: '.$operator['base_expr']);
                            break;
                    }
                }

            }
            $evalString .= ';';
            return eval($evalString);
        });
        $this->setRows($rows);
    }

    /**
     * parseValue
     * @param array $where
     * @return string
     * @throws DbException
     */
    private function parseValue(array $where) {
        $value = $where['base_expr'];
        $possibleQuotes = ['"', "'"];
        if(in_array($value[0], ['"', "'"])) {
            return trim($value, implode('', $possibleQuotes));
        } else {
            throw new DbException('Unexpected Raw Value: '.$value);
        }
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
                $newCells = $this->findCell($row, $sqlColumn['no_quotes']['parts']);
                if(is_array($newCells)) {
                    $cells = array_merge($cells, $newCells);
                } else {
                    $cells[] = $newCells;
                }
            }
            $row->setCells($cells);
        }
    }

    /**
     * findCell
     * @param Row      $row
     * @param string[] $parts
     * @return Cell|Cell[]
     * @throws DbException
     */
    private function findCell(Row $row, array $parts) {
        $cellName = array_pop($parts);
        $tableName = array_pop($parts);
        $returnVar = [];
        foreach($row->getCells() as $cell) {
            if(($cellName=='*' || $cell->getCellName()==$cellName) && (is_null($tableName) || in_array($tableName, [$cell->getAlias(), $cell->getTableName()]))) {
                $returnVar[] = $cell;
            }
        }
        if(empty($returnVar)) {
            throw new DbException('Select: Could not find cell: ' . implode('.', $parts));
        } elseif(count($returnVar)==1) {
            return $returnVar[0];
        } else {
            return $returnVar;
        }
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