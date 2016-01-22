<?php
namespace NickLewis\PhalconDbMock\Models;
use Phalcon\Mvc\Model\Row as PhalconRow;
class Row extends PhalconRow {

    /**
     * Row constructor.
     * @param array $data
     */
    public function __construct(array $data=[]) {
        foreach($data as $name=>$value) {
            $this->writeAttribute($name, $value);
        }
    }
}