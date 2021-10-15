{*
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
*}

<section class="card card-block d-none product-donation-block">
  <p>{l s='By validating this product to cart, you are making a donation worth' mod='everpsdonation'} <span id="product-donation-amount" data-donationlink="{$ajax_url}"></span></p>
</section>

<section class="card card-block donation-block mt-3">
  <p>{l s='By validating this cart' mod='everpsdonation'} <span id="cart-donation-amount" data-donationlink="{$ajax_url}">{$donation}</span> {l s='will be donated to an association' mod='everpsdonation'}</p>
</section>