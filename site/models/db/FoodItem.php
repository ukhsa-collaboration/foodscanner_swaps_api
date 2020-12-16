<?php


class FoodItem extends FoodBbItem implements JsonSerializable
{
    protected $m_source;


    protected function getAccessorFunctions(): array
    {
        $accessorFunctions = parent::getAccessorFunctions();
        $accessorFunctions["source"] = function() { return $this->m_source; };
        return $accessorFunctions;
    }


    protected function getSetFunctions(): array
    {
        $setFunctions = parent::getSetFunctions();
        $setFunctions["source"] = function($x) { $this->m_source = $x; };
        return $setFunctions;
    }


    public function getTableHandler(): \Programster\MysqlObjects\TableInterface
    {
        return FoodTable::getInstance();
    }


    public function toArray()
    {
        $arrayForm = parent::toArray();
        $arrayForm['source'] = $this->m_source;
        return $arrayForm;
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


    # Accessors
    public function getSource() { return $this->m_source; }
}
