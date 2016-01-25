<?php
namespace NickLewis\PhalconDbMock\Services\Parsers\Select;
class Cell {
    /** @var  string */
    private $tableName;
    /** @var  string */
    private $alias;
    /** @var  string */
    private $cellName;
    /** @var  string */
    private $value;

    /**
     * Getter
     * @return string
     */
    public function getCellName() {
        return $this->cellName;
    }

    /**
     * Setter
     * @param string $cellName
     * @return Cell
     */
    public function setCellName($cellName) {
        $this->cellName = $cellName;
        return $this;
    }

    /**
     * Getter
     * @return string
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * Setter
     * @param string $tableName
     * @return Cell
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Getter
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * Setter
     * @param string $alias
     * @return Cell
     */
    public function setAlias($alias) {
        $this->alias = $alias;
        return $this;
    }

    /**
     * Getter
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Setter
     * @param string $value
     * @return Cell
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }


}