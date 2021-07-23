{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}

<div id="lgcookieslaw_banner" class="lgcookieslaw-banner{if $lgcookieslaw_position == 3} lgcookieslaw-message-floating{/if}{if isset($lgcookieslaw_show_reject_all_button) && $lgcookieslaw_show_reject_all_button} lgcookieslaw-reject-all-button-enabled{/if}">
    <div class="container">
        <div class="lgcookieslaw-message">
            {$cookie_message nofilter} {* HTML CONTENT *}

            <div class="lgcookieslaw-link-container">
                <a id="lgcookieslaw_info" class="lgcookieslaw-info lgcookieslaw-link-button" {if isset($cms_target) && $cms_target} target="_blank" {/if} href="{$cms_link|escape:'quotes':'UTF-8'}" >
                    {stripslashes($button2|escape:'quotes':'UTF-8')}
                </a>

                <a id="lgcookieslaw_customize_cookies" class="lgcookieslaw-customize-cookies lgcookieslaw-link-button" onclick="customizeCookies()">
                    {l s='Customize Cookies' mod='lgcookieslaw'}
                </a>
            </div>
        </div>
        <div class="lgcookieslaw-button-container">
            {if isset($lgcookieslaw_show_reject_all_button) && $lgcookieslaw_show_reject_all_button}
                <button id="lgcookieslaw_reject_all" class="lgcookieslaw-btn lgcookieslaw-reject-all lgcookieslaw-link-button" onclick="closeinfo(true, 2)">
                    {l s='Reject All' mod='lgcookieslaw'}
                </button>
            {/if}

            <button id="lgcookieslaw_accept" class="lgcookieslaw-btn lgcookieslaw-accept lggoogleanalytics-accept" onclick="closeinfo(true, 1)">{stripslashes($button1|escape:'quotes':'UTF-8')}</button>
        </div>
    </div>
</div>

<div id="lgcookieslaw_modal" class="lgcookieslaw-modal">
    <div class="lgcookieslaw-modal-body">
        <h2>{l s='Cookies configuration' mod='lgcookieslaw'}</h2>
        <div class="lgcookieslaw-section">
            <div class="lgcookieslaw-section-name">
                {l s='Customization' mod='lgcookieslaw'}
            </div>
            <div class="lgcookieslaw-section-checkbox">
                <label class="lgcookieslaw-switch">
                    <div class="lgcookieslaw-slider-option-left">{l s='No' mod='lgcookieslaw'}</div>
                    <input type="checkbox" id="lgcookieslaw-customization-enabled" {if $third_paries}checked="checked"{/if}>
                    <span class="lgcookieslaw-slider{if $third_paries} lgcookieslaw-slider-checked{/if}"></span>
                    <div class="lgcookieslaw-slider-option-right">{l s='Yes' mod='lgcookieslaw'}</div>
                </label>
            </div>
            <div class="lgcookieslaw-section-description">
                {$cookie_additional nofilter} {* HTML CONTENT *}
            </div>
        </div>
        <div class="lgcookieslaw-section">
            <div class="lgcookieslaw-section-name">
                {l s='Functional (required)' mod='lgcookieslaw'}
            </div>
            <div class="lgcookieslaw-section-checkbox">
                <label class="lgcookieslaw-switch">
                    <div class="lgcookieslaw-slider-option-left">{l s='No' mod='lgcookieslaw'}</div>
                    <input type="checkbox" checked="checked" disabled="disabled">
                    <span class="lgcookieslaw-slider lgcookieslaw-slider-checked"></span>
                    <div class="lgcookieslaw-slider-option-right">{l s='Yes' mod='lgcookieslaw'}</div>
                </label>
            </div>
            <div class="lgcookieslaw-section-description">
                {$cookie_required nofilter} {* HTML CONTENT *}
            </div>
        </div>
    </div>
    <div class="lgcookieslaw-modal-footer">
        <div class="lgcookieslaw-modal-footer-left">
            <button id="lgcookieslaw_cancel" class="btn lgcookieslaw-cancel"> > {l s='Cancel' mod='lgcookieslaw'}</button>
        </div>
        <div class="lgcookieslaw-modal-footer-right">
            {if isset($lgcookieslaw_show_reject_all_button) && $lgcookieslaw_show_reject_all_button}
                <button id="lgcookieslaw_reject_all" class="btn lgcookieslaw-reject-all" onclick="closeinfo(true, 2)">{l s='Reject All' mod='lgcookieslaw'}</button>
            {/if}

            <button id="lgcookieslaw_save" class="btn lgcookieslaw-save" onclick="closeinfo(true)">{l s='Accept Selection' mod='lgcookieslaw'}</button>
            <button id="lgcookieslaw_accept_all" class="btn lgcookieslaw-accept-all lggoogleanalytics-accept" onclick="closeinfo(true, 1)">{l s='Accept All' mod='lgcookieslaw'}</button>
        </div>
    </div>
</div>

<div class="lgcookieslaw-overlay"></div>
