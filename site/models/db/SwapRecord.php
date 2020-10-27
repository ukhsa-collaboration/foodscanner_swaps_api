<?php

/*
 * An object to represent a single record in the swap_records table
 */


class SwapRecord extends Programster\MysqlObjects\AbstractTableRowObject
{
    protected $m_fromBarcode;
    protected $m_toBarcode;
    protected $m_deviceIdentifier;
    protected $m_when;


    public function __construct(array $row)
    {
        $this->initializeFromArray($row);
    }


    protected function getAccessorFunctions(): array
    {
        return array(
            'to_barcode' => function() { return $this->m_toBarcode; },
            'from_barcode' => function() { return $this->m_fromBarcode; },
            'device_identifier' => function() { return $this->m_deviceIdentifier; },
            'when' => function() { return $this->m_when; },
        );
    }


    protected function getSetFunctions(): array
    {
        return array(
            'from_barcode' => function ($x) { $this->m_fromBarcode = $x; },
            'to_barcode' => function($x) { $this->m_toBarcode = $x; },
            'device_identifier' => function($x) { $this->m_deviceIdentifier = $x; },
            'when' => function($x) { $this->m_when = $x; },
        );
    }


    public function getTableHandler(): \Programster\MysqlObjects\TableInterface
    {
        return SwapRecordTable::getInstance();
    }


    # Accessors
    public function getToBarcode() : string { return $this->m_toBarcode; }
    public function getFromBarcode() : string { return $this->m_fromBarcode; }
    public function getDeviceIdentifier() : string { return $this->m_deviceIdentifier; }
    public function getWhen() : int { return $this->m_when; }
}