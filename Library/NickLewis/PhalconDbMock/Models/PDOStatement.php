<?php
namespace NickLewis\PhalconDbMock\Models;
class PDOStatement extends \PDOStatement {
    /** @var  array */
    private $rows;

    /**
     * PDOStatement constructor.
     * @param array $rows
     */
    public function __construct(array $rows) {
        $this->setRows($rows);
    }


    /**
     * Getter
     * @return array
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * Setter
     * @param array $rows
     * @return PDOStatement
     */
    private function setRows(array $rows) {
        $this->rows = $rows;
        return $this;
    }


}