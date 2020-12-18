<?php


interface FoodItemInterface
{
    public function getBarcode();
    public function getManufacturer();
    public function getName();
    public function getTimestamp();
    public function getCategory();
    public function getBadge();
    public function getPacksize();
    public function getPackunits();
    public function get100units();
    public function getPackcount();
    public function getLightsdesc();
    public function getFat100();
    public function getFatPack();
    public function getFatServing();
    public function getFatLevel();
    public function getSaturates100();
    public function getSaturatesPack();
    public function getSaturatesServing();
    public function getSaturatesLevel();
    public function getSugar100();
    public function getSugarPack();
    public function getSugarServing();
    public function getSugarLevel();
    public function getSalt100();
    public function getSaltPack();
    public function getSaltServing();
    public function getSaltLevel();
    public function getCals100();
    public function getCalsPack();
    public function getCalsServing();
    public function getKj100();
    public function getKjPack();
    public function getKjServing();
    public function getFibre_100();
    public function getFibre_pack();
    public function getFibre_serving();
    public function getIngredients();
    public function getSource();
}
