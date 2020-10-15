<?php


class TrafficLightObject extends Programster\MysqlObjects\AbstractNoIdTableRowObject
{
    protected $m_sugar;
    protected $m_saturates;
    protected $m_salt;
    protected $m_message;


    public function __construct(array $row)
    {
        $this->initializeFromArray($row);
    }


    protected function getAccessorFunctions(): array
    {
        return array(
            'sugar' => function() { return $this->m_sugar; },
            'saturates' => function() { return $this->m_saturates; },
            'salt' => function() { return $this->m_salt; },
            'message' => function() { return $this->m_message; },
        );
    }


    protected function getSetFunctions(): array
    {
        return array(
            'sugar' => function($x) { $this->m_sugar = $x; },
            'saturates' => function($x) { $this->m_saturates = $x; },
            'salt' => function($x) { $this->m_salt = $x; },
            'message' => function($x) { $this->m_message = $x; },
        );
    }


    public function getTableHandler() : Programster\MysqlObjects\AbstractNoIdTable
    {
        return new TrafficLightTable();
    }


    # Accessors
    public function getSugar() : string { return $this->m_sugr; }
    public function getSaturates() : string { return $this->m_saturates; }
    public function getSalt() : string { return $this->m_salt; }
    public function getMessage() : string { return $this->m_message; }
}