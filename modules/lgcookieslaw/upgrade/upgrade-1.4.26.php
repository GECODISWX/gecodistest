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

function upgrade_module_1_4_26()
{
    Configuration::deleteByName('PS_LGCOOKIES_SETTING_BUTTON');
    Configuration::deleteByName('PS_LGCOOKIES_SHOW_CLOSE');
    Configuration::deleteByName('PS_LGCOOKIES_NAVIGATION_BTN');
    Configuration::deleteByName('PS_LGCOOKIES_NAVIGATION');
    Configuration::deleteByName('PS_LGCOOKIES_BTN2_FONT_COLOR');
    Configuration::deleteByName('PS_LGCOOKIES_BTN2_BG_COLOR');
    Configuration::deleteByName('PS_LGCOOKIES_SEL_TAB');

    Configuration::updateValue('PS_LGCOOKIES_POSITION', '3');
    Configuration::updateValue('PS_LGCOOKIES_SHOW_REJECT_ALL_BUTTON', '1');

    return true;
}
