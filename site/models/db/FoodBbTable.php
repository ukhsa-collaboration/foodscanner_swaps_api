<?php

/*
 * An object to represent the f_food_bb table.
 */


class FoodBbTable extends \Programster\MysqlObjects\AbstractTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::getFoodDatabase();
    }


    public function getFieldsThatAllowNull(): array
    {
        return array(
            'manufacturer',
            'packsize',
            '100units',
        );
    }


    public function getFieldsThatHaveDefaults()
    {
        return array();
    }


    public function getObjectClassName()
    {
        return FoodBbItem::class;
    }

    public function getTableName() { return 'f_food_bb'; }


    public function validateInputs(array $data): array
    {
        return $data;
    }


    /**
     * Fetches the food items that correspond the the swaps passed in.
     * @param Swap $swaps
     * @param type $indexByBarcode
     * @return array - map of food barcodes to the foodItem objects.
     */
    public function fetchForSwaps(Swap ...$swaps) : array
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


    /**
     * Fetch a single product by its barcode
     * @param string $barcode
     * @return \FoodItem
     * @throws ExceptionProductNotFound - if the product with the provided barcode could not be found.
     */
    public function findByBarcode(string $barcode) : FoodItemInterface
    {
        $products = $this->loadWhereAnd(['barcode' => $barcode]);

        // if barcode not found try trimming leading zeros (was in original API)
        if (count($products) !== 1 && Programster\CoreLibs\StringLib::startsWith($barcode, "0"))
        {
            $alteredBarcodeAttempt = ltrim($barcode, '0');
            $products = $this->loadWhereAnd(['barcode' => $alteredBarcodeAttempt]);
        }

        // if barcode not found try padding with leading zeros to 13
        if (count($products) !== 1)
        {
            $alteredBarcodeAttempt = str_pad($barcode, 13, '0', STR_PAD_LEFT);
            $products = $this->loadWhereAnd(['barcode' => $alteredBarcodeAttempt]);
        }

        if (count($products) !== 1)
        {
            throw new ExceptionProductNotFound();
        }

        return $products[0];
    }
}

