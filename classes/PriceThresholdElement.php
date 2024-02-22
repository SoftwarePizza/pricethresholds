<?php

class PriceThresholdElement extends ObjectModelCore {
    /** @var int PriceThresholdElement identifier */
    public $id_pricethreshold_element;

    /** @var string|array*/
    public $name;

    public $dummy;

    public static $definition = array(
        'table' => 'pricethreshold_element',
        'primary' => 'id_pricethreshold_element',
        'multilang' => true,
        'fields' => array(
            'dummy' => array('validate' => 'isCatalogName', 'required' => false, 'size' => 100),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100),
        )
    );

    public static function getAllByLang($id_lang) {
        $query =  'SELECT * FROM ' . _DB_PREFIX_ . 'pricethreshold_element p LEFT JOIN  ' . _DB_PREFIX_ . 'pricethreshold_element_lang pl 
        ON (p.id_pricethreshold_element = pl.id_pricethreshold_element) WHERE pl.id_lang = ' . $id_lang;

        return \Db::getInstance()->executeS($query);
    }
}