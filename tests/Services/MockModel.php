<?php
namespace Tests\Services;
use Phalcon\Mvc\Model;

class MockModel extends Model {
    /** @var  int */
    private $id;
    /** @var  string */
    private $columnA;
    /** @var  string */
    private $columnB;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     * @return MockModel
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getColumnA() {
        return $this->columnA;
    }

    /**
     * @param string $columnA
     * @return MockModel
     */
    public function setColumnA($columnA) {
        $this->columnA = $columnA;
        return $this;
    }

    /**
     * @return string
     */
    public function getColumnB() {
        return $this->columnB;
    }

    /**
     * @param string $columnB
     * @return MockModel
     */
    public function setColumnB($columnB) {
        $this->columnB = $columnB;
        return $this;
    }

    public function getSource() {
        return 'MockModel';
    }

}