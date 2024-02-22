<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pricethreshold` (
    `id_pricethreshold` int(11) NOT NULL AUTO_INCREMENT,
    `price` float NOT NULL,
    PRIMARY KEY  (`id_pricethreshold`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pricethreshold_element` (
    `id_pricethreshold_element` int(11) NOT NULL AUTO_INCREMENT,
    `dummy` varchar(255) NULL,
    PRIMARY KEY (`id_pricethreshold_element`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pricethreshold_element_lang` (
    `id_pricethreshold_element` int(11) NOT NULL,
    `id_lang` int NOT NULL,
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id_pricethreshold_element`, `id_lang`),
    FOREIGN KEY (`id_lang`) REFERENCES ' . _DB_PREFIX_ . 'lang(`id_lang`) ON DELETE CASCADE,
    FOREIGN KEY (`id_pricethreshold_element`) REFERENCES ' . _DB_PREFIX_ . 'pricethreshold_element(`id_pricethreshold_element`) ON DELETE CASCADE
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pricethreshold_effect` (
    `id_pricethreshold_effect` int(11) NOT NULL AUTO_INCREMENT,
    `id_pricethreshold_element` int(11) NOT NULL,
    `id_pricethreshold` int(11) NOT NULL,
    PRIMARY KEY (`id_pricethreshold_effect`),
    FOREIGN KEY (`id_pricethreshold_element`) REFERENCES ' . _DB_PREFIX_ . 'pricethreshold_element(`id_pricethreshold_element`) ON DELETE CASCADE,
    FOREIGN KEY (`id_pricethreshold`) REFERENCES ' . _DB_PREFIX_ . 'pricethreshold(`id_pricethreshold`) ON DELETE CASCADE
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pricethreshold_effect_lang` (
    `id_pricethreshold_effect` int(11) NOT NULL AUTO_INCREMENT,
    `id_lang` int NOT NULL,
    `effect` varchar(255) NOT NULL,
    PRIMARY KEY (`id_pricethreshold_effect`, `id_lang`),
    FOREIGN KEY (`id_lang`) REFERENCES ' . _DB_PREFIX_ . 'lang(`id_lang`) ON DELETE CASCADE,
    FOREIGN KEY (`id_pricethreshold_effect`) REFERENCES ' . _DB_PREFIX_ . 'pricethreshold_effect(`id_pricethreshold_effect`) ON DELETE CASCADE
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
