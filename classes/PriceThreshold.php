<?php

class PriceThreshold extends ObjectModelCore {
    /** @var int PriceThreshold identifier */
    public $id_pricethreshold;

    /** @var float */
    public $price;

    public static $definition = array(
        'table' => 'pricethreshold',
        'primary' => 'id_pricethreshold',
        'fields' => array(
            'price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => false,),
        )
    );

    public static function getAll() {
        $query = "SELECT * FROM " . _DB_PREFIX_ . "pricethreshold WHERE 1 ORDER BY price";
        return Db::getInstance()->executeS($query);
    }
}