<?php

/*
 * An object to represent the swaps table.
 */

class SwapTable extends Programster\MysqlObjects\AbstractTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::getSwapsDatabase();
    }


    public function getFieldsThatAllowNull(): array
    {
        return array();
    }


    public function getFieldsThatHaveDefaults()
    {
        return array();
    }


    public function getObjectClassName()
    {
        return Swap::class;
    }


    public function getTableName() { return "swaps"; }


    public function validateInputs(array $data): array
    {
        return $data;
    }


    public function loadForBarcode(string $barcode) : array
    {
        return $this->loadWhereAnd(['barcode' => $barcode]);
    }
}

