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

if (!defined('_PS_VERSION_')) {
    exit;
}

class LGCookieslawDisallowModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            $this->setTemplate('module:lgcookieslaw/views/templates/front/disallow_1_7.tpl');
        } else {
            $this->setTemplate('disallow.tpl');
        }
        $ok = 0;
        if (md5(_COOKIE_KEY_.$this->module->name) == Tools::getValue('token', '')) {
            $ok = 1;
        }
        $cookies = array(
            $this->context->cookie->getName(),
            'PHPSESSID',
        );
        $this->context->smarty->assign(
            array(
                'lgcookieslaw_safe_cookies' => $cookies,
                'lgcookieslaw_token_ok' => $ok,
            )
        );
        //Tools::dieObject($this->context->cookie);
    }
}
