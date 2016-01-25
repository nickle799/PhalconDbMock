<?php
namespace NickLewis\PhalconDbMock\Services\Parsers\Select;
class Row {
    /** @var  Cell[] */
    private $cells;

    /**
     * Getter
     * @return Cell[]
     */
    public function getCells() {
        return $this->cells;
    }

    /**
     * Setter
     * @param Cell[] $cells
     * @return Row
     */
    public function setCells(array $cells) {
        $this->cells = $cells;
        return $this;
    }


}