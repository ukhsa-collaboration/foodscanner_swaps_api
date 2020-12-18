<?php

/*
 * An object to represent the swaps table.
 */

class FoodTable extends FoodBbTable
{
    public function getObjectClassName()
    {
        return FoodItem::class;
    }


    public function getTableName() { return $_ENV['FOOD_DB_TABLE']; }
}

