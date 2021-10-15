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

class EverpsdonationAjaxGetDonationModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        $this->ajax = true;
    }

    public function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        if ($this->isSeven) {
            return Context::getContext()->getTranslator()->trans(
                $string,
                [],
                'Modules.Everpsdonation.AjaxGetDonation'
            );
        }

        return parent::l($string, $specific, $class, $addslashes, $htmlentities);
    }

    /**
     * Ajax display product donation
     */
    public function displayAjaxProductDonation()
    {
        if (Tools::getValue('id_product')
            && !Validate::isInt(Tools::getValue('id_product'))
        ) {
            die(json_encode(array(
                'return' => false,
                'error' => $this->l('Product is not valid', 'everpsdonation')
            )));
        }
        if (Tools::getValue('id_product_attribute')
            && !Validate::isInt(Tools::getValue('id_product_attribute'))
        ) {
            die(json_encode(array(
                'return' => false,
                'error' => $this->l('Product attribute is not valid', 'everpsdonation')
            )));
        }
        if (!Tools::getValue('qty')
            || !Validate::isInt(Tools::getValue('qty'))
            || Tools::getValue('qty') == 0
        ) {
            die(json_encode(array(
                'return' => false,
                'error' => $this->l('Quantity is not valid', 'everpsdonation')
            )));
        }
        $module = Module::getInstanceByName('everpsdonation');
        $donation = $module->getProductDonationAmount(
            (int)Tools::getValue('id_product'),
            (int)Tools::getValue('id_product_attribute'),
            (int)Tools::getValue('qty')
        );
        if (Validate::isFloat($donation)) {
            $donation = Tools::displayPrice($donation);
            die(json_encode(array(
                'return' => true,
                'success' => $this->l('By adding this quantity to cart, you will donate'),
                'donation' => $donation
            )));
        } else {
            die(json_encode(array(
                'return' => false,
                'error' => $this->l('No donation', 'everpsdonation')
            )));
        }
    }
    
    public function displayAjaxCartDonation()
    {
        $module = Module::getInstanceByName('everpsdonation');
        $donation = $module->getDonationAmount();
        if (Validate::isFloat($donation)) {
            $donation = Tools::displayPrice($donation);
            die(json_encode(array(
                'return' => true,
                'success' => $this->l('By validating your current cart, you will donate'),
                'donation' => $donation
            )));
        } else {
            die(json_encode(array(
                'return' => false,
                'error' => $this->l('No donation', 'everpsdonation')
            )));
        }
    }
}
