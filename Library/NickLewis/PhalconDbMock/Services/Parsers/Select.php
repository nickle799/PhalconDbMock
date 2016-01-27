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
                $result[$cell->getCellAlias()] = $cell->getValue();
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
        /** @var Row[] $rows */
        $rows = [];
        foreach($parsed['FROM'] as $key=>$from) {
            $tableName = $from['no_quotes']['parts'][0];
            $alias = $from['alias']['name'];
            $table = $this->getDatabase()->getTable($tableName);
            $localRows = [];
            foreach($table->getRows() as $tableRow) {
                $row = new Row();
                $localRows[] = $row;
                foreach ($tableRow->toArray() as $name => $value) {
                    $cell = new Cell();
                    $cell->setAlias($alias);
                    $cell->setCellName($name);
                    $cell->setTableName($table->getName());
                    $cell->setValue($value);
                    $row->addCell($cell);
                }
            }
            if ($key == 0) {
                $rows = $localRows;
            } else {
                foreach($rows as $rowKey=>$row) {
                    $joinExpr = $from['ref_clause'][0]['sub_tree'];
                    $localRow = $this->findTableRow($row, $localRows, $joinExpr);
                    if($localRow===false) {
                        //Could not join on table
                        //TODO - OUTER JOINS
                        unset($rows[$rowKey]);
                        continue;
                    } else {
                        foreach($localRow->getCells() as $cell) {
                            $row->addCell($cell);
                        }
                    }
                }
            }
        }
        $this->setRows($rows);
    }

    /**
     * findTableRow
     * @param Row   $row
     * @param Row[] $localRows
     * @param array $joinExpr
     * @return bool|Row
     */
    private function findTableRow(Row $row, array $localRows, array $joinExpr) {
        foreach($localRows as $localRow) {
            $mergedRow = new Row();
            foreach($row->getCells() as $cell) {
                $mergedRow->addCell($cell);
            }
            foreach($localRow->getCells() as $cell) {
                $mergedRow->addCell($cell);
            }
            if($this->evalWhere($mergedRow, $joinExpr)) {
                return $localRow;
            }
        }
        return false;
    }

    /**
     * evalWhere
     * @param Row   $row
     * @param array $wheres
     * @return bool
     * @throws DbException
     * @throws \Exception
     */
    private function evalWhere(Row $row, array $wheres) {
        $evalString = 'return ';
        $originalWheres = $wheres;
        while(!empty($wheres)) {
            $colRef = array_shift($wheres);
            if($colRef['expr_type']!='colref') {
                throw new DbException('Unable to parse where (unexpected expr_type for colref): '.print_r($originalWheres, true));
            }
            $cell = $this->findCell($row, $colRef['no_quotes']['parts']);
            $operator = array_shift($wheres);
            if($operator['expr_type']!='operator') {
                throw new DbException('Unable to parse where (unexpected expr_type for operator): '.print_r($originalWheres, true));
            }
            switch($operator['base_expr']) {
                case '=':
                    $value = $this->parseValue($row, array_shift($wheres));
                    $isTrue = $cell->getValue()==$value;
                    break;
                case 'BETWEEN':
                    $firstValue = $this->parseValue($row, array_shift($wheres));
                    array_shift($wheres); //AND
                    $secondValue = $this->parseValue($row, array_shift($wheres));
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
                    throw new DbException('Unable to parse where (unexpected expr_type for operator): '.print_r($originalWheres, true));
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
    }

    /**
     * filterWhere
     * @return void
     */
    private function filterWhere() {
        if(!array_key_exists('WHERE', $this->getParsed())) {
            return; //No where to filter
        }
        $rows = $this->getRows();
        $rows = array_filter($rows, function(Row $row) {
            return $this->evalWhere($row, $this->getParsed()['WHERE']);
        });
        $this->setRows($rows);
    }

    /**
     * parseValue
     * @param Row   $row
     * @param array $where
     * @return string
     * @throws DbException
     */
    private function parseValue(Row $row, array $where) {
        $value = $where['base_expr'];
        $possibleQuotes = ['"', "'"];
        if(in_array($value[0], ['"', "'"])) {
            return trim($value, implode('', $possibleQuotes));
        } elseif(is_numeric($value)) {
            return $value;
        } else {
            return $this->findCell($row, $where['no_quotes']['parts'])->getValue();
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
                $newCells = $this->findCell($row, $sqlColumn['no_quotes']['parts'], $sqlColumn['alias']);
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
     * @param array|bool $alias
     * @return Cell|Cell[]
     * @throws DbException
     */
    private function findCell(Row $row, array $parts, $alias=false) {
        $cellName = array_pop($parts);
        $tableName = array_pop($parts);
        $returnVar = [];
        foreach($row->getCells() as $cell) {
            if(($cellName=='*' || $cell->getCellName()==$cellName) && (is_null($tableName) || in_array($tableName, [$cell->getAlias(), $cell->getTableName()]))) {
                if($alias!==false) {
                    $cell->setCellAlias($alias['no_quotes']['parts'][0]);
                }
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