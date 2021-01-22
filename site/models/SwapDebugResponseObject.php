<?php

/*
 * A decorator of the SwapResponseObject which adds information about the machine learning PHE_cat value that it has.
 */


class SwapDebugResponseObject implements JsonSerializable
{
    private SwapResponseObject $m_swapResponseObject;
    private string $m_algorithmName;


    /**
     * Create a swap response object
     * @param ProductResponseObject $swapResponseObject - the product response object this swap is based on switching to.
     * This object is basically the same response, just with a few properties added.
     * @param int $rank - the rank or "similarity order" that this swap is in. E.g. top 3 is rank 1,2, and 3.
     */
    public function __construct(SwapResponseObject $swapResponseObject, string $machineLearningAlgorithm)
    {
        $this->m_algorithmName = $machineLearningAlgorithm;
        $this->m_swapResponseObject = $swapResponseObject;
    }


    public function toArray() : array
    {
        $foodDebugItem = new FoodItemDebugResponseObject(
            $this->m_swapResponseObject->getFoodItem(),
            $this->m_algorithmName
        );

        $arrayForm = $this->m_swapResponseObject->toArray();
        $arrayForm['PHE_Cat'] = $foodDebugItem->getPheCat();
        $arrayForm['PHE_Cat_context'] = $foodDebugItem->getPheCatContext();
        $arrayForm['pgc_badge'] = $foodDebugItem->getPgcBadgeFlag();
        return $arrayForm;
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
