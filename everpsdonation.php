<?php
/**
 * 2019-2021 Team Ever
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
 *  @author    Team Ever <https://www.team-ever.com/>
 *  @copyright 2019-2021 Team Ever
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Everpsdonation extends Module
{
    private $html;
    private $postErrors = array();
    private $postSuccess = array();

    public function __construct()
    {
        $this->name = 'everpsdonation';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'Team Ever';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Ever PS Donation');
        $this->description = $this->l('Show amount customer will donate after processing order');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('EVERPSDONATION_AMOUNT', false);
        Configuration::updateValue('EVERPSDONATION_TYPE', 'percent');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayLeftColumn') &&
            $this->registerHook('displayRightColumn') &&
            $this->registerHook('displayReassurance');
    }

    public function uninstall()
    {
        Configuration::deleteByName('EVERPSDONATION_AMOUNT');
        Configuration::deleteByName('EVERPSDONATION_CATEGORIES');
        Configuration::deleteByName('EVERPSDONATION_TYPE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitEverpsdonationModule')) == true) {
            $this->postValidation();

            if (!count($this->postErrors)) {
                $this->postProcess();
            }
        }

        $this->context->smarty->assign('everpsdonation_dir', $this->_path);

        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/header.tpl');
        $this->html .= $this->renderForm();
        $this->html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/footer.tpl');

        return $this->html;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEverpsdonationModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
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
        $allowed_cats = json_decode(
            Configuration::get(
                'EVERPSDONATION_CATEGORIES'
            )
        );
        if (!is_array($allowed_cats)) {
            $allowed_cats = array($allowed_cats);
        }
        $tree = array(
            'selected_categories' => $allowed_cats,
            'use_search' => true,
            'use_checkbox' => true,
            'id' => 'id_category_tree',
        );
        $donation_type = array(
            array(
                'id_type' => 'amount',
                'name' => $this->l('Amount')
            ),
            array(
                'id_type' => 'percent',
                'name' => $this->l('Percent')
            ),
        );
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
                        'name' => 'EVERPSDONATION_AMOUNT',
                        'label' => $this->l('Donation amount for each cart'),
                        'desc' => $this->l('Type donation amount for each cart'),
                        'hint' => $this->l('This will be used to calculate donations amount'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Donation type'),
                        'desc' => $this->l('Is your donation percent or fixed amount ?'),
                        'hint' => $this->l('Will be used for calculation'),
                        'required' => true,
                        'name' => 'EVERPSDONATION_TYPE',
                        'options' => array(
                            'query' => $donation_type,
                            'id' => 'id_type',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'categories',
                        'name' => 'EVERPSDONATION_CATEGORIES',
                        'label' => $this->l('Category'),
                        'desc' => $this->l('Allow only these categories for donation'),
                        'hint' => $this->l('Only products in selected categories will be allowed for donations'),
                        'required' => true,
                        'tree' => $tree,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'EVERPSDONATION_AMOUNT' => Configuration::get('EVERPSDONATION_AMOUNT'),
            'EVERPSDONATION_TYPE' => Configuration::get('EVERPSDONATION_TYPE'),
            'EVERPSDONATION_CATEGORIES' => Tools::getValue(
                'EVERPSDONATION_CATEGORIES',
                json_decode(
                    Configuration::get(
                        'EVERPSDONATION_CATEGORIES'
                    )
                )
            ),
        );
    }

    public function postValidation()
    {
        if (((bool)Tools::isSubmit('submitEverpsdonationModule')) == true) {
            if (!Tools::getValue('EVERPSDONATION_AMOUNT')
                || !Validate::isFloat(Tools::getValue('EVERPSDONATION_AMOUNT'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Amount" is not valid');
            }
            if (!Tools::getValue('EVERPSDONATION_CATEGORIES')
                || !Validate::isArrayWithIds(Tools::getValue('EVERPSDONATION_CATEGORIES'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Categories" is not valid');
            }
            if (!Tools::getValue('EVERPSDONATION_TYPE')
                || !Validate::isString(Tools::getValue('EVERPSDONATION_TYPE'))
            ) {
                $this->postErrors[] = $this->l('Error : The field "Donation type" is not valid');
            }
        }
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        Configuration::updateValue(
            'EVERPSDONATION_AMOUNT',
            Tools::getValue('EVERPSDONATION_AMOUNT')
        );
        Configuration::updateValue(
            'EVERPSDONATION_TYPE',
            Tools::getValue('EVERPSDONATION_TYPE')
        );
        Configuration::updateValue(
            'EVERPSDONATION_CATEGORIES',
            json_encode(Tools::getValue('EVERPSDONATION_CATEGORIES')),
            true
        );
        $this->postSuccess[] = $this->l('All settings have been saved :-)');
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $controller_name = Tools::getValue('controller');
        if ($controller_name == 'product') {
            $this->context->controller->addJS($this->_path.'/views/js/product.js');
            $this->context->controller->addJS($this->_path.'/views/js/cart.js');
        }
        if ($controller_name == 'cart') {
            $this->context->controller->addJS($this->_path.'/views/js/cart.js');
        }
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayShoppingCartFooter()
    {
        return $this->hookDisplayShoppingCart();
    }

    public function hookDisplayCartModalFooter()
    {
        return $this->hookDisplayShoppingCart();
    }

    public function hookDisplayLeftColumn()
    {
        return $this->hookDisplayShoppingCart();
    }

    public function hookDisplayRightColumn()
    {
        return $this->hookDisplayShoppingCart();
    }

    public function hookDisplayReassurance()
    {
        // $product_donation = $this->getProductDonationAmount();
        $donation = $this->getDonationAmount();
        if (!Validate::isFloat($donation)
            || (float)$donation <= 0
        ) {
            return;
        }
        $link = new Link();
        $ajax_url = $link->getModuleLink(
            $this->name,
            'ajaxGetDonation'
        );
        $donation = Tools::displayPrice($donation);
        $this->context->smarty->assign(array(
            'donation' => $donation,
            'ajax_url' => $ajax_url,
            'cart_url' => $link->getPageLink('cart', true),
        ));
        return $this->display(__FILE__, 'views/templates/hook/product_donation.tpl');
    }

    public function hookDisplayShoppingCart()
    {
        $donation = $this->getDonationAmount();
        if (!Validate::isFloat($donation)
            || (float)$donation <= 0
        ) {
            return;
        }
        $link = new Link();
        $ajax_url = $link->getModuleLink(
            $this->name,
            'ajaxGetDonation'
        );
        $donation = Tools::displayPrice($donation);
        $this->context->smarty->assign(array(
            'donation' => $donation,
            'ajax_url' => $ajax_url,
            'cart_url' => $link->getPageLink('cart', true),
        ));
        return $this->display(__FILE__, 'views/templates/hook/cart_donation.tpl');
    }

    public function getProductDonationAmount($id_product, $id_product_attribute = 0, $qty = 1)
    {
        $price = Product::getPriceStatic(
            $id_product,
            false,
            $id_product_attribute,
            2,
            null,
            false,
            true,
            $qty,
            false,
            (int)Context::getContext()->customer->id
        );
        $settings = $this->getConfigFormValues();
        if ((float)$settings['EVERPSDONATION_AMOUNT'] <= 0) {
            return false;
        }
        if ($settings['EVERPSDONATION_TYPE'] == 'amount') {
            return (float)$settings['EVERPSDONATION_AMOUNT'];
        }
        $allowed_cats = $this->getAllowedCategories();
        $product = new Product(
            (int)$id_product
        );
        if (!in_array($product->id_category_default, $allowed_cats)) {
            return false;
        }
        $donation = (float)$price * (float)$settings['EVERPSDONATION_AMOUNT'] / 100;
        // Return donation total without currency
        return $donation;
    }

    /**
     * Get donation amount
     * @return float donation amount without currency | false
    */
    public function getDonationAmount()
    {
        $cart = Context::getContext()->cart;
        $cart_total = $cart->getOrderTotal(
            false,
            Cart::ONLY_PRODUCTS
        );
        if ($cart_total <= 0) {
            return false;
        }
        $settings = $this->getConfigFormValues();
        if ((float)$settings['EVERPSDONATION_AMOUNT'] <= 0) {
            return false;
        }
        if ($settings['EVERPSDONATION_TYPE'] == 'amount') {
            return (float)$settings['EVERPSDONATION_AMOUNT'];
        }
        $cartproducts = $cart->getProducts();
        $allowed_cats = $this->getAllowedCategories();
        $allowed_total = 0;
        foreach ($cartproducts as $cartproduct) {
            $product = new Product((int)$cartproduct['id_product']);
            if (!in_array($product->id_category_default, $allowed_cats)) {
                continue;
            }
            $allowed_total += (float)$cartproduct['total_wt'];
        }
        if ((float)$allowed_total <= 0) {
            return;
        }
        // Calculate donation total for each allowed product on cart
        $donation = (float)$allowed_total * (float)$settings['EVERPSDONATION_AMOUNT'] / 100;
        // Return donation total without currency
        return $donation;
    }

    private function getAllowedCategories()
    {
        $selected_cat = json_decode(
            Configuration::get(
                'EVERPSDONATION_CATEGORIES'
            )
        );
        if (!is_array($selected_cat)) {
            $selected_cat = array($selected_cat);
        }
        return $selected_cat;
    }
}
