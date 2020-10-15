<?php

/*
 * An object to represent the food_consolidated table in the ETL database.
 */

class FoodConsolidatedTable extends Programster\MysqlObjects\AbstractTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::getEtlDatabase();
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
        return FoodConsolidatedItem::class;
    }


    public function getTableName() { return $_ENV['ETL_DB_TABLE']; }


    public function validateInputs(array $data): array
    {
        return $data;
    }


    /**
     * Fetch a single product by its barcode
     * @param string $barcode
     * @return \FoodConsolidatedItem
     * @throws ExceptionProductNotFound - if the product with the provided barcode could not be found.
     */
    public function findByBarcode(string $barcode) : FoodConsolidatedItem
    {
        $products = $this->loadWhereAnd(['barcode' => $barcode]);

        if (count($products) !== 1)
        {
            throw new ExceptionProductNotFound();
        }

        return $products[0];
    }
}

