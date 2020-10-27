<?php

/*
 * A class to represent a single swap object in the collection of swaps in our response to /swaps/{barcode}
 * This object is necessary because we need to return the information about the food product, not just the
 * barcodes which the swap object has.
 */


class SwapResponseObject implements JsonSerializable
{
    private ProductResponseObject $m_productResponseObject;
    private int $m_rank;
    private int $m_swapCount;


    /**
     * Create a swap response object
     * @param ProductResponseObject $productResponseObj - the product response object this swap is based on switching to.
     * This object is basically the same response, just with a few properties added.
     * @param int $rank - the rank or "similarity order" that this swap is in. E.g. top 3 is rank 1,2, and 3.
     */
    private function __construct(ProductResponseObject $productResponseObj, int $rank)
    {
        $this->m_productResponseObject = $productResponseObj;
        $this->m_rank = $rank;

        // calculate how many people have swapped to this product (from any other product).
        /* @var $swapRecordTable SwapRecordTable */
        $swapRecordTable = SwapRecordTable::getInstance();
        $barcodeTo = $productResponseObj->getFoodItem()->getBarcode();
        $swapRecords = $swapRecordTable->loadFromBarcodeTo($barcodeTo);
        $this->m_swapCount = count($swapRecords);
    }


    /**
     * Creates a collection of swap response objects for the provided database swap objects.
     * @param Swap $swaps - the swap objects we wish to fetch response objects for.
     * @return array - a collection of SwapResponseObject objects for the provided swaps. There may not be the
     * same number of items, if some swaps have barcodes that could not be found in the food table.
     */
    public static function createForSwaps(Swap ...$swaps) : array
    {
        $swapResponseObjects = array();
        $foodTable = FoodTable::getInstance();
        $foodConsolidatedTable = FoodConsolidatedTable::getInstance();
        $foodItems = $foodTable->fetchForSwaps(...$swaps);
        $foodConsolidatedItems = $foodConsolidatedTable->fetchForSwaps(...$swaps);

        foreach ($swaps as $swap)
        {
            if (isset($foodItems[$swap->getSwapBarcode()]))
            {
                $foodItem = $foodItems[$swap->getSwapBarcode()];

                if (isset($foodConsolidatedItems[$swap->getSwapBarcode()]))
                {
                    $foodConsolidatedItem = $foodConsolidatedItems[$swap->getSwapBarcode()];
                }
                else
                {
                    $foodConsolidatedItem = null;
                }

                $productResponseObject = new ProductResponseObject($foodItem, $foodConsolidatedItem);
                $swapResponseObjects[] = new SwapResponseObject($productResponseObject, $swap->getRank());
            }
        }

        return $swapResponseObjects;
    }


    public function toArray() : array
    {
        $arrayForm = $this->m_productResponseObject->toArray();
        $arrayForm['rank'] = $this->m_rank;
        $arrayForm['swap_count'] = $this->m_swapCount;
        return $arrayForm;
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


    # Accessors
    public function getRank() : int { return $this->m_rank; }
}
