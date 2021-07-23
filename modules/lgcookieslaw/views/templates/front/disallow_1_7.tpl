{*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 *}
{extends file='page.tpl'}
{block name='page_content'}
<script type="text/javascript">
    var lgcookieslaw_safe_cookies = [];
    {foreach $lgcookieslaw_safe_cookies as $cookie}
    lgcookieslaw_safe_cookies.push('{$cookie|escape:'htmlall':'UTF-8'}');
    {/foreach}

    var getCookies = function(){
        var pairs = document.cookie.split(";");
        var cookies = {};
        for (var i=0; i<pairs.length; i++){
            var pair = pairs[i].split("=");
            cookies[(pair[0]+'').trim()] = unescape(pair[1]);
        }
        return cookies;
    }

    var myCookies = getCookies();
    Object.keys(myCookies).map(function(key, index) {
        document.cookie = key + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        document.cookie = key + "=; domain=." + window.location.hostname + "; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        document.cookie = key + "=; domain=." + window.location.hostname.replace('www.',  '') + "; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    });
</script>
<div>
    {if $lgcookieslaw_token_ok}
        <h2>{l s='All your cookies except Prestashop session ones have been deleted' mod='lgcookieslaw'}</h2>
    {else}
        <h2>{l s='Bad request' mod='lgcookieslaw'}</h2>
    {/if}
</div>
{/block}
