<?php

/*
 * An object to represent the swaps table.
 */

class TrafficLightTable extends \Programster\MysqlObjects\AbstractNoIdTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::getFoodDatabase();
    }


    public function getFieldsThatAllowNull(): array
    {
        return array();
    }


    public function getFieldsThatHaveDefaults() : array
    {
        return array();
    }


    public function getObjectClassName()
    {
        return TrafficLightObject::class;
    }


    public function getTableName() { return "f_trafficlights"; }


    public function validateInputs(array $data): array
    {
        return $data;
    }
}

