<?php

/*
 * An object to represent the swaps table.
 */

class FoodTable extends Programster\MysqlObjects\AbstractTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::getFoodDatabase();
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
        return FoodItem::class;
    }

    public function getTableName() { return $_ENV['FOOD_DB_TABLE']; }


    public function validateInputs(array $data): array
    {
        return $data;
    }


    /**
     * Fetches the food items that correspond the the swaps passed in.
     * @param Swap $swaps
     * @param type $indexByBarcode
     * @return type
     */
    public function fetchForSwaps(Swap ...$swaps)
    {
        foreach ($swaps as $swap)
        {
            $barcodes[] = $this->getDb()->escape_string($swap->getSwapBarcode());
        }

        $foodItems = $this->loadWhereAnd(array('barcode' => $barcodes));
        $result = array();

        foreach ($foodItems as $foodItem)
        {
            /* @var $foodItem FoodItem */
            $result[$foodItem->getBarcode()] = $foodItem;
        }

        return $result;
    }
}

