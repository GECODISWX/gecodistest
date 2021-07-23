{*
* 2007-2015 PrestaShop
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<div class="js-mailalert form-inline"
     data-url="{url entity='module' name='ps_emailalerts' controller='actions' params=['process' => 'add']}">
    <form>

    <div class="input-group mr-2 mb-2">
        {if isset($email) AND $email}
            <input type="email" placeholder="{l s='your@email.com' d='Modules.Mailalerts.Shop'}" class="form-control"/>
            <br/>
        {/if}
    </div>
    <div class="input-group mr-2 mb-2">
            {if isset($id_module)}
                {hook h='displayGDPRConsent' id_module=$id_module}
            {/if}
    </div>
    <div class="input-group mr-2 mb-2">
        <input type="hidden" value="{$id_product}"/>
        <input type="hidden" value="{$id_product_attribute}"/>
        <input type="submit"  class="btn btn-secondary"
           onclick="return addNotification();"  value="{l s='Notify me when available' d='Modules.Mailalerts.Shop'}" />
    </div>
        <div class="input-group mr-2 mb-2">
    <span class="alert alert-info js-mailalert-response" style="display:none;"></span>
        </div>
    </form>
</div>
