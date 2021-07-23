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
        <i class="icon-{$pm_tab.icon|escape:'html':'UTF-8'}"></i> {l s='Settings' mod='pm_productsbyattributes'}
    </div>
    <div class="form-wrapper">
    {* Attributes to split *}
        <div class="form-group selectedGroups">
            <label class="control-label col-lg-4">
                {l s='Attributes to split' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <select name="selectedGroups[]" class="inline fixed-width-xxxl" onchange="handleSelectedGroupOption();" multiple="multiple">
                    {foreach from=$attributeGroupOptions item=attributeGroupOption key=attributeGroupOptionValue}
                        <option value="{$attributeGroupOptionValue|intval}" {if isset($configurations.selectedGroups) && in_array($attributeGroupOptionValue, $configurations.selectedGroups)}selected="selected"{/if}>{$attributeGroupOption|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    {* Sort combinations by *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Sort combinations by' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <select name="sortCombinationBy" class="form-control fixed-width-xxl">
                    {foreach from=$sortCombinationsByOptions item=sortCombinationsByOption key=sortCombinationsByOptionValue}
                        <option value="{$sortCombinationsByOptionValue|escape:'html':'UTF-8'}" {if isset($configurations.sortCombinationBy) && $sortCombinationsByOptionValue == $configurations.sortCombinationBy}selected="selected"{/if}>{$sortCombinationsByOption|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    {* Combination to highlight *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='If several combinations match your split settings, give priority to the one:' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <select name="combinationToHighlight" class="form-control fixed-width-xxl">
                    {foreach from=$highlightCombinationsOptions item=highlightCombinationOption key=highlightCombinationOptionValue}
                        <option value="{$highlightCombinationOptionValue|escape:'html':'UTF-8'}" {if isset($configurations.combinationToHighlight) && $highlightCombinationOptionValue == $configurations.combinationToHighlight}selected="selected"{/if}>{$highlightCombinationOption|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    {* Hide color squares?  *}
        {if $showColorOption|intval}
            <div class="form-group colorSquaresFormGroup">
                <label class="control-label col-lg-4">
                    {l s='Hide color squares?' mod='pm_productsbyattributes'}
                </label>
                <div class="col-lg-7">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="hideColorSquares" id="hideColorSquares_on" value="1" {if isset($configurations.hideColorSquares) && $configurations.hideColorSquares}checked="checked"{/if}>
                        <label class="t" for="hideColorSquares_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                        <input type="radio" name="hideColorSquares" id="hideColorSquares_off" value="0" {if !isset($configurations.hideColorSquares) || !$configurations.hideColorSquares}checked="checked"{/if}>
                        <label class="t" for="hideColorSquares_off">{l s='No' mod='pm_productsbyattributes'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                    <div class="col-lg-12">
                        <div class="help-block">
                            {l s='If you choose an attribute group which is a color group, you can decide to hide the color squares into the product list' mod='pm_productsbyattributes'}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    {* Change product name? *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Change product name?' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="changeProductName" id="changeProductName_on" value="1" {if isset($configurations.changeProductName) && $configurations.changeProductName}checked="checked"{/if} onchange="handleChangeProductNameOption();">
                    <label class="t" for="changeProductName_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="changeProductName" id="changeProductName_off" value="0" {if !isset($configurations.changeProductName) || !$configurations.changeProductName}checked="checked"{/if} onchange="handleChangeProductNameOption();">
                    <label class="t" for="changeProductName_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
                <div class="col-lg-12">
                    <div class="help-block">
                        {l s='If enabled, will add the attribute values to the product name. (e.g.: "Printed Dress" in Red and M sized would become "Printed Dress - Red - M")' mod='pm_productsbyattributes'}
                    </div>
                </div>
            </div>
        </div>
    {* Product name separator *}
        <div class="form-group nameSeparatorFormGroup">
            <label class="control-label col-lg-4">
                {l s='Name separator' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <input type="text" name="nameSeparator" value="{$configurations.nameSeparator}" />

                <div class="col-lg-12">
                    <div class="help-block">
                        {l s='Between the product name and the attribute value' mod='pm_productsbyattributes'}
                    </div>
                </div>
            </div>
        </div>
    {* Hide combinations without stock? *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Hide combinations without stock?' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="hideCombinationsWithoutStock" id="hideCombinationsWithoutStock_on" value="1" {if !empty($configurations.hideCombinationsWithoutStock) || !$psDispUnavailableAttr}checked="checked" {/if} {if (!$psDispUnavailableAttr || !$psStockManagement)}disabled="disabled"{/if}>
                    <label class="t" for="hideCombinationsWithoutStock_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="hideCombinationsWithoutStock" id="hideCombinationsWithoutStock_off" value="0" {if empty($configurations.hideCombinationsWithoutStock) && $psDispUnavailableAttr}checked="checked" {/if} {if (!$psDispUnavailableAttr || !$psStockManagement)}disabled="disabled"{/if}>
                    <label class="t" for="hideCombinationsWithoutStock_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
        {if !$psDispUnavailableAttr|intval}
        <div class="form-group">
            <label class="control-label col-lg-4">
                &nbsp;
            </label>
            <div class="col-lg-7">
                <div class="alert alert-warning">
                    <p>{l s='We have detected that you don\'t show the unavailable attributes on a product page. The splitted product without stocks will not be shown' mod='pm_productsbyattributes'}</p>
                </div>
            </div>
        </div>
        {/if}

        <div class="clear nameSeparatorFormGroup"></div>

    {* Hide combinations without cover? *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Hide combinations without cover?' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="hideCombinationsWithoutCover" id="hideCombinationsWithoutCover_on" value="1" {if isset($configurations.hideCombinationsWithoutCover) && $configurations.hideCombinationsWithoutCover}checked="checked"{/if}>
                    <label class="t" for="hideCombinationsWithoutCover_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="hideCombinationsWithoutCover" id="hideCombinationsWithoutCover_off" value="0" {if !isset($configurations.hideCombinationsWithoutCover) || !$configurations.hideCombinationsWithoutCover}checked="checked"{/if}>
                    <label class="t" for="hideCombinationsWithoutCover_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    {* Display products from subcategories *}
        {if !$layeredModuleIsEnabled}
            <div class="form-group">
                <label class="control-label col-lg-4">
                    {l s='Display products from subcategories' mod='pm_productsbyattributes'}
                </label>
                <div class="col-lg-7">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="fullTree" id="fullTree_on" value="1" {if isset($configurations.fullTree) && $configurations.fullTree}checked="checked"{/if}>
                        <label class="t" for="fullTree_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                        <input type="radio" name="fullTree" id="fullTree_off" value="0" {if !isset($configurations.fullTree) || !$configurations.fullTree}checked="checked"{/if}>
                        <label class="t" for="fullTree_off">{l s='No' mod='pm_productsbyattributes'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>
        {/if}
    {* Enable automatic re-indexing when catalog changes are made? *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Enable automatic re-indexing when catalog changes are made?' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="autoReindex" id="autoReindex_on" value="1" {if isset($configurations.autoReindex) && $configurations.autoReindex}checked="checked"{/if}>
                    <label class="t" for="autoReindex_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="autoReindex" id="autoReindex_off" value="0" {if !isset($configurations.autoReindex) || !$configurations.autoReindex}checked="checked"{/if}>
                    <label class="t" for="autoReindex_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <button type="submit" value="1" name="submitForm_{$pm_tab_identifier|escape:'html':'UTF-8'}" class="btn btn-default pull-right">
            <i class="process-icon-save"></i> {l s='Save' mod='pm_productsbyattributes'}
        </button>
    </div>
</div>

{if $showPagesOption|intval}
<div class="panel form-horizontal showPagesOption">
    <div class="panel-heading">
        <i class="icon-files-o"></i> {l s='Enable the module on following pages:' mod='pm_productsbyattributes'}
    </div>
    <div class="form-wrapper">
    {* Category *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Category' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="enabledControllers[Category]" id="enabledControllers[Category]_on" value="1" {if isset($configurations.enabledControllers['Category']) && $configurations.enabledControllers['Category']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[Category]_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="enabledControllers[Category]" id="enabledControllers[Category]_off" value="0" {if !isset($configurations.enabledControllers['Category']) || !$configurations.enabledControllers['Category']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[Category]_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    {* NewProducts *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='New products' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="enabledControllers[NewProducts]" id="enabledControllers[NewProducts]_on" value="1" {if isset($configurations.enabledControllers['NewProducts']) && $configurations.enabledControllers['NewProducts']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[NewProducts]_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="enabledControllers[NewProducts]" id="enabledControllers[NewProducts]_off" value="0" {if !isset($configurations.enabledControllers['NewProducts']) || !$configurations.enabledControllers['NewProducts']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[NewProducts]_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    {* BestSales *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Best sales' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="enabledControllers[BestSales]" id="enabledControllers[BestSales]_on" value="1" {if isset($configurations.enabledControllers['BestSales']) && $configurations.enabledControllers['BestSales']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[BestSales]_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="enabledControllers[BestSales]" id="enabledControllers[BestSales]_off" value="0" {if !isset($configurations.enabledControllers['BestSales']) || !$configurations.enabledControllers['BestSales']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[BestSales]_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    {* PricesDrop *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Prices drop' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="enabledControllers[PricesDrop]" id="enabledControllers[PricesDrop]_on" value="1" {if isset($configurations.enabledControllers['PricesDrop']) && $configurations.enabledControllers['PricesDrop']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[PricesDrop]_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="enabledControllers[PricesDrop]" id="enabledControllers[PricesDrop]_off" value="0" {if !isset($configurations.enabledControllers['PricesDrop']) || !$configurations.enabledControllers['PricesDrop']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[PricesDrop]_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    {* Search *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Search' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="enabledControllers[Search]" id="enabledControllers[Search]_on" value="1" {if isset($configurations.enabledControllers['Search']) && $configurations.enabledControllers['Search']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[Search]_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="enabledControllers[Search]" id="enabledControllers[Search]_off" value="0" {if !isset($configurations.enabledControllers['Search']) || !$configurations.enabledControllers['Search']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[Search]_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    {* Manufacturer *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Manufacturer' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="enabledControllers[Manufacturer]" id="enabledControllers[Manufacturer]_on" value="1" {if isset($configurations.enabledControllers['Manufacturer']) && $configurations.enabledControllers['Manufacturer']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[Manufacturer]_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="enabledControllers[Manufacturer]" id="enabledControllers[Manufacturer]_off" value="0" {if !isset($configurations.enabledControllers['Manufacturer']) || !$configurations.enabledControllers['Manufacturer']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[Manufacturer]_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    {* Supplier *}
        <div class="form-group">
            <label class="control-label col-lg-4">
                {l s='Supplier' mod='pm_productsbyattributes'}
            </label>
            <div class="col-lg-7">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="enabledControllers[Supplier]" id="enabledControllers[Supplier]_on" value="1" {if isset($configurations.enabledControllers['Supplier']) && $configurations.enabledControllers['Supplier']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[Supplier]_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                    <input type="radio" name="enabledControllers[Supplier]" id="enabledControllers[Supplier]_off" value="0" {if !isset($configurations.enabledControllers['Supplier']) || !$configurations.enabledControllers['Supplier']}checked="checked"{/if}>
                    <label class="t" for="enabledControllers[Supplier]_off">{l s='No' mod='pm_productsbyattributes'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <button type="submit" value="1" name="submitForm_{$pm_tab_identifier|escape:'html':'UTF-8'}" class="btn btn-default pull-right">
            <i class="process-icon-save"></i> {l s='Save' mod='pm_productsbyattributes'}
        </button>
    </div>
</div>
{/if}

{literal}
<script type="text/javascript">
	var color_groups = [{/literal}{$colorGroups|escape:'html':'UTF-8'}{literal}];
</script>
{/literal}
