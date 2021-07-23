{*
    *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
    *
    * @author    Línea Gráfica E.C.E. S.L.
    * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
    * @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
*}

{if isset($fields.title)}<h3>{$fields.title|escape:'htmlall':'UTF-8'}</h3>{/if}

{block name="defaultForm"}
{if isset($tabs) && $tabs|count}

<ul id="lg-tabs" class="nav nav-tabs" />
{foreach $tabs as $k => $tab}
<li><a href="#{$k|escape:'htmlall':'UTF-8'}" data-toggle="tab">{$tab|escape:'htmlall':'UTF-8'}</a></li>
{/foreach}

{/if}
</ul>
<form id="{if isset($fields.form.form.id_form)}{$fields.form.form.id_form|escape:'htmlall':'UTF-8'}{else}{if $table == null}configuration_form{else}{$table|escape:'htmlall':'UTF-8'}_form{/if}{/if}" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&amp;token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data"{if isset($style)} style="{$style|escape:'htmlall':'UTF-8'}"{/if} novalidate>
    {if $form_id}
        <input type="hidden" name="{$identifier|escape:'htmlall':'UTF-8'}" id="{$identifier|escape:'htmlall':'UTF-8'}" value="{$form_id|escape:'htmlall':'UTF-8'}" />
    {/if}
    {if !empty($submit_action)}
        <input type="hidden" name="{$submit_action|escape:'htmlall':'UTF-8'}" value="1" />
    {/if}
    
    {foreach $fields as $f => $fieldset}
        {block name="fieldset"}
        <div class="panel" id="fieldset_{$f|escape:'htmlall':'UTF-8'}">
            {foreach $fieldset.form as $key => $field}
                {if $key == 'legend'}
                    {block name="legend"}
                        <div class="panel-heading">
                            {if isset($field.image) && isset($field.title)}<img src="{$field.image|escape:'htmlall':'UTF-8'}" alt="{$field.title|escape:'htmlall':'UTF-8'}" />{/if}
                            {if isset($field.icon)}<i class="{$field.icon|escape:'htmlall':'UTF-8'}"></i>{/if}
                            {$field.title|escape:'htmlall':'UTF-8'}
                        </div>
                    {/block}
                {elseif $key == 'description' && $field}
                    <div class="alert alert-info">{$field|escape:'htmlall':'UTF-8'}</div>
                {elseif $key == 'input'}
                    <div class="form-wrapper">
                    {foreach $field as $input}
                        {block name="input_row"}
                        <div class="form-group{if isset($input.form_group_class)} {$input.form_group_class|escape:'htmlall':'UTF-8'}{/if}{if $input.type == 'hidden'} hide{/if}"{if $input.name == 'id_state'} id="contains_states"{if !$contains_states} style="display:none;"{/if}{/if} {if isset($tabs) && isset($input.tab)}data-tab-id="{$input.tab|escape:'htmlall':'UTF-8'}"{/if}>
                        {if $input.type == 'hidden'}
                            <input type="hidden" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$input.name|escape:'htmlall':'UTF-8'}" value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
                        {else}
                            {block name="label"}
                                {if isset($input.label)}
                                    <label for="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{if isset($input.lang) AND $input.lang}_{$current_id_lang|escape:'htmlall':'UTF-8'}{/if}{else}{$input.name|escape:'htmlall':'UTF-8'}{if isset($input.lang) AND $input.lang}_{$current_id_lang|escape:'htmlall':'UTF-8'}{/if}{/if}" class="control-label col-lg-3 {if isset($input.required) && $input.required && $input.type != 'radio'}required{/if}">
                                        {if isset($input.hint)}
                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                            title="{if is_array($input.hint)}
                                                    {foreach $input.hint as $hint}
                                                        {if is_array($hint)}
                                                            {$hint.text|escape:'htmlall':'UTF-8'}
                                                        {else}
                                                            {$hint|escape:'htmlall':'UTF-8'}
                                                        {/if}
                                                    {/foreach}
                                                {else}
                                                    {$input.hint|escape:'htmlall':'UTF-8'}
                                                {/if}">
                                        {/if}
                                        {$input.label|escape:'quotes':'UTF-8'}
                                        {if isset($input.hint)}
                                        </span>
                                        {/if}
                                    </label>
                                {/if}
                            {/block}

                            {block name="field"}
                                <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if} {if !isset($input.label)}col-lg-offset-3{/if}"  {if isset($input.tab)}data-tab-id="{$input.tab|escape:'html':'UTF-8'}"{/if}>
                                {block name="input"}
                                {if $input.type == 'text' || $input.type == 'tags'}
                                    {if isset($input.lang) AND $input.lang}
                                    {if $languages|count > 1}
                                    <div class="form-group" {if isset($input.tab)}data-tab-id="{$input.tab|escape:'html':'UTF-8'}"{/if}>
                                    {/if}
                                    {foreach $languages as $language}
                                        {assign var='value_text' value=$fields_value[$input.name][$language.id_lang]}
                                        {if $languages|count > 1}
                                        <div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                            <div class="col-lg-9">
                                        {/if}
                                                {if $input.type == 'tags'}
                                                    {literal}
                                                        {literal}
                                                        <script type="text/javascript">
                                                            $().ready(function () {
                                                                var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}{literal}';
                                                                $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' js=1}{literal}'});


                                                                $('{/literal}#{$table|escape:'htmlall':'UTF-8'}{literal}_form').submit( function() {
                                                                    $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                                                });
                                                            });
                                                            {/literal}
                                                        </script>
                                                    {/literal}
                                                {/if}
                                                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                                <div class="input-group {if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}">
                                                {/if}
                                                {if isset($input.maxchar)}
                                                <span id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}_counter" class="input-group-addon">
                                                    <span class="text-count-down">{$input.maxchar|escape:'htmlall':'UTF-8'}</span>
                                                </span>
                                                {/if}
                                                {if isset($input.prefix)}
                                                    <span class="input-group-addon">
                                                      {$input.prefix|escape:'htmlall':'UTF-8'}
                                                    </span>
                                                    {/if}
                                                <input type="text"
                                                    id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}"
                                                    name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                                                    class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                                                    value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
                                                    onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
                                                    {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                                    {if isset($input.maxchar)} data-maxchar="{$input.maxchar|escape:'htmlall':'UTF-8'}"{/if}
                                                    {if isset($input.maxlength)} maxlength="{$input.maxlength|escape:'htmlall':'UTF-8'}"{/if}
                                                    {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                                    {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                                    {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                                    {if isset($input.required) && $input.required} required="required" {/if}
                                                    {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}"{/if} />
                                                    {if isset($input.suffix)}
                                                    <span class="input-group-addon">
                                                      {$input.suffix|escape:'htmlall':'UTF-8'}
                                                    </span>
                                                    {/if}
                                                {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                                </div>
                                                {/if}
                                        {if $languages|count > 1}
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                    {$language.iso_code|escape:'htmlall':'UTF-8'}
                                                    <i class="icon-caret-down"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {foreach from=$languages item=language}
                                                    <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'javascript':'UTF-8'});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a></li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        </div>
                                        {/if}
                                    {/foreach}
                                    {if isset($input.maxchar)}
                                    <script type="text/javascript">
                                    function countDown($source, $target) {
                                        var max = $source.attr("data-maxchar");
                                        $target.html(max-$source.val().length);

                                        $source.keyup(function(){
                                            $target.html(max-$source.val().length);
                                        });
                                    }

                                    $(document).ready(function(){
                                    {foreach from=$languages item=language}
                                        countDown($("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}{/if}_counter"));
                                    {/foreach}
                                    });
                                    </script>
                                    {/if}
                                    {if $languages|count > 1}
                                    </div>
                                    {/if}
                                    {else}
                                        {if $input.type == 'tags'}
                                            {literal}
                                            <script type="text/javascript">
                                                $().ready(function () {
                                                    var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}{literal}';
                                                    $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' mod='lgcookieslaw'}{literal}'});
                                                    $({/literal}'#{$table|escape:'htmlall':'UTF-8'}{literal}_form').submit( function() {
                                                        $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                                    });
                                                });
                                            </script>
                                            {/literal}
                                        {/if}
                                        {assign var='value_text' value=$fields_value[$input.name]}
                                        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                        <div class="input-group {if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}">
                                        {/if}
                                        {if isset($input.maxchar)}
                                        <span id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|escape:'htmlall':'UTF-8'}</span></span>
                                        {/if}
                                        {if isset($input.prefix)}
                                        <span class="input-group-addon">
                                          {$input.prefix|escape:'htmlall':'UTF-8'}
                                        </span>
                                        {/if}
                                        <input type="text"
                                            name="{$input.name|escape:'htmlall':'UTF-8'}"
                                            id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                            value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
                                            class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                                            {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.maxchar)} data-maxchar="{$input.maxchar|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.maxlength)} maxlength="{$input.maxlength|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.class)} class="{$input.class|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                            {if isset($input.required) && $input.required } required="required" {/if}
                                            {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}"{/if}
                                            />
                                        {if isset($input.suffix)}
                                        <span class="input-group-addon">
                                          {$input.suffix|escape:'htmlall':'UTF-8'}
                                        </span>
                                        {/if}
                                        
                                        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                                        </div>
                                        {/if}
                                        {if isset($input.maxchar)}
                                        <script type="text/javascript">
                                        function countDown($source, $target) {
                                            var max = $source.attr("data-maxchar");
                                            $target.html(max-$source.val().length);

                                            $source.keyup(function(){
                                                $target.html(max-$source.val().length);
                                            });
                                        }
                                        $(document).ready(function(){
                                            countDown($("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter"));
                                        });
                                        </script>
                                        {/if}
                                    {/if}
                                {elseif $input.type == 'textbutton'}
                                    {assign var='value_text' value=$fields_value[$input.name]}
                                    <div class="row">
                                        <div class="col-lg-9">
                                        {if isset($input.maxchar)}
                                        <div class="input-group">
                                            <span id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter" class="input-group-addon">
                                                <span class="text-count-down">{$input.maxchar|escape:'htmlall':'UTF-8'}</span>
                                            </span>
                                        {/if}
                                        <input type="text"
                                            name="{$input.name|escape:'htmlall':'UTF-8'}"
                                            id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                            value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'htmlall':'UTF-8'}{else}{$value_text|escape:'htmlall':'UTF-8'}{/if}"
                                            class="{if $input.type == 'tags'}tagify {/if}{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                                            {if isset($input.size)} size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.maxchar)} data-maxchar="{$input.maxchar|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.maxlength)} maxlength="{$input.maxlength|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.class)} class="{$input.class|escape:'htmlall':'UTF-8'}"{/if}
                                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                            {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'htmlall':'UTF-8'}"{/if}
                                            />
                                        {if isset($input.suffix)}{$input.suffix|escape:'htmlall':'UTF-8'}{/if}
                                        {if isset($input.maxchar)}
                                        </div>
                                        {/if}
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-default{if isset($input.button.attributes['class'])} {$input.button.attributes['class']|escape:'htmlall':'UTF-8'}{/if}{if isset($input.button.class)} {$input.button.class|escape:'htmlall':'UTF-8'}{/if}"
                                                {foreach from=$input.button.attributes key=name item=value}
                                                    {if $name|lower != 'class'}
                                                     {$name|escape:'htmlall':'UTF-8'}="{$value|escape:'htmlall':'UTF-8'}"
                                                    {/if}
                                                {/foreach} >
                                                {$input.button.label|escape:'htmlall':'UTF-8'}
                                            </button>
                                        </div>
                                    </div>
                                    {if isset($input.maxchar)}
                                    <script type="text/javascript">
                                        function countDown($source, $target) {
                                            var max = $source.attr("data-maxchar");
                                            $target.html(max-$source.val().length);
                                            $source.keyup(function(){
                                                $target.html(max-$source.val().length);
                                            });
                                        }
                                        $(document).ready(function() {
                                            countDown($("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}_counter"));
                                        });
                                    </script>
                                    {/if}
                                {elseif $input.type == 'select'}
                                    {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
                                        {$input.empty_message|escape:'htmlall':'UTF-8'}
                                        {$input.required = false}
                                        {$input.desc = null}
                                    {else}
                                        <select name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if} fixed-width-xl"
                                                id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                                {if isset($input.multiple)}multiple="multiple" {/if}
                                                {if isset($input.size)}size="{$input.size|escape:'htmlall':'UTF-8'}"{/if}
                                                {if isset($input.onchange)}onchange="{$input.onchange|escape:'htmlall':'UTF-8'}"{/if}>
                                            {if isset($input.options.default)}
                                                <option value="{$input.options.default.value|escape:'htmlall':'UTF-8'}">{$input.options.default.label|escape:'htmlall':'UTF-8'}</option>
                                            {/if}
                                            {if isset($input.options.optiongroup)}
                                                {foreach $input.options.optiongroup.query AS $optiongroup}
                                                    <optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'htmlall':'UTF-8'}">
                                                        {foreach $optiongroup[$input.options.options.query] as $option}
                                                            <option value="{$option[$input.options.options.id]|escape:'htmlall':'UTF-8'}"
                                                                {if isset($input.multiple)}
                                                                    {foreach $fields_value[$input.name] as $field_value}
                                                                        {if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
                                                                    {/foreach}
                                                                {else}
                                                                    {if $fields_value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                                                {/if}
                                                            >{$option[$input.options.options.name]|escape:'htmlall':'UTF-8'}</option>
                                                        {/foreach}
                                                    </optgroup>
                                                {/foreach}
                                            {else}
                                                {foreach $input.options.query AS $option}
                                                    {if is_object($option)}
                                                        <option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}"
                                                            {if isset($input.multiple)}
                                                                {foreach $fields_value[$input.name] as $field_value}
                                                                    {if $field_value == $option->$input.options.id}
                                                                        selected="selected"
                                                                    {/if}
                                                                {/foreach}
                                                            {else}
                                                                {if $fields_value[$input.name] == $option->$input.options.id}
                                                                    selected="selected"
                                                                {/if}
                                                            {/if}
                                                        >{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
                                                    {elseif $option == "-"}
                                                        <option value="">-</option>
                                                    {else}
                                                        <option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}"
                                                            {if isset($input.multiple)}
                                                                {foreach $fields_value[$input.name] as $field_value}
                                                                    {if $field_value == $option[$input.options.id]}
                                                                        selected="selected"
                                                                    {/if}
                                                                {/foreach}
                                                            {else}
                                                                {if $fields_value[$input.name] == $option[$input.options.id]}
                                                                    selected="selected"
                                                                {/if}
                                                            {/if}
                                                        >{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>

                                                    {/if}
                                                {/foreach}
                                            {/if}
                                        </select>
                                    {/if}
                                {elseif $input.type == 'radio'}
                                    {foreach $input.values as $value}
                                        <div class="radio {if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}">
                                            <label>
                                            <input type="radio" name="{$input.name|escape:'htmlall':'UTF-8'}" id="{$value.id|escape:'htmlall':'UTF-8'}" value="{$value.value|escape:'htmlall':'UTF-8'}"
                                                {if $fields_value[$input.name] == $value.value}checked="checked"{/if}
                                                {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if} />
                                                {$value.label|escape:'htmlall':'UTF-8'}
                                            </label>
                                        </div>
                                        {if isset($value.p) && $value.p}<p class="help-block">{$value.p|escape:'htmlall':'UTF-8'}</p>{/if}
                                    {/foreach}
                                {elseif $input.type == 'switch'}
                                    <span class="switch prestashop-switch fixed-width-lg">
                                        {foreach $input.values as $value}
                                        <input
                                            type="radio"
                                            name="{$input.name|escape:'htmlall':'UTF-8'}"
                                            {if $value.value == 1}
                                                id="{$input.name|escape:'htmlall':'UTF-8'}_on"
                                            {else}
                                                id="{$input.name|escape:'htmlall':'UTF-8'}_off"
                                            {/if}
                                            value="{$value.value|escape:'htmlall':'UTF-8'}"
                                            {if $fields_value[$input.name] == $value.value}checked="checked"{/if}
                                            {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
                                        />
                                        <label
                                            {if $value.value == 1}
                                                for="{$input.name|escape:'htmlall':'UTF-8'}_on"
                                            {else}
                                                for="{$input.name|escape:'htmlall':'UTF-8'}_off"
                                            {/if}
                                        >
                                            {if $value.value == 1}
                                                {l s='Yes' mod='lgcookieslaw'}
                                            {else}
                                                {l s='No' mod='lgcookieslaw'}
                                            {/if}
                                        </label>
                                        {/foreach}
                                        <a class="slide-button btn"></a>
                                    </span>
                                {elseif $input.type == 'textarea'}
                                    {assign var=use_textarea_autosize value=true}
                                    {if isset($input.lang) AND $input.lang}
                                    {foreach $languages as $language}
                                    {if $languages|count > 1}
                                    <div class="form-group translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}"  {if $language.id_lang != $defaultFormLanguage}style="display:none;"{/if}>

                                        <div class="col-lg-9">
                                    {/if}
                                            <textarea name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte {if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{else}textarea-autosize{/if}{/if}" >{$fields_value[$input.name][$language.id_lang]|escape:'htmlall':'UTF-8'}</textarea>
                                    {if $languages|count > 1}   
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                                {$language.iso_code|escape:'htmlall':'UTF-8'}
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                {foreach from=$languages item=language}
                                                <li>
                                                    <a href="javascript:hideOtherLanguage({$language.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$language.name|escape:'htmlall':'UTF-8'}</a>
                                                </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                    </div>
                                    {/if}
                                    {/foreach}

                                    {else}
                                        <textarea name="{$input.name|escape:'htmlall':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'quotes':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'quotes':'UTF-8'}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte {if isset($input.class)}{$input.class|escape:'quotes':'UTF-8'}{/if}{else}textarea-autosize{/if}">{$fields_value[$input.name]|escape:'quotes':'UTF-8'}</textarea>
                                    {/if}

                                {elseif $input.type == 'checkbox'}
                                    {if isset($input.expand)}
                                        <a class="btn btn-default show_checkbox{if strtolower($input.expand.default) == 'hide'} hidden {/if}" href="#">
                                            <i class="icon-{$input.expand.show.icon|escape:'htmlall':'UTF-8'}"></i>
                                            {$input.expand.show.text|escape:'htmlall':'UTF-8'}
                                            {if isset($input.expand.print_total) && $input.expand.print_total > 0}
                                                <span class="badge">{$input.expand.print_total|escape:'htmlall':'UTF-8'}</span>
                                            {/if}
                                        </a>
                                        <a class="btn btn-default hide_checkbox{if strtolower($input.expand.default) == 'show'} hidden {/if}" href="#">
                                            <i class="icon-{$input.expand.hide.icon|escape:'htmlall':'UTF-8'}"></i>
                                            {$input.expand.hide.text|escape:'htmlall':'UTF-8'}
                                            {if isset($input.expand.print_total) && $input.expand.print_total > 0}
                                                <span class="badge">{$input.expand.print_total|escape:'htmlall':'UTF-8'}</span>
                                            {/if}
                                        </a>
                                    {/if}
                                    {foreach $input.values.query as $value}
                                        {assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
                                        <div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden {/if}">
                                            <label for="{$id_checkbox|escape:'htmlall':'UTF-8'}">
                                                <input type="checkbox"
                                                    name="{$id_checkbox|escape:'htmlall':'UTF-8'}"
                                                    id="{$id_checkbox|escape:'htmlall':'UTF-8'}"
                                                    class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                                                    {if isset($value.val)}value="{$value.val|escape:'htmlall':'UTF-8'}"{/if}
                                                    {if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]}checked="checked"{/if} />
                                                {$value[$input.values.name]|escape:'htmlall':'UTF-8'}
                                            </label>
                                        </div>
                                    {/foreach}
                                {elseif $input.type == 'change-password'}
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button type="button" id="{$input.name|escape:'htmlall':'UTF-8'}-btn-change" class="btn btn-default">
                                                <i class="icon-lock"></i>
                                                {l s='Change password...' mod='lgcookieslaw'}
                                            </button>
                                            <div id="{$input.name|escape:'htmlall':'UTF-8'}-change-container" class="form-password-change well hide">
                                                <div class="form-group">
                                                    <label for="old_passwd" class="control-label col-lg-2 required">
                                                        {l s='Current password' mod='lgcookieslaw'}
                                                    </label>
                                                    <div class="col-lg-10">
                                                        <div class="input-group fixed-width-lg">
                                                            <span class="input-group-addon">
                                                                <i class="icon-unlock"></i>
                                                            </span>
                                                            <input type="password" id="old_passwd" name="old_passwd" class="form-control" value="" required="required" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label for="{$input.name|escape:'htmlall':'UTF-8'}" class="required control-label col-lg-2">
                                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="Minimum of 8 characters.">                     
                                                            {l s='New password' mod='lgcookieslaw'}
                                                        </span>
                                                    </label>
                                                    <div class="col-lg-9">
                                                        <div class="input-group fixed-width-lg">
                                                            <span class="input-group-addon">
                                                                <i class="icon-key"></i>
                                                            </span>
                                                            <input type="password" id="{$input.name|escape:'htmlall':'UTF-8'}" name="{$input.name|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}" value="" required="required" autocomplete="off"/>
                                                        </div>
                                                        <span id="{$input.name|escape:'htmlall':'UTF-8'}-output"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="{$input.name|escape:'htmlall':'UTF-8'}2" class="required control-label col-lg-2">
                                                        {l s='Confirm password' mod='lgcookieslaw'}
                                                    </label>
                                                    <div class="col-lg-4">
                                                        <div class="input-group fixed-width-lg">
                                                            <span class="input-group-addon">
                                                                <i class="icon-key"></i>
                                                            </span>
                                                            <input type="password" id="{$input.name|escape:'htmlall':'UTF-8'}2" name="{$input.name|escape:'htmlall':'UTF-8'}2" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}" value="" autocomplete="off"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-lg-10 col-lg-offset-2">
                                                        <input type="text" class="form-control fixed-width-md pull-left" id="{$input.name|escape:'htmlall':'UTF-8'}-generate-field" disabled="disabled">
                                                        <button type="button" id="{$input.name|escape:'htmlall':'UTF-8'}-generate-btn" class="btn btn-default">
                                                            <i class="icon-random"></i>
                                                            {l s='Generate password' mod='lgcookieslaw'}
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-lg-10 col-lg-offset-2">
                                                        <p class="checkbox">
                                                            <label for="{$input.name|escape:'htmlall':'UTF-8'}-checkbox-mail">
                                                                <input name="passwd_send_email" id="{$input.name|escape:'htmlall':'UTF-8'}-checkbox-mail" type="checkbox" checked="checked">
                                                                {l s='Send me this new password by Email' mod='lgcookieslaw'}
                                                            </label>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <button type="button" id="{$input.name|escape:'htmlall':'UTF-8'}-cancel-btn" class="btn btn-default">
                                                            <i class="icon-remove"></i>
                                                            {l s='Cancel' mod='lgcookieslaw'}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $(function(){
                                            var $oldPwd = $('#old_passwd');
                                            var $passwordField = $('#{$input.name|escape:'htmlall':'UTF-8'}');
                                            var $output = $('#{$input.name|escape:'htmlall':'UTF-8'}-output');
                                            var $generateBtn = $('#{$input.name|escape:'htmlall':'UTF-8'}-generate-btn');
                                            var $generateField = $('#{$input.name|escape:'htmlall':'UTF-8'}-generate-field');
                                            var $cancelBtn = $('#{$input.name|escape:'htmlall':'UTF-8'}-cancel-btn');
                                            
                                            var feedback = [
                                                { badge: 'text-danger', text: '{l s='Invalid' js=1 mod='lgcookieslaw'}' },
                                                { badge: 'text-warning', text: '{l s='Okay' js=1 mod='lgcookieslaw'}' },
                                                { badge: 'text-success', text: '{l s='Good' js=1 mod='lgcookieslaw'}' },
                                                { badge: 'text-success', text: '{l s='Fabulous' js=1 mod='lgcookieslaw'}' }
                                            ];
                                            $.passy.requirements.length.min = 8;
                                            $.passy.requirements.characters = 'DIGIT';
                                            $passwordField.passy(function(strength, valid) {
                                                $output.text(feedback[strength].text);
                                                $output.removeClass('text-danger').removeClass('text-warning').removeClass('text-success');
                                                $output.addClass(feedback[strength].badge);
                                                if (valid){
                                                    $output.show();
                                                }
                                                else {
                                                    $output.hide();
                                                }
                                            });
                                            var $container = $('#{$input.name|escape:'htmlall':'UTF-8'}-change-container');
                                            var $changeBtn = $('#{$input.name|escape:'htmlall':'UTF-8'}-btn-change');
                                            var $confirmPwd = $('#{$input.name|escape:'htmlall':'UTF-8'}2');

                                            $changeBtn.on('click',function(){
                                                $container.removeClass('hide');
                                                $changeBtn.addClass('hide');
                                            });
                                            $generateBtn.click(function() {
                                                $generateField.passy( 'generate', 8 );
                                                var generatedPassword = $generateField.val();
                                                $passwordField.val(generatedPassword);
                                                $confirmPwd.val(generatedPassword);
                                            });
                                            $cancelBtn.on('click',function() {
                                                $container.find("input").val("");
                                                $container.addClass('hide');
                                                $changeBtn.removeClass('hide');
                                            });

                                            $.validator.addMethod('password_same', function(value, element) {
                                                return $passwordField.val() == $confirmPwd.val();
                                            }, '{l s='Invalid password confirmation' js=1 mod='lgcookieslaw'}');

                                            $('#employee_form').validate({
                                                rules: {
                                                    "email": {
                                                        email: true
                                                    },
                                                    "{$input.name|escape:'htmlall':'UTF-8'}" : {
                                                        minlength: 8
                                                    },
                                                    "{$input.name|escape:'htmlall':'UTF-8'}2": {
                                                        password_same: true
                                                    },
                                                    "old_passwd" : {},
                                                },
                                                // override jquery validate plugin defaults for bootstrap 3
                                                highlight: function(element) {
                                                    $(element).closest('.form-group').addClass('has-error');
                                                },
                                                unhighlight: function(element) {
                                                    $(element).closest('.form-group').removeClass('has-error');
                                                },
                                                errorElement: 'span',
                                                errorClass: 'help-block',
                                                errorPlacement: function(error, element) {
                                                    if(element.parent('.input-group').length) {
                                                        error.insertAfter(element.parent());
                                                    } else {
                                                        error.insertAfter(element);
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                {elseif $input.type == 'password'}
                                    <div class="input-group fixed-width-lg">
                                        <span class="input-group-addon">
                                            <i class="icon-key"></i>
                                        </span>
                                        <input type="password"
                                            id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                            name="{$input.name|escape:'htmlall':'UTF-8'}"
                                            class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"
                                            value=""
                                            {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off"{/if}
                                            {if isset($input.required) && $input.required } required="required" {/if} />
                                    </div>

                                {elseif $input.type == 'birthday'}
                                <div class="form-group">
                                    {foreach $input.options as $key => $select}
                                    <div class="col-lg-2">
                                        <select name="{$key|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}" class="fixed-width-lg">
                                            <option value="">-</option>
                                            {if $key == 'months'}
                                                {*
                                                    This comment is useful to the translator tools /!\ do not remove them
                                                    {l s='January' mod='lgcookieslaw'}
                                                    {l s='February' mod='lgcookieslaw'}
                                                    {l s='March' mod='lgcookieslaw'}
                                                    {l s='April' mod='lgcookieslaw'}
                                                    {l s='May' mod='lgcookieslaw'}
                                                    {l s='June' mod='lgcookieslaw'}
                                                    {l s='July' mod='lgcookieslaw'}
                                                    {l s='August' mod='lgcookieslaw'}
                                                    {l s='September' mod='lgcookieslaw'}
                                                    {l s='October' mod='lgcookieslaw'}
                                                    {l s='November' mod='lgcookieslaw'}
                                                    {l s='December' mod='lgcookieslaw'}
                                                *}
                                                {foreach $select as $k => $v}
                                                    <option value="{$k|escape:'htmlall':'UTF-8'}" {if $k == $fields_value[$key]}selected="selected"{/if}>{l s=$v mod='lgcookieslaw'}</option>
                                                {/foreach}
                                            {else}
                                                {foreach $select as $v}
                                                    <option value="{$v|escape:'htmlall':'UTF-8'}" {if $v == $fields_value[$key]}selected="selected"{/if}>{$v|escape:'htmlall':'UTF-8'}</option>
                                                {/foreach}
                                            {/if}
                                        </select>
                                    </div>
                                    {/foreach}
                                </div>
                                {elseif $input.type == 'group'}
                                    {assign var=groups value=$input.values}
                                    {include file='helpers/form/form_group.tpl'}
                                {elseif $input.type == 'shop'}
                                    {$input.html|escape:'htmlall':'UTF-8'}
                                {elseif $input.type == 'categories'}
                                    {$categories_tree|escape:'htmlall':'UTF-8'}
                                {elseif $input.type == 'file'}
                                    {$input.file|escape:'htmlall':'UTF-8'}
                                {elseif $input.type == 'categories_select'}
                                    {$input.category_tree|escape:'htmlall':'UTF-8'}
                                {elseif $input.type == 'asso_shop' && isset($asso_shop) && $asso_shop}
                                    {$asso_shop|escape:'htmlall':'UTF-8'}
                                {elseif $input.type == 'banner_type'}
                                    {foreach $input.images as $type => $image}
                                        <img src="{$image|escape:'html':'UTF-8'}" class="lgcookieslaw_banner_type lgcookieslaw_banner_type_{$type|intval}" style="border: 1px solid #d3d8db;{if $input.selected != $type};display:none{/if}">
                                    {/foreach}

                                    <script type="text/javascript">
                                        $('#PS_LGCOOKIES_POSITION').change(function() {
                                            $('.lgcookieslaw_banner_type').hide();
                                            $('.lgcookieslaw_banner_type_' + $(this).val()).show();
                                        });
                                    </script>
                                {elseif $input.type == 'color'}
                                <div class="form-group"  {if isset($input.tab)}data-tab-id="{$input.tab|escape:'html':'UTF-8'}"{/if}>
                                    <div class="col-lg-2">
                                        <div class="row">
                                            <div class="input-group">
                                                <input type="color"
                                                data-hex="true"
                                                {if isset($input.class)}class="{$input.class|escape:'quotes':'UTF-8'}"
                                                {else}class="color mColorPickerInput"{/if}
                                                name="{$input.name|escape:'quotes':'UTF-8'}"
                                                value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {elseif $input.type == 'date'}
                                    <div class="row">
                                        <div class="input-group col-lg-4">
                                            <input
                                                id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                                type="text"
                                                data-hex="true"
                                                {if isset($input.class)}class="{$input.class|escape:'htmlall':'UTF-8'}"
                                                {else}class="datepicker"{/if}
                                                name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
                                            <span class="input-group-addon">
                                                <i class="icon-calendar-empty"></i>
                                            </span>
                                        </div>
                                    </div>
                                {elseif $input.type == 'ip'}
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <input type="text"
                                                id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                                {if isset($input.class)}class="{$input.class|escape:'htmlall':'UTF-8'}"{/if}
                                                name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
                                        </div>
                                        <div class="col-lg-2">
                                            <button class="button btn btn-default" onclick="javascript:addIP('{$input.name|escape:'javascript':'UTF-8'}');return false;"">{l s='Add IP' mod='lgcookieslaw'}</button>
                                        </div>
                                    </div>
                                {elseif $input.type == 'datetime'}
                                    <div class="row">
                                        <div class="input-group col-lg-4">
                                            <input
                                                id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}"
                                                type="text"
                                                data-hex="true"
                                                {if isset($input.class)}class="{$input.class|escape:'htmlall':'UTF-8'}"
                                                {else}class="datetimepicker"{/if}
                                                name="{$input.name|escape:'htmlall':'UTF-8'}"
                                                value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" />
                                            <span class="input-group-addon">
                                                <i class="icon-calendar-empty"></i>
                                            </span>
                                        </div>
                                    </div>
                                {elseif $input.type == 'free'}
                                    {$fields_value[$input.name]|escape:'quotes':'UTF-8'}
                                {elseif $input.type == 'html'}
                                    {if isset($input.html_content)}
                                        {$input.html_content|escape:'quotes':'UTF-8'}
                                    {else}
                                        {$input.name|escape:'htmlall':'UTF-8'}
                                    {/if}
                                {/if}
                                {/block}{* end block input *}
                                {block name="description"}
                                    {if isset($input.desc) && !empty($input.desc)}
                                        <p class="help-block">
                                            {if is_array($input.desc)}
                                                {foreach $input.desc as $p}
                                                    {if is_array($p)}
                                                        <span id="{$p.id|escape:'htmlall':'UTF-8'}">{$p.text|escape:'quotes':'UTF-8'}</span><br />
                                                    {else}
                                                        {$p|escape:'quotes':'UTF-8'}<br />
                                                    {/if}
                                                {/foreach}
                                            {else}
                                                {$input.desc|escape:'quotes':'UTF-8'}
                                            {/if}
                                        </p>
                                    {/if}
                                {/block}
                                </div>
                            {/block}{* end block field *}
                        {/if}
                        </div>
                        {/block}
                    {/foreach}
                    {hook h='displayAdminForm' fieldset=$f}
                    {if isset($name_controller)}
                        {capture name=hookName assign=hookName}display{$name_controller|escape:'htmlall':'UTF-8'|ucfirst}Form{/capture}
                        {hook h=$hookName fieldset=$f}
                    {elseif isset($smarty.get.controller)}
                        {capture name=hookName assign=hookName}display{$smarty.get.controller|escape:'htmlall':'UTF-8'|ucfirst|htmlentities}Form{/capture}
                        {hook h=$hookName fieldset=$f}
                    {/if}
                </div><!-- /.form-wrapper -->
                {elseif $key == 'desc'}
                    <div class="alert alert-info col-lg-offset-3">
                        {if is_array($field)}
                            {foreach $field as $k => $p}
                                {if is_array($p)}
                                    <span{if isset($p.id)} id="{$p.id|escape:'htmlall':'UTF-8'}"{/if}>{$p.text|escape:'htmlall':'UTF-8'}</span><br />
                                {else}
                                    {$p|escape:'htmlall':'UTF-8'}
                                    {if isset($field[$k+1])}<br />{/if}
                                {/if}
                            {/foreach}
                        {else}
                            {$field|escape:'htmlall':'UTF-8'}
                        {/if}
                    </div>
                {/if}
                {block name="other_input"}{/block}
            {/foreach}
            {block name="footer"}
                {if isset($fieldset['form']['submit']) || isset($fieldset['form']['buttons'])}
                    <div class="panel-footer">
                        {if isset($fieldset['form']['submit']) && !empty($fieldset['form']['submit'])}
                        <button
                            type="submit"
                            value="1"
                            id="{if isset($fieldset['form']['submit']['id'])}{$fieldset['form']['submit']['id']|escape:'htmlall':'UTF-8'}{else}{$table|escape:'htmlall':'UTF-8'}_form_submit_btn{/if}"
                            name="{if isset($fieldset['form']['submit']['name'])}{$fieldset['form']['submit']['name']|escape:'htmlall':'UTF-8'}{else}{$submit_action|escape:'htmlall':'UTF-8'}{/if}{if isset($fieldset['form']['submit']['stay']) && $fieldset['form']['submit']['stay']}AndStay{/if}"
                            class="{if isset($fieldset['form']['submit']['class'])}{$fieldset['form']['submit']['class']|escape:'htmlall':'UTF-8'}{else}btn btn-default pull-right{/if}"
                            >
                            <i class="{if isset($fieldset['form']['submit']['icon'])}{$fieldset['form']['submit']['icon']|escape:'htmlall':'UTF-8'}{else}process-icon-save{/if}"></i> {$fieldset['form']['submit']['title']|escape:'htmlall':'UTF-8'}
                        </button>
                        {/if}
                        {if isset($show_cancel_button) && $show_cancel_button}
                        <a href="{$back_url|escape:'htmlall':'UTF-8'}" class="btn btn-default" onclick="window.history.back()">
                            <i class="process-icon-cancel"></i> {l s='Cancel' mod='lgcookieslaw'}
                        </a>
                        {/if}
                        {if isset($fieldset['form']['reset'])}
                        <button
                            type="reset"
                            id="{if isset($fieldset['form']['reset']['id'])}{$fieldset['form']['reset']['id']|escape:'htmlall':'UTF-8'}{else}{$table|escape:'htmlall':'UTF-8'}_form_reset_btn{/if}"
                            class="{if isset($fieldset['form']['reset']['class'])}{$fieldset['form']['reset']['class']|escape:'htmlall':'UTF-8'}{else}btn btn-default{/if}"
                            >
                            {if isset($fieldset['form']['reset']['icon'])}<i class="{$fieldset['form']['reset']['icon']|escape:'htmlall':'UTF-8'}"></i> {/if} {$fieldset['form']['reset']['title']|escape:'htmlall':'UTF-8'}
                        </button>
                        {/if}
                        {if isset($fieldset['form']['buttons'])}
                        {foreach from=$fieldset['form']['buttons'] item=btn key=k}
                            {if isset($btn.href) && trim($btn.href) != ''}
                                <a href="{$btn.href|escape:'htmlall':'UTF-8'}" {if isset($btn['id'])}id="{$btn['id']|escape:'htmlall':'UTF-8'}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']|escape:'htmlall':'UTF-8'}{/if}" {if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'htmlall':'UTF-8'}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']|escape:'htmlall':'UTF-8'}" ></i> {/if}{$btn.title|escape:'htmlall':'UTF-8'}</a>
                            {else}
                                <button type="{if isset($btn['type'])}{$btn['type']|escape:'htmlall':'UTF-8'}{else}button{/if}" {if isset($btn['id'])}id="{$btn['id']|escape:'htmlall':'UTF-8'}"{/if} class="btn btn-default{if isset($btn['class'])} {$btn['class']|escape:'htmlall':'UTF-8'}{/if}" name="{if isset($btn['name'])}{$btn['name']|escape:'htmlall':'UTF-8'}{else}submitOptions{$table|escape:'htmlall':'UTF-8'}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'htmlall':'UTF-8'}"{/if}>{if isset($btn['icon'])}<i class="{$btn['icon']|escape:'htmlall':'UTF-8'}" ></i> {/if}{$btn.title|escape:'htmlall':'UTF-8'}</button>
                            {/if}
                        {/foreach}
                        {/if}
                    </div>
                {/if}
            {/block}
        </div>
        {/block}
        {block name="other_fieldsets"}{/block}
    {/foreach}
</form>
{/block}
{block name="after"}{/block}

{if isset($tinymce) && $tinymce}
<script type="text/javascript">
    var iso = '{$iso|escape:'htmlall':'UTF-8'|addslashes}';
    var pathCSS = '{$smarty.const._THEME_CSS_DIR_|escape:'htmlall':'UTF-8'|addslashes}';
    var ad = '{$ad|escape:'htmlall':'UTF-8'|addslashes}';

    $(document).ready(function(){
        {block name="autoload_tinyMCE"}
            tinySetup({
                editor_selector :"autoload_rte"
            });
        {/block}
    });
</script>
{/if}

{if isset($color) && $color}
    <script type="text/javascript">
        $.fn.mColorPicker.defaults.imageFolder = baseDir + 'img/admin/';
    </script>
{/if}

{if $firstCall}
    <script type="text/javascript">
        var module_dir = '{$smarty.const._MODULE_DIR_|escape:'htmlall':'UTF-8'}';
        var id_language = {$defaultFormLanguage|intval};
        var languages = new Array();
        var vat_number = {if $vat_number}1{else}0{/if};
        // Multilang field setup must happen before document is ready so that calls to displayFlags() to avoid
        // precedence conflicts with other document.ready() blocks
        {foreach $languages as $k => $language}
            languages[{$k|escape:'htmlall':'UTF-8'}] = {
                id_lang: {$language.id_lang|escape:'htmlall':'UTF-8'},
                iso_code: '{$language.iso_code|escape:'htmlall':'UTF-8'}',
                name: '{$language.name|escape:'htmlall':'UTF-8'}',
                is_default: '{$language.is_default|escape:'htmlall':'UTF-8'}'
            };
        {/foreach}
        // we need allowEmployeeFormLang var in ajax request
        allowEmployeeFormLang = {$allowEmployeeFormLang|intval};
        displayFlags(languages, id_language, allowEmployeeFormLang);

        $(document).ready(function() {

            $(".show_checkbox").click(function () {
                $(this).addClass('hidden')
                $(this).siblings('.checkbox').removeClass('hidden');
                $(this).siblings('.hide_checkbox').removeClass('hidden');
                return false;
            });
            $(".hide_checkbox").click(function () {
                $(this).addClass('hidden')
                $(this).siblings('.checkbox').addClass('hidden');
                $(this).siblings('.show_checkbox').removeClass('hidden');
                return false;
            });

            {if isset($fields_value.id_state)}
                if ($('#id_country') && $('#id_state'))
                {
                    ajaxStates({$fields_value.id_state|escape:'htmlall':'UTF-8'});
                    $('#id_country').change(function() {
                        ajaxStates();
                    });
                }
            {/if}

            if ($(".datepicker").length > 0)
                $(".datepicker").datepicker({
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd'
                });

            if ($(".datetimepicker").length > 0)
            $('.datetimepicker').datetimepicker({
                prevText: '',
                nextText: '',
                dateFormat: 'yy-mm-dd',
                // Define a custom regional settings in order to use PrestaShop translation tools
                currentText: '{l s='Now' mod='lgcookieslaw'}',
                closeText: '{l s='Done' mod='lgcookieslaw'}',
                ampm: false,
                amNames: ['AM', 'A'],
                pmNames: ['PM', 'P'],
                timeFormat: 'hh:mm:ss tt',
                timeSuffix: '',
                timeOnlyTitle: '{l s='Choose Time' js=1 mod='lgcookieslaw'}',
                timeText: '{l s='Time' js=1 mod='lgcookieslaw'}',
                hourText: '{l s='Hour' js=1 mod='lgcookieslaw'}',
                minuteText: '{l s='Minute' js=1 mod='lgcookieslaw'}',
            });
            {*{if isset($use_textarea_autosize)}*}
            {*$(".textarea-autosize").autosize();*}
            {*{/if}*}
        });
    state_token = '{getAdminToken tab='AdminStates'}';
    {block name="script"}{/block}
    </script>
{/if}
