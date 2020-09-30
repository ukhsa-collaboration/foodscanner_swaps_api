<?php


class FoodItem extends Programster\MysqlObjects\AbstractTableRowObject implements JsonSerializable
{
    protected $m_barcode;
    protected $m_manufacturer;
    protected $m_name;
    protected $m_timestamp;
    protected $m_category;
    protected $m_badge;
    protected $m_packsize;
    protected $m_packunits;
    protected $m_100units;
    protected $m_packcount;
    protected $m_lightsdesc;
    protected $m_fat_100;
    protected $m_fat_pack;
    protected $m_fat_serving;
    protected $m_fat_level;
    protected $m_saturates_100;
    protected $m_saturates_pack;
    protected $m_saturates_serving;
    protected $m_saturates_level;
    protected $m_sugar_100;
    protected $m_sugar_pack;
    protected $m_sugar_serving;
    protected $m_sugar_level;
    protected $m_salt_100;
    protected $m_salt_pack;
    protected $m_salt_serving;
    protected $m_salt_level;
    protected $m_cals_100;
    protected $m_cals_pack;
    protected $m_cals_serving;
    protected $m_kj_100;
    protected $m_kj_pack;
    protected $m_kj_serving;
    protected $m_fibre_100;
    protected $m_fibre_pack;
    protected $m_fibre_serving;
    protected $m_ingredients;
    protected $m_source;


    public function __construct(array $data, $row_field_types = null)
    {
        $this->initializeFromArray($data, $row_field_types);
    }


    protected function getAccessorFunctions(): array
    {
        return array(
            "barcode" => function() { return $this->m_barcode; },
            "manufacturer" => function() { return $this->m_manufacturer; },
            "name" => function() { return $this->m_name; },
            "timestamp" => function() { return $this->m_timestamp; },
            "category" => function() { return $this->m_category; },
            "badge" => function() { return $this->m_badge; },
            "packsize" => function() { return $this->m_packsize; },
            "packunits" => function() { return $this->m_packunits; },
            "100units" => function() { return $this->m_100units; },
            "packcount" => function() { return $this->m_packcount; },
            "lightsdesc" => function() { return $this->m_lightsdesc; },
            "fat_100" => function() { return $this->m_fat_100; },
            "fat_pack" => function() { return $this->m_fat_pack; },
            "fat_serving" => function() { return $this->m_fat_serving; },
            "fat_level" => function() { return $this->m_fat_level; },
            "saturates_100" => function() { return $this->m_saturates_100; },
            "saturates_pack" => function() { return $this->m_saturates_pack; },
            "saturates_serving" => function() { return $this->m_saturates_serving; },
            "saturates_level" => function() { return $this->m_saturates_level; },
            "sugar_100" => function() { return $this->m_sugar_100; },
            "sugar_pack" => function() { return $this->m_sugar_pack; },
            "sugar_serving" => function() { return $this->m_sugar_serving; },
            "sugar_level" => function() { return $this->m_sugar_level; },
            "salt_100" => function() { return $this->m_salt_100; },
            "salt_pack" => function() { return $this->m_salt_pack; },
            "salt_serving" => function() { return $this->m_salt_serving; },
            "salt_level" => function() { return $this->m_salt_level; },
            "cals_100" => function() { return $this->m_cals_100; },
            "cals_pack" => function() { return $this->m_cals_pack; },
            "cals_serving" => function() { return $this->m_cals_serving; },
            "kj_100" => function() { return $this->m_kj_100; },
            "kj_pack" => function() { return $this->m_kj_pack; },
            "kj_serving" => function() { return $this->m_kj_serving; },
            "fibre_100" => function() { return $this->m_fibre_100; },
            "fibre_pack" => function() { return $this->m_fibre_pack; },
            "fibre_serving" => function() { return $this->m_fibre_serving; },
            "ingredients" => function() { return $this->m_ingredients; },
            "source" => function() { return $this->m_source; },
        );
    }


    protected function getSetFunctions(): array
    {
        return array(
            "barcode" => function($x) { $this->m_barcode = $x; },
            "manufacturer" => function($x) { $this->m_manufacturer = $x; },
            "name" => function($x) { $this->m_name = $x; },
            "timestamp" => function($x) { $this->m_timestamp = $x; },
            "category" => function($x) { $this->m_category = $x; },
            "badge" => function($x) { $this->m_badge = $x; },
            "packsize" => function($x) { $this->m_packsize = $x; },
            "packunits" => function($x) { $this->m_packunits = $x; },
            "100units" => function($x) { $this->m_100units = $x; },
            "packcount" => function($x) { $this->m_packcount = $x; },
            "lightsdesc" => function($x) { $this->m_lightsdesc = $x; },
            "fat_100" => function($x) { $this->m_fat_100 = $x; },
            "fat_pack" => function($x) { $this->m_fat_pack = $x; },
            "fat_serving" => function($x) { $this->m_fat_serving = $x; },
            "fat_level" => function($x) { $this->m_fat_level = $x; },
            "saturates_100" => function($x) { $this->m_saturates_100 = $x; },
            "saturates_pack" => function($x) { $this->m_saturates_pack = $x; },
            "saturates_serving" => function($x) { $this->m_saturates_serving = $x; },
            "saturates_level" => function($x) { $this->m_saturates_level = $x; },
            "sugar_100" => function($x) { $this->m_sugar_100 = $x; },
            "sugar_pack" => function($x) { $this->m_sugar_pack = $x; },
            "sugar_serving" => function($x) { $this->m_sugar_serving = $x; },
            "sugar_level" => function($x) { $this->m_sugar_level = $x; },
            "salt_100" => function($x) { $this->m_salt_100 = $x; },
            "salt_pack" => function($x) { $this->m_salt_pack = $x; },
            "salt_serving" => function($x) { $this->m_salt_serving = $x; },
            "salt_level" => function($x) { $this->m_salt_level = $x; },
            "cals_100" => function($x) { $this->m_cals_100 = $x; },
            "cals_pack" => function($x) { $this->m_cals_pack = $x; },
            "cals_serving" => function($x) { $this->m_cals_serving = $x; },
            "kj_100" => function($x) { $this->m_kj_100 = $x; },
            "kj_pack" => function($x) { $this->m_kj_pack = $x; },
            "kj_serving" => function($x) { $this->m_kj_serving = $x; },
            "fibre_100" => function($x) { $this->m_fibre_100 = $x; },
            "fibre_pack" => function($x) { $this->m_fibre_pack = $x; },
            "fibre_serving" => function($x) { $this->m_fibre_serving = $x; },
            "ingredients" => function($x) { $this->m_ingredients = $x; },
            "source" => function($x) { $this->m_source = $x; },
        );
    }


