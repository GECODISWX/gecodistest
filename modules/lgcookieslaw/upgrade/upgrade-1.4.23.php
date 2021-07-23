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

function upgrade_module_1_4_23($module)
{
    Configuration::updateValue('PS_LGCOOKIES_THIRD_PARTIES', '0');
    Configuration::updateValue('PS_LGCOOKIES_HOOK', 'footer');
    $module->registerHook('footer');
    return true;
}
