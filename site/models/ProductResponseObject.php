<?php

/*
 * A class to represent a product in our response to /products/{barcode}
 * This has custom badge logic added to it.
 */

declare(strict_types = 1);


class ProductResponseObject implements JsonSerializable
{
    private FoodItem $m_foodItem;
    private ?FoodConsolidatedItem $m_foodConsolidatedItem;


    public function __construct(FoodItem $foodItem, ?FoodConsolidatedItem $foodConsolidatedItem)
    {
        $this->m_foodItem = $foodItem;
        $this->m_foodConsolidatedItem = $foodConsolidatedItem;
    }


    public function toArray() : array
    {
        # The original API returned everything as strings, and need to maintain this interface.
        $arrayForm = [
            "barcode" => "{$this->m_foodItem->getBarcode()}",
            "manufacturer" => "{$this->m_foodItem->getManufacturer()}",
            "name" => "{$this->m_foodItem->getName()}",
            "timestamp" => "{$this->m_foodItem->getTimestamp()}",
            "category" => "{$this->m_foodItem->getCategory()}",
            "badge" => "{$this->m_foodItem->getBadge()}",
            "packsize" => "{$this->m_foodItem->getPacksize()}",
            "packunits" => "{$this->m_foodItem->getPackunits()}",
            "100units" => "{$this->m_foodItem->get100units()}",
            "packcount" => "{$this->m_foodItem->getPackcount()}",
            "lightsdesc" => "{$this->m_foodItem->getLightsdesc()}",
            "fat_100" => "{$this->m_foodItem->getFat100()}",
            "fat_pack" => "{$this->m_foodItem->getFatPack()}",
            "fat_serving" => "{$this->m_foodItem->getFatServing()}",
            "fat_level" => "{$this->m_foodItem->getFatLevel()}",
            "saturates_100" => "{$this->m_foodItem->getSaturates100()}",
            "saturates_pack" => "{$this->m_foodItem->getSaturatesPack()}",
            "saturates_serving" => "{$this->m_foodItem->getSaturatesServing()}",
            "saturates_level" => "{$this->m_foodItem->getSaturatesLevel()}",
            "sugar_100" => "{$this->m_foodItem->getSugar100()}",
            "sugar_pack" => "{$this->m_foodItem->getSugarPack()}",
            "sugar_serving" => "{$this->m_foodItem->getSugarServing()}",
            "sugar_level" => "{$this->m_foodItem->getSugarLevel()}",
            "salt_100" => "{$this->m_foodItem->getSalt100()}",
            "salt_pack" => "{$this->m_foodItem->getSaltPack()}",
            "salt_serving" => "{$this->m_foodItem->getSaltServing()}",
            "salt_level" => "{$this->m_foodItem->getSaltLevel()}",
            "cals_100" => "{$this->m_foodItem->getCals100()}",
            "cals_pack" => "{$this->m_foodItem->getCalsPack()}",
            "cals_serving" => "{$this->m_foodItem->getCalsServing()}",
            "kj_100" => "{$this->m_foodItem->getKj100()}",
            "kj_pack" => "{$this->m_foodItem->getKjPack()}",
            "kj_serving" => "{$this->m_foodItem->getKjServing()}",
            "fibre_100" => "{$this->m_foodItem->getFibre_100()}",
            "fibre_pack" => "{$this->m_foodItem->getFibre_pack()}",
            "fibre_serving" => "{$this->m_foodItem->getFibre_serving()}",
            "ingredients" => "{$this->m_foodItem->getIngredients()}",
            "source" => "{$this->m_foodItem->getSource()}",
        ];

        // apply traffic light rules
        if
        (
               $this->m_foodItem->getSaturatesLevel() != ''
            || $this->m_foodItem->getSugarLevel() != ''
            || $this->m_foodItem->getSaltLevel() != ''
        )
        {
            $wherePairs = array(
                'saturates' => ($this->m_foodItem->getSaturatesLevel() ? $this->m_foodItem->getSaturatesLevel() : 'Low'),
                'sugar'     => ($this->m_foodItem->getSugarLevel() ? $this->m_foodItem->getSugarLevel() : 'Low'),
                'salt'      => ($this->m_foodItem->getSaltLevel() ? $this->m_foodItem->getSaltLevel() : 'Low'),
            );

            $trafficLights = TrafficLightTable::getInstance()->loadWhereAnd($wherePairs);

            if (count($trafficLights) === 1)
            {
                $trafficLightObject = $trafficLights[0];
                /* @var $trafficLightObject TrafficLightObject */
                $arrayForm['trafficlight'] = $trafficLightObject->getMessage();
            }
            else
            {
                // do nothing, original api didn't include in response if row not found.
            }
        }

        // apply custom badge logic rules.
        if ($this->m_foodConsolidatedItem !== null)
        {
            if ($this->m_foodConsolidatedItem->getCategory() === "02.01.01")
            {
                $arrayForm['badge'] = "1"; // milk
            }
            elseif (intval($arrayForm['badge']) === 1)
            {
                // if milk badget but not milk category, give good choice badge intead.
                $arrayForm['badge'] = "11"; // good choice
            }
            elseif (intval($arrayForm['badge']) === 11)
            {
                $arrayForm['badge'] = "11"; // keep good choice if already set.
            }
            elseif
            (
                   intval($this->m_foodConsolidatedItem->getBadgeNew()) === 0
                && $this->m_foodConsolidatedItem->getHighFiveMan() === 1
                && $this->m_foodConsolidatedItem->getSugarLevel() === "Low"
                && $this->m_foodConsolidatedItem->getSaltLevel() === "Low"
                && $this->m_foodConsolidatedItem->getFatLevel() === "Low"
            )
            {
                $arrayForm['badge'] = "12"; // high five man
            }
            elseif
            (
                   $this->m_foodConsolidatedItem->getBadgeNew() === 1
                || $this->m_foodConsolidatedItem->getBadgeNew() === "1"
            )
            {
                $arrayForm['badge'] = "11"; // good choice badge
            }
        }

        return $arrayForm;
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


    # Accessors
    public function getFoodItem() : FoodItem { return $this->m_foodItem; }
}