    public function getTableHandler(): \Programster\MysqlObjects\TableInterface
    {
        return new FoodTable();
    }



    public function toArray()
    {
        return array(
            "barcode" => $this->m_barcode,
            "manufacturer" => $this->m_manufacturer,
            "name" => $this->m_name,
            "timestamp" => $this->m_timestamp,
            "category" => $this->m_category,
            "badge" => $this->m_badge,
            "packsize" => $this->m_packsize,
            "packunits" => $this->m_packunits,
            "100units" => $this->m_100units,
            "packcount" => $this->m_packcount,
            "lightsdesc" => $this->m_lightsdesc,
            "fat_100" => $this->m_fat_100,
            "fat_pack" => $this->m_fat_pack,
            "fat_serving" => $this->m_fat_serving,
            "fat_level" => $this->m_fat_level,
            "saturates_100" => $this->m_saturates_100,
            "saturates_pack" => $this->m_saturates_pack,
            "saturates_serving" => $this->m_saturates_serving,
            "saturates_level" => $this->m_saturates_level,
            "sugar_100" => $this->m_sugar_100,
            "sugar_pack" => $this->m_sugar_pack,
            "sugar_serving" => $this->m_sugar_serving,
            "sugar_level" => $this->m_sugar_level,
            "salt_100" => $this->m_salt_100,
            "salt_pack" => $this->m_salt_pack,
            "salt_serving" => $this->m_salt_serving,
            "salt_level" => $this->m_salt_level,
            "cals_100" => $this->m_cals_100,
            "cals_pack" => $this->m_cals_pack,
            "cals_serving" => $this->m_cals_serving,
            "kj_100" => $this->m_kj_100,
            "kj_pack" => $this->m_kj_pack,
            "kj_serving" => $this->m_kj_serving,
            "fibre_100" => $this->m_fibre_100,
            "fibre_pack" => $this->m_fibre_pack,
            "fibre_serving" => $this->m_fibre_serving,
            "ingredients" => $this->m_ingredients,
            "source" => $this->m_source,
        );
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


    # Accessors
    public function getBarcode() { return $this->m_barcode; }
    public function getManufacturer() { return $this->m_manufacturer; }
    public function getName() { return $this->m_name; }
    public function getTimestamp() { return $this->m_timestamp; }
    public function getCategory() { return $this->m_category; }
    public function getBadge() { return $this->m_badge; }
    public function getPacksize() { return $this->m_packsize; }
    public function getPackunits() { return $this->m_packunits; }
    public function get100units() { return $this->m_100units; }
    public function getPackcount() { return $this->m_packcount; }
    public function getLightsdesc() { return $this->m_lightsdesc; }
    public function getFat100() { return $this->m_fat_100; }
    public function getFatPack() { return $this->m_fat_pack; }
    public function getFatServing() { return $this->m_fat_serving; }
    public function getFatLevel() { return $this->m_fat_level; }
    public function getSaturates100() { return $this->m_saturates_100; }
    public function getSaturatesPack() { return $this->m_saturates_pack; }
    public function getSaturatesServing() { return $this->m_saturates_serving; }
    public function getSaturatesLevel() { return $this->m_saturates_level; }
    public function getSugar100() { return $this->m_sugar_100; }
    public function getSugarPack() { return $this->m_sugar_pack; }
    public function getSugarServing() { return $this->m_sugar_serving; }
    public function getSugarLevel() { return $this->m_sugar_level; }
    public function getSalt100() { return $this->m_salt_100; }
    public function getSaltPack() { return $this->m_salt_pack; }
    public function getSaltServing() { return $this->m_salt_serving; }
    public function getSaltLevel() { return $this->m_salt_level; }
    public function getCals100() { return $this->m_cals_100; }
    public function getCalsPack() { return $this->m_cals_pack; }
    public function getCalsServing() { return $this->m_cals_serving; }
    public function getKj100() { return $this->m_kj_100; }
    public function getKjPack() { return $this->m_kj_pack; }
    public function getKjServing() { return $this->m_kj_serving; }
    public function getFibre_100() { return $this->m_fibre_100; }
    public function getFibre_pack() { return $this->m_fibre_pack; }
    public function getFibre_serving() { return $this->m_fibre_serving; }
    public function getIngredients() { return $this->m_ingredients; }
    public function getSource() { return $this->m_source; }
}