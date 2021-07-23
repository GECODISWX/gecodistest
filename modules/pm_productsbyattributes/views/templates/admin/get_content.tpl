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

<div class="bootstrap">
    <div class="clear">&nbsp;</div>

    {foreach from=$warnings item=warning}
        <div class="alert {if $pm_isPS16}alert-{/if}warning {if !$pm_isPS16}ps15{/if}">
            <p>{$warning}{*HTML*}</p>
        </div>
    {/foreach}

    <div id="pm-module-configuration-wrapper clearfix">
        <!-- Nav tabs -->
            {if $pm_isPS16}
            <div class="col-lg-2">
                <div class="list-group">
                    {foreach from=$pm_tabs item=pm_tab key=pm_tab_identifier}
                    <a href="#{$pm_tab_identifier|escape:'html':'UTF-8'}" class="list-group-item {if isset($pm_selected_tab) and $pm_selected_tab eq $pm_tab_identifier}selected_tab active{/if}" data-toggle="tab" data-identifier="{$pm_tab_identifier|escape:'html':'UTF-8'}">{if isset($pm_tab.icon)}<i class="icon-{$pm_tab.icon|escape:'html':'UTF-8'}" ></i> {/if}{$pm_tab.label|escape:'html':'UTF-8'} {if isset($pm_tab.badge) && $pm_tab.badge}<span class="badge badge-warning">{$pm_tab.badge|intval}</span>{/if}</a>
                    {/foreach}
                </div>
            </div>
            {/if}
            <form method="post">
                <input type="hidden" name="submitModuleConfiguration" value="1" />
                <input type="hidden" name="selected_tab" value="{$pm_selected_tab|escape:'html':'UTF-8'}" />
                <!-- Tab panes -->
                <div class="tab-content col-lg-10">
                        {foreach from=$pm_tabs item=pm_tab key=pm_tab_identifier}
                        <div class="tab-pane {if isset($pm_selected_tab) && $pm_selected_tab eq $pm_tab_identifier}active{/if}" id="{$pm_tab_identifier|escape:'html':'UTF-8'}">
                        {if !isset($pm_tab.content)}
                            {if isset($pm_tab.type) && $pm_tab.type eq 'core'}
                                {include file="./core/tabs/{$pm_tab_identifier}.tpl" pm_tab_identifier=$pm_tab_identifier}
                            {else}
                                {include file="./tabs/{$pm_tab_identifier}.tpl" pm_tab_identifier=$pm_tab_identifier pm_tab=$pm_tab}
                            {/if}
                        {else}
                            {$pm_tab.content}{* HTML *}
                        {/if}
                        </div>
                        {/foreach}
                </div>
            </form>
    </div><!-- end pm-module-configuration-wrapper -->
</div>
