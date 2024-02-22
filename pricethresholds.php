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

if (!defined('_PS_VERSION_')) {
    exit;
}


require_once('classes/PriceThreshold.php');
require_once('classes/PriceThresholdElement.php');
require_once('classes/PriceThresholdEffect.php');

class Pricethresholds extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'pricethresholds';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Michał Drożdżyński';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Price thresholds');
        $this->description = $this->l('Price thresholds');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayProductAdditionalInfo');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->postProcess();

        if (Tools::isSubmit('addThreshold') || Tools::isSubmit('updatepricethreshold')) {
            return $this->renderForm();
        }

        if (Tools::isSubmit('addAwards') || Tools::isSubmit('updatepricethreshold_element')) {
            return $this->addAwardsForm();
        }

        if (Tools::isSubmit('addPriceThresholdAwards') || Tools::isSubmit('updatepricethreshold_effect')) {
            return $this->addPriceThresholdAwardsForm();
        }

        return $this->renderPriceThresholdList() . $this->renderPriceThresholdElementList() . $this->renderPriceThresholdAwardsList();
    }

    protected function addPriceThresholdAwardsForm() {
        $priceThresholdEffect = new PriceThresholdEffect(Tools::getValue('id_pricethreshold_effect'));
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPriceThresholdAwards';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => [
                'effect' => $priceThresholdEffect->effect,
                'id_pricethreshold_element' => $priceThresholdEffect->id_pricethreshold_element,
                'id_pricethreshold' => $priceThresholdEffect->id_pricethreshold,
                'id_pricethreshold_effect' => $priceThresholdEffect->id_pricethreshold_effect,
            ], /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $awards = PriceThresholdElement::getAllByLang($this->context->language->id);
        $priceThreshold = PriceThreshold::getAll();

        $fields_form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'select',
                        'name' => 'id_pricethreshold',
                        'label' => $this->l('Prica'),
                        'options' => array(
                            'query' => $priceThreshold,                           // $options contains the data itself.
                            'id' => 'id_pricethreshold',                           // The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
                            'name' => 'price'                               // The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
                        )
                    ),
                    array(
                        'col' => 3,
                        'type' => 'select',
                        'name' => 'id_pricethreshold_element',
                        'label' => $this->l('Awards'),
                        'options' => array(
                            'query' => $awards,                           // $options contains the data itself.
                            'id' => 'id_pricethreshold_element',                           // The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
                            'name' => 'name'                               // The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
                        )
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'effect',
                        'lang' => true,
                        'label' => $this->l('Effect'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'hidden',
                        'name' => 'id_pricethreshold_effect',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function renderPriceThresholdAwardsList() {
        $result = PriceThresholdEffect::getAllByLang($this->context->language->id);

        $fields_list = array(
            'id_pricethreshold_effect' => array(
              'title' => "ID",
              'align' => 'center',
              'class' => 'fixed-width-xs',
              'search' => false,
            ),
            'id_pricethreshold_element' => array(
                'title' => "Award",
                'align' => 'center',
                'callback' => 'displayAwards',
                'callback_object' => $this,
                'search' => false,
            ),
            'id_pricethreshold' => array(
                'title' => "Price",
                'align' => 'center',
                'search' => false,
                'callback' => 'displayPrice2',
                'callback_object' => $this,
            ),
            'effect' => array(
              'title' => $this->l('Effect'),
              'orderby' => false,
              'search' => false,
            ),
        );
  
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_pricethreshold_effect';
        $helper->table = 'pricethreshold_effect';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = false;
        $helper->_default_pagination = 10;
        $helper->listTotal = count($result);
        $helper->toolbar_btn['new'] = [
            'href' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name, 'module_name' => $this->name, 'addPriceThresholdAwards' => '']),
            'desc' => $this->trans('Add New Awards', [], 'Modules.Productcomments.Admin'),
        ];
        $helper->module = $this;
        $helper->title = $this->l('Price Threshold Awards');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;


        return $helper->generateList($result, $fields_list);
    }

    public function displayAwards($id_pricethreshold_element) {
        $award = new  PriceThresholdElement($id_pricethreshold_element);

        return $award->name[$this->context->language->id];
    }

    protected function addAwardsForm() {
        $priceThresholdElement = new PriceThresholdElement(Tools::getValue('id_pricethreshold_element'));
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAwards';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => [
                'name' => $priceThresholdElement->name,
                'id_pricethreshold_element' => $priceThresholdElement->id_pricethreshold_element,
            ], /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'name',
                        'lang' => true,
                        'label' => $this->l('Name'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'hidden',
                        'name' => 'id_pricethreshold_element',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        return $helper->generateForm(array($fields_form));
    }
 
    public function renderPriceThresholdElementList() {
        $result = PriceThresholdElement::getAllByLang($this->context->language->id);

        $fields_list = array(
            'id_pricethreshold_element'=> array(
              'title' => "ID",
              'align' => 'center',
              'class' => 'fixed-width-xs',
              'search' => false,
            ),
            'name' => array(
              'title' => $this->l('Name'),
              'orderby' => false,
              'class' => 'fixed-width-xxl',
              'search' => false,
            ),
        );
  
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_pricethreshold_element';
        $helper->table = 'pricethreshold_element';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = false;
        $helper->_default_pagination = 10;
        $helper->listTotal = count($result);
        $helper->toolbar_btn['new'] = [
            'href' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name, 'module_name' => $this->name, 'addAwards' => '']),
            'desc' => $this->trans('Add New Awards', [], 'Modules.Productcomments.Admin'),
        ];
        $helper->module = $this;
        $helper->title = $this->l('Awards');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;


        return $helper->generateList($result, $fields_list);
    }

    public function renderPriceThresholdList() {
        $result = PriceThreshold::getAll();

        $fields_list = array(
            'id_pricethreshold'=> array(
              'title' => "ID",
              'align' => 'center',
              'class' => 'fixed-width-xs',
              'search' => false,
            ),
            'price' => array(
              'title' => $this->l('Price'),
              'orderby' => false,
              'callback' => 'displayPrice',
              'callback_object' => $this,
              'class' => 'fixed-width-xxl',
              'search' => false,
            ),
        );
  
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_pricethreshold';
        $helper->table = 'pricethreshold';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = false;
        $helper->_default_pagination = 10;
        $helper->listTotal = count($result);
        $helper->toolbar_btn['new'] = [
            'href' => $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name, 'module_name' => $this->name, 'addThreshold' => '']),
            'desc' => $this->trans('Add New Threshold', [], 'Modules.Productcomments.Admin'),
        ];
        $helper->module = $this;
        $helper->title = $this->l('Price threshold');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;


        return $helper->generateList($result, $fields_list);
    }

    public function displayPrice($price) {
        return Tools::displayPrice($price);
    }

    public function displayPrice2($id_pricethreshold) {
        $priceThreshold = new PriceThreshold($id_pricethreshold);
        return Tools::displayPrice($priceThreshold->price);
    }
    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $priceThreshold = new PriceThreshold(Tools::getValue('id_pricethreshold'));
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPricethresholdsModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => [
                'price' => $priceThreshold->price,
                'id_pricethreshold' => $priceThreshold->id_pricethreshold,
            ], /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'price',
                        'label' => $this->l('Price'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'hidden',
                        'name' => 'id_pricethreshold',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if (Tools::isSubmit('submitPricethresholdsModule')) {
            $priceThreshold = new PriceThreshold(Tools::getValue('id_pricethreshold'));
            $priceThreshold->price = Tools::getValue('price');
            $priceThreshold->save();
        }

        if (Tools::isSubmit('deletepricethreshold')) {
            $priceThreshold = new PriceThreshold(Tools::getValue('id_pricethreshold'));
            $priceThreshold->delete();
        }

        if (Tools::isSubmit('submitAwards')) {
            $langs = Language::getLanguages();
            $name = [];
            
            foreach ($langs as $lang) {
                $name[$lang['id_lang']] = Tools::getValue('name_' . $lang['id_lang']);
            }

            $priceThresholdElement = new PriceThresholdElement(Tools::getValue('id_pricethreshold_element'));
            $priceThresholdElement->name = $name;

            $priceThresholdElement->save();
        }

        if (Tools::isSubmit('submitPriceThresholdAwards')) {
            $langs = Language::getLanguages();
            $priceThresholdEffect = new PriceThresholdEffect(Tools::getValue('id_pricethreshold_effect'));
            
            $effect = [];
            
            foreach ($langs as $lang) {
                $effect[$lang['id_lang']] = Tools::getValue('effect_' . $lang['id_lang']);
            }

            $priceThresholdEffect->id_pricethreshold = Tools::getValue('id_pricethreshold');
            $priceThresholdEffect->id_pricethreshold_element = Tools::getValue('id_pricethreshold_element');
            $priceThresholdEffect->effect = $effect;
            $priceThresholdEffect->save();
        }

        if (Tools::isSubmit('deletepricethreshold_element')) {
            $priceThresholdElement = new PriceThresholdElement(Tools::getValue('id_pricethreshold_element'));
            $priceThresholdElement->delete();
        }

        if (Tools::isSubmit('deletepricethreshold_effect')) {
            $priceThresholdEffect = new PriceThresholdEffect(Tools::getValue('id_pricethreshold_effect'));
            $priceThresholdEffect->delete();
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookdisplayProductAdditionalInfo()
    {
        $priceThresholds = PriceThreshold::getAll();
        $awards = PriceThresholdElement::getAllByLang($this->context->language->id);
        $priceThresholdAwards = PriceThresholdEffect::getAllByLangWithForeign($this->context->language->id);
        
        foreach ($priceThresholdAwards as $key => $priceThresholdAward) {
            $amount = Tools::convertPriceFull($priceThresholdAward['price'], new Currency(1), $this->context->currency);

            $priceThresholdAwards[$key]['price'] = $amount;
        }

        // Tablica do grupowania
        $groupedArray = [];

        foreach ($priceThresholdAwards as $element) {
            $key = $element['id_pricethreshold_element'];
            
            // Dodaj element do odpowiedniej grupy w oparciu o wartość id_pricethreshold_element
            $groupedArray[$key][] = $element;
        }

        $cart = $this->context->cart;
  
        $total = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);

        $data = [[]];
        $cartPriceLabel = $this->l('Value of your order');

        foreach ($priceThresholds as $key => $priceThreshold) {
            $amount = Tools::convertPriceFull($priceThreshold['price'], new Currency(1), $this->context->currency);

            $priceThresholds[$key]['price'] = $amount;
        }
        
        $this->smarty->assign([
            'priceThresholds' => $priceThresholds,
            'cartPriceLabel' => $cartPriceLabel,
            'grouped_awards' => $groupedArray,
            'cart_price' => $total,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/pricethresholds.tpl');
    }
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
}
