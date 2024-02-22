<?php

class PriceThresholdEffect extends ObjectModelCore {
    /** @var int PriceThresholdEffect identifier */
    public $id_pricethreshold_effect;

    public $id_pricethreshold_element;

    public $id_pricethreshold;

    /** @var string|array*/
    public $effect;

    public static $definition = array(
        'table' => 'pricethreshold_effect',
        'primary' => 'id_pricethreshold_effect',
        'multilang' => true,
        'fields' => array(
            'id_pricethreshold' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_pricethreshold_element' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'effect' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 100),
        )
    );

    public static function getAllByLang($id_lang) {
        $query =  'SELECT * FROM ' . _DB_PREFIX_ . 'pricethreshold_effect p LEFT JOIN  ' . _DB_PREFIX_ . 'pricethreshold_effect_lang pl 
        ON (p.id_pricethreshold_effect = pl.id_pricethreshold_effect) WHERE pl.id_lang = ' . $id_lang;

        return \Db::getInstance()->executeS($query);
    }

    public static function getAllByLangWithForeign($id_lang) {
        $query =  'SELECT * FROM ' . _DB_PREFIX_ . 'pricethreshold_effect e LEFT JOIN  ' . _DB_PREFIX_ . 'pricethreshold_effect_lang el 
        ON (e.id_pricethreshold_effect = el.id_pricethreshold_effect) LEFT JOIN ' . 
        _DB_PREFIX_ . 'pricethreshold p ON (e.id_pricethreshold = p.id_pricethreshold) 
        LEFT JOIN ' . _DB_PREFIX_ .'pricethreshold_element_lang pel ON (e.id_pricethreshold_element = pel.id_pricethreshold_element)
         WHERE el.id_lang = ' . $id_lang . ' AND pel.id_lang = ' . $id_lang . ' ORDER BY pel.id_pricethreshold_element, p.price';

        return \Db::getInstance()->executeS($query);
    }
}