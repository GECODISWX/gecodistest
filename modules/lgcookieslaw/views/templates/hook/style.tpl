{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}

{literal}
.lgcookieslaw-banner {
    background-color: rgba({/literal}{$bgcolor|escape:'html':'UTF-8'}{literal});
    color: {/literal}{$fontcolor|escape:'html':'UTF-8'}{literal} !important;
    -webkit-box-shadow: 0px 1px 5px 0px {/literal}{$shadowcolor|escape:'html':'UTF-8'}{literal};
    -moz-box-shadow:    0px 1px 5px 0px {/literal}{$shadowcolor|escape:'html':'UTF-8'}{literal};
    box-shadow:         0px 1px 5px 0px {/literal}{$shadowcolor|escape:'html':'UTF-8'}{literal};
    {/literal}
    {$position|escape:'htmlall':'UTF-8'};
    {literal}
}
#lgcookieslaw_banner .lgcookieslaw-message p {
    color: {/literal}{$fontcolor|escape:'html':'UTF-8'}{literal} !important;
}
#lgcookieslaw_banner .lgcookieslaw-message a {
    color: {/literal}{$fontcolor|escape:'html':'UTF-8'}{literal} !important;
    border-bottom: 1px solid {/literal}{$fontcolor|escape:'html':'UTF-8'}{literal};
}
.lgcookieslaw-modal-body {
    border-top: 4px solid {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal};
}
.lgcookieslaw-banner .lgcookieslaw-btn {
    border-color: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal} !important;
    background: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal} !important;
    color: {/literal}{$btn1_fontcolor|escape:'html':'UTF-8'}{literal} !important;
}

/* New module*/
.lgcookieslaw-section-description ul li:before {
    color: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal}!important;
}
input:checked + .lgcookieslaw-slider,
.lgcookieslaw-slider-checked {
    background-color: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal}!important;
}
input:focus + .lgcookieslaw-slider,
.lgcookieslaw-slider-checked {
    box-shadow: 0 0 1px {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal}!important;
}
.lgcookieslaw-accept-all {
    background: {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal}!important;
}
.lgcookieslaw-message-floating {
    border: 1px solid {/literal}{$btn1_bgcolor|escape:'html':'UTF-8'}{literal};
}
{/literal}
