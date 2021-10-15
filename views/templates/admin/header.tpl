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
<div class="panel everheader">
    <div class="panel-heading">
        <i class="icon icon-smile"></i> {l s='Ever Donation' mod='everpsdonation'}
    </div>
    <div class="panel-body">
        <div class="col-md-6">
            <a href="#everbottom" id="evertop">
               <img id="everlogo" src="{$everpsdonation_dir|escape:'htmlall':'UTF-8'}/logo.png" style="max-width: 120px;">
            </a>
            <strong>{l s='Welcome to Ever Donation !' mod='everpsdonation'}</strong><br />{l s='Please configure your this form to set donation messages' mod='everpsdonation'}<br />
            <p>
                <strong>{l s='Click on our logo to go direct to bottom' mod='everpsdonation'}</strong>
            </p>
        </div>
        <div class="col-md-6">
                <p class="alert alert-warning">
                    {l s='This module is free and will always be ! You can support our free modules by making a donation by clicking the button below' mod='everpsdonation'}
                </p>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="display: flex;justify-content: center;">
                <input type="hidden" name="cmd" value="_s-xclick" />
                <input type="hidden" name="hosted_button_id" value="3LE8ABFYJKP98" />
                <input type="image" src="https://www.team-ever.com/wp-content/uploads/2019/06/appel_a_dons-1.jpg" border="0" name="submit" title="Soutenez le développement des modules gratuits de Team Ever !" alt="Soutenez le développement des modules gratuits de Team Ever !" style="width: 150px;" />
                <img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1" />
                </form>
        </div>
    </div>
</div>
