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

<fieldset class="pm_fieldset">
    <div class="margin-form pm_title">
        <h2>{l s='Settings' mod='pm_productsbyattributes'}</h2>
    </div>

    <div class="clear"></div>

    {* Attributes to be split *}
    <div>
        <label>
            {l s='Attributes to split' mod='pm_productsbyattributes'}
        </label>
        <div class="margin-form">
            <select name="selectedGroups[]" onchange="handleSelectedGroupOption();" multiple="multiple">
                {foreach from=$attributeGroupOptions item=attributeGroupOption key=attributeGroupOptionValue}
                    <option value="{$attributeGroupOptionValue|intval}" {if isset($configurations.selectedGroups) && in_array($attributeGroupOptionValue, $configurations.selectedGroups)}selected="selected"{/if}>{$attributeGroupOption|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="clear"></div>

    <div class="more_options">

    {* Sort combinations by *}
    <div>
        <label>
            {l s='Sort combinations by' mod='pm_productsbyattributes'}
        </label>
        <div class="margin-form">
            <select name="sortCombinationBy">
                {foreach from=$sortCombinationsByOptions item=sortCombinationsByOption key=sortCombinationsByOptionValue}
                    <option value="{$sortCombinationsByOptionValue|escape:'html':'UTF-8'}" {if isset($configurations.sortCombinationBy) && $sortCombinationsByOptionValue == $configurations.sortCombinationBy}selected="selected"{/if}>{$sortCombinationsByOption|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>

    {* Combination to highlight *}
    <div>
        <label>
            {l s='If several combinations match your split settings, give priority to the one:' mod='pm_productsbyattributes'}
        </label>
        <div class="margin-form">
            <select name="combinationToHighlight">
                {foreach from=$highlightCombinationsOptions item=highlightCombinationOption key=highlightCombinationOptionValue}
                    <option value="{$highlightCombinationOptionValue|escape:'html':'UTF-8'}" {if isset($configurations.combinationToHighlight) && $highlightCombinationOptionValue == $configurations.combinationToHighlight}selected="selected"{/if}>{$highlightCombinationOption|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="clear"></div>

    {* Change product name? *}
    <div>
        <label>
            {l s='Change product name?' mod='pm_productsbyattributes'}
        </label>
        <div class="margin-form">
            <input type="radio" name="changeProductName" id="changeProductName_on" value="1" {if isset($configurations.changeProductName) && $configurations.changeProductName}checked="checked"{/if} onchange="handleChangeProductNameOption();">
            <label class="t" for="changeProductName_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
            <input type="radio" name="changeProductName" id="changeProductName_off" value="0" {if !isset($configurations.changeProductName) || !$configurations.changeProductName}checked="checked"{/if} onchange="handleChangeProductNameOption();">
            <label class="t" for="changeProductName_off">{l s='No' mod='pm_productsbyattributes'}</label>
            <p class="preference_description">
                {l s='If enabled, will add the attribute values to the product name. (e.g.: "Printed Dress" in Red and M sized would become "Printed Dress - Red - M")' mod='pm_productsbyattributes'}
            </p>
        </div>
    </div>

    <div class="clear"></div>

    {* Product name separator *}
    <div>
        <label class="nameSeparatorFormGroup">
            {l s='Name separator' mod='pm_productsbyattributes'}
        </label>
        <div class="margin-form nameSeparatorFormGroup">
            <input type="text" name="nameSeparator" value="{$configurations.nameSeparator}" />
            <p class="preference_description">
                {l s='Between the product name and the attribute value' mod='pm_productsbyattributes'}
            </p>
        </div>

        <div class="clear nameSeparatorFormGroup"></div>
    </div>

    {* Hide combinations without stock? *}
    <div>
        <label>
            {l s='Hide combinations without stock?' mod='pm_productsbyattributes'}
        </label>
        <div class="margin-form">
            <input type="radio" name="hideCombinationsWithoutStock" id="hideCombinationsWithoutStock_on" value="1" {if !empty($configurations.hideCombinationsWithoutStock) || !$psDispUnavailableAttr}checked="checked" {/if} {if (!$psDispUnavailableAttr || !psStockManagement)}disabled="disabled"{/if}>
            <label class="t" for="hideCombinationsWithoutStock_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
            <input type="radio" name="hideCombinationsWithoutStock" id="hideCombinationsWithoutStock_off" value="0" {if empty($configurations.hideCombinationsWithoutStock) && $psDispUnavailableAttr}checked="checked" {/if} {if (!$psDispUnavailableAttr || !psStockManagement)}disabled="disabled"{/if}>
            <label class="t" for="hideCombinationsWithoutStock_off">{l s='No' mod='pm_productsbyattributes'}</label>
        </div>
    </div>

    <div class="clear"></div>

    {if !$psDispUnavailableAttr|intval}
    <div>
        <label>
            &nbsp;
        </label>
        <div class="margin-form">
            <div class="alert warning">
                <p>{l s='We have detected that you don\'t show the unavailable attributes on a product page. The splitted product without stocks will not be shown' mod='pm_productsbyattributes'}</p>
            </div>
        </div>

        <div class="clear"></div>
    </div>
    {/if}

    {* Hide combinations without cover? *}
    <div>
        <label>
            {l s='Hide combinations without cover?' mod='pm_productsbyattributes'}
        </label>
        <div class="margin-form">
            <input type="radio" name="hideCombinationsWithoutCover" id="hideCombinationsWithoutCover_on" value="1" {if isset($configurations.hideCombinationsWithoutCover) && $configurations.hideCombinationsWithoutCover}checked="checked"{/if}>
            <label class="t" for="hideCombinationsWithoutCover_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
            <input type="radio" name="hideCombinationsWithoutCover" id="hideCombinationsWithoutCover_off" value="0" {if !isset($configurations.hideCombinationsWithoutCover) || !$configurations.hideCombinationsWithoutCover}checked="checked"{/if}>
            <label class="t" for="hideCombinationsWithoutCover_off">{l s='No' mod='pm_productsbyattributes'}</label>
        </div>
    </div>

    <div class="clear"></div>

    {* Display products from subcategories *}
        {if !$layeredModuleIsEnabled}
        <div>
            <label>
                {l s='Display products from subcategories' mod='pm_productsbyattributes'}
            </label>
            <div class="margin-form">
                <input type="radio" name="fullTree" id="fullTree_on" value="1" {if isset($configurations.fullTree) && $configurations.fullTree}checked="checked"{/if}>
                <label class="t" for="fullTree_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
                <input type="radio" name="fullTree" id="fullTree_off" value="0" {if !isset($configurations.fullTree) || !$configurations.fullTree}checked="checked"{/if}>
                <label class="t" for="fullTree_off">{l s='No' mod='pm_productsbyattributes'}</label>
            </div>

            <div class="clear"></div>
        </div>
        {/if}

    {* Enable automatic re-indexing when catalog changes are made? *}
    <div>
        <label>
            {l s='Enable automatic re-indexing when catalog changes are made?' mod='pm_productsbyattributes'}
        </label>
        <div class="margin-form">
            <input type="radio" name="autoReindex" id="autoReindex_on" value="1" {if isset($configurations.autoReindex) && $configurations.autoReindex}checked="checked"{/if}>
            <label class="t" for="autoReindex_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
            <input type="radio" name="autoReindex" id="autoReindex_off" value="0" {if !isset($configurations.autoReindex) || !$configurations.autoReindex}checked="checked"{/if}>
            <label class="t" for="autoReindex_off">{l s='No' mod='pm_productsbyattributes'}</label>
        </div>
    </div>

    <div class="clear"></div>

    </div>
    {* Maintenance Mode *}

    <div>
        <label>
            {l s='Maintenance Mode' mod='pm_productsbyattributes'}
        </label>
        <div class="margin-form">
            <input type="radio" name="maintenanceMode" id="maintenanceMode_on" value="1" {if isset($configurations.maintenanceMode) && $configurations.maintenanceMode}checked="checked"{/if}>
            <label class="t" for="maintenanceMode_on">{l s='Yes' mod='pm_productsbyattributes'}</label>
            <input type="radio" name="maintenanceMode" id="maintenanceMode_off" value="0" {if !isset($configurations.maintenanceMode) || !$configurations.maintenanceMode}checked="checked"{/if}>
            <label class="t" for="maintenanceMode_off">{l s='No' mod='pm_productsbyattributes'}</label>
            <p class="preference_description">
                 {l s='You must define a maintenance IP in your "Preferences Panel".' mod='pm_productsbyattributes'}
            </p>
        </div>
    </div>

    <div class="clear"></div>

    <div class="center">
        <input type="submit" class="button" value="{l s='Save' mod='pm_productsbyattributes'}" name="submitForm_{$pm_tab_identifier|escape:'html':'UTF-8'}">
    </div>
</fieldset>

{literal}
<script type="text/javascript">
	var color_groups = [{/literal}{$colorGroups|escape:'html':'UTF-8'}{literal}];
</script>
{/literal}
