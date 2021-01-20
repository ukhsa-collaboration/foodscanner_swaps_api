<?php

/*
 * A decorator of the FoodItemResponseObject which adds information about the machine learning PHE_cat value that it has.
 */


class FoodItemDebugResponseObject implements JsonSerializable
{
    private FoodItemInterface $m_foodItem;
    private string $m_algorithmName;
    private ?string $m_pheCat;
    private string $m_pheCatContext;


    /**
     * Create a swap response object
     * @param ProductResponseObject $foodItem - the product response object this swap is based on switching to.
     * This object is basically the same response, just with a few properties added.
     * @param int $rank - the rank or "similarity order" that this swap is in. E.g. top 3 is rank 1,2, and 3.
     */
    public function __construct(FoodItemInterface $foodItem, string $machineLearningAlgorithm)
    {
        $algorithms = ['spacy', 'sklearn'];
        $machineLearningAlgorithm = strtolower($machineLearningAlgorithm);

        if (!in_array($machineLearningAlgorithm, $algorithms))
        {
            throw new Exception("Invalid machine learning algorithm specified. Must be one of [" . implode(", ", $algorithms) . "]");
        }

        $this->m_foodItem = $foodItem;
        $this->m_algorithmName = $machineLearningAlgorithm;


        /* @var $etlTable FoodConsolidatedTable */
        $etlTable = FoodConsolidatedTable::getInstance();

        /* @var $mlTable FoodMachineLearningCategorisationTable */
        $mlTable = FoodMachineLearningCategorisationTable::getInstance();

        try
        {
            $etlRow = $etlTable->findByBarcode($this->m_foodItem->getBarcode());
            $pheCat = $etlRow->getPHECat();
            $mlRow = $mlTable->findByBarcode($this->m_foodItem->getBarcode());

            switch ($this->m_algorithmName)
            {
                case 'spacy': $pheCat = $mlRow->getPheCategory1(); break;
                case 'sklearn': $pheCat = $mlRow->getPheCategory2(); break;
                default: throw new Exception("Unrecognized ML algorithm: {$this->m_algorithmName}");
            }

            $pheCatContext = "Value is from machine learning algorithm";
        }
        catch (ExceptionProductNotFound $ex)
        {
            $pheCat = null;
            $pheCatContext = "Product not in ETL table";
        }
        catch (ExceptionMachineLearningCategorisationRowNotFound $mlRowNotFoundException)
        {
            $pheCatContext = "Value is from ETL table as no value in machine-learning results";
        }

        $this->m_pheCat = $pheCat; // ['PHE_Cat'] = $pheCat;
        $this->m_pheCatContext = $pheCatContext;
    }


    public function toArray() : array
    {
        $arrayForm = $this->m_foodItem->toArray();
        $arrayForm['PHE_Cat'] = $this->m_pheCat;
        $arrayForm['PHE_Cat_context'] = $this->m_pheCatContext;
        return $arrayForm;
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


    # Accessors
    public function getPheCat() : ?string { return $this->m_pheCat; }
    public function getPheCatContext() : string { return $this->m_pheCatContext; }
}
