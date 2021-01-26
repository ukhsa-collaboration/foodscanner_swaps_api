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


    /**
     * Fetches the swaps for a specified barcode.
     * @param string $barcode
     * @param bool $reversed - if set to true, fetches the products acts as a swap for, rather than
     * fetching swaps for this product. (product being the item having the specified barcode
     * @return array
     */
    public function loadForBarcode(string $barcode, bool $reversed=false) : array
    {
        if ($reversed === false)
        {
            $models = $this->loadWhereAnd(['barcode' => $barcode]);
        }
        else
        {
            $models = $this->loadWhereAnd(['swap_barcode' => $barcode]);
        }

        return $models;
    }
}

