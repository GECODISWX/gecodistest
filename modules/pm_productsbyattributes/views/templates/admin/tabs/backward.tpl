{*
*
* Products by Attributes
*
* @author Presta-Module.com <support@presta-module.com>
* @copyright Presta-Module
*
*           ____     __  __
*          |  _ \   |  \/  |
*          | |_) |  | |\/| |
*          |  __/   | |  | |
*          |_|      |_|  |_|
*
*}

<div class="panel form-horizontal">
    <div class="panel-heading">
        <i class="icon-{$pm_tab.icon|escape:'html':'UTF-8'}"></i> {l s='Compatibility settings' mod='pm_productsbyattributes'}
    </div>
    <div class="form-wrapper">
	{* Add ID to anchor? *}
    	<div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Add ID to anchor?' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="addIDToAnchor" id="addIDToAnchor_on" value="1" {if isset($configurations.addIDToAnchor) && $configurations.addIDToAnchor}checked="checked"{/if}>
                    <label class="t" for="addIDToAnchor_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="addIDToAnchor" id="addIDToAnchor_off" value="0" {if !isset($configurations.addIDToAnchor) || !$configurations.addIDToAnchor}checked="checked"{/if}>
                    <label class="t" for="addIDToAnchor_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
                <div class="col-lg-12">
                    <div class="help-block">
                        {l s='If your theme is not compliant with ID in anchor URL, you need to disable this option' mod='pm_productsbyattributes'}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <button type="submit" value="1" name="submitForm_{$pm_tab_identifier|escape:'html':'UTF-8'}" class="btn btn-default pull-right">
            <i class="process-icon-save"></i> {l s='Save' mod='pm_productsbyattributes'}
        </button>
    </div>
</div>