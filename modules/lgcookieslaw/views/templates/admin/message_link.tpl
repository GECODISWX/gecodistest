{*
	*  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
	*
	* @author    Línea Gráfica E.C.E. S.L.
	* @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
	* @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
*}
<a {if isset($href)} href="{$href|escape:'quotes':'UTF-8'}"{/if}{if isset($title)} title="{$title|escape:'htmlall':'UTF-8'}"{/if}{if isset($target)} target="{$target|escape:'htmlall':'UTF-8'}"{/if}>
    {$message|escape:'htmlall':'UTF-8'}
</a>