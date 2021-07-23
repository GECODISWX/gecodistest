<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

function upgrade_module_1_4_27()
{
    Configuration::deleteByName('PS_LGCOOKIES_SHOW_REJECT_ALL_BUTTON');

    Configuration::updateValue('PS_LGCOOKIES_SHOW_REJECT_ALL_BTN', '1');
    Configuration::updateValue('PS_LGCOOKIES_CMS_SHOW_BANNER', '0');

    return true;
}
