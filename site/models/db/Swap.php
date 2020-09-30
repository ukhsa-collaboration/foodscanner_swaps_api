<?php


class Swap extends Programster\MysqlObjects\AbstractTableRowObject
{
    protected $m_barcode;
    protected $m_swapBarcode;
    protected $m_rank;


    public function __construct(array $row)
    {
        $this->initializeFromArray($row);
    }


    protected function getAccessorFunctions(): array
    {
        return array(
            'barcode' => function () { return $this->m_barcode; },
            'rank' => function() { return $this->m_rank; },
            'swap_barcode' => function() { return $this->m_swapBarcode; }
        );
    }


    protected function getSetFunctions(): array
    {
        return array(
            'barcode' => function ($x) { $this->m_barcode = $x; },
            'rank' => function($x) { $this->m_rank = $x; },
            'swap_barcode' => function($x) { $this->m_swapBarcode = $x; }
        );
    }


    public function getTableHandler(): \Programster\MysqlObjects\TableInterface
    {
        return new SwapTable();
    }


    # Accessors
    public function getBarcode() : string { return $this->m_barcode; }
    public function getRank() : int { return $this->m_rank; }
    public function getSwapBarcode() : string { return $this->m_swapBarcode; }
}