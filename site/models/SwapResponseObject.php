<?php

/*
 * A class to represent the swap in our response to /swaps/{barcode}
 * This object is necessary because we need to return the information about the food product, not just the
 * barcodes which the swap object has.
 */


class SwapResponseObject implements JsonSerializable
{
    private FoodItem $m_foodItem;
    private int $m_rank;


    private function __construct(FoodItem $foodItem, int $rank)
    {
        $this->m_foodItem = $foodItem;
        $this->m_rank = $rank;
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
        $foodItems = $foodTable->fetchForSwaps(...$swaps);

        foreach ($swaps as $swap)
        {
            if (isset($foodItems[$swap->getSwapBarcode()]))
            {
                $foodItem = $foodItems[$swap->getSwapBarcode()];
                $swapResponseObjects[] = new SwapResponseObject($foodItem, $swap->getRank());
            }
        }

        return $swapResponseObjects;
    }


    public function toArray() : array
    {
        $arrayForm = $this->m_foodItem->toArray();
        $arrayForm['rank'] = $this->m_rank;
        return $arrayForm;
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


    # Accessors
    public function getRank() : int { return $this->m_rank; }
    public function getFoodItem() : FoodItem { return $this->m_foodItem; }
}
