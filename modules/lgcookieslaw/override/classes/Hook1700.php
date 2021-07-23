<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class Hook extends HookCore
{
    /**
     * Get list of modules we can execute per hook
     *
     * @since 1.5.0
     * @param string $hook_name Get list of modules for this hook if given
     * @return array
     */
    public static function getHookModuleExecList($hook_name = null)
    {
        $context = Context::getContext();
        $cache_id = 'hook_module_exec_list_'.(isset($context->shop->id) ?
                '_'.$context->shop->id : '').((isset($context->customer)) ? '_'.$context->customer->id :
                '');
        if (!Cache::isStored($cache_id)
            || $hook_name == 'displayPayment'
            || $hook_name == 'displayPaymentEU'
            || $hook_name == 'paymentOptions'
            || $hook_name == 'displayBackOfficeHeader'
        ) {
            $frontend = true;
            $groups = array();
            $use_groups = Group::isFeatureActive();
            if (isset($context->employee)) {
                $frontend = false;
            } else {
                // Get groups list
                if ($use_groups) {
                    if (isset($context->customer) && $context->customer->isLogged()) {
                        $groups = $context->customer->getGroups();
                    } elseif (isset($context->customer) && $context->customer->isLogged(true)) {
                        $groups = array((int)Configuration::get('PS_GUEST_GROUP'));
                    } else {
                        $groups = array((int)Configuration::get('PS_UNIDENTIFIED_GROUP'));
                    }
                }
            }

            // CARLOS: Agrego la condicion de que el modulo este activo, cuando se desactiva no debe hacer nada.
            //         Agregamos a la condición de que sea en el frontend para evitar que bloquee modulos en backend
            if (Module::isInstalled('lgcookieslaw') && Module::isEnabled('lgcookieslaw') && $frontend) {
                $cookieslaw = null;
                // CARLOS: Se realizan 160 consultas por segundo porque no se ha optimizado bien la forma de onsultar
                //         el modulo. La cambiamos para obtener en una consulta la lista de modulos a excluir y con un
                //         in_array comprobar si es de los que hay que excluir.

                // Obtenemos todos los modulos a excluir
                $cookieslaw = Db::getInstance()->executeS(
                    'SELECT id_module '.
                    'FROM '._DB_PREFIX_.'lgcookieslaw;'
                );

                // Intercambiamso claves por valor para luego consultar por la id del hook
                if (!is_null($cookieslaw) && !empty($cookieslaw)) {
                    $modules = array();
                    foreach ($cookieslaw as $module) {
                        $modules[] = $module['id_module'];
                    }
                    $cookieslaw = $modules;
                    unset($modules);
                }
            }

            // SQL Request
            $sql = new DbQuery();
            $sql->select('h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module');
            $sql->from('module', 'm');
            if ($hook_name != 'displayBackOfficeHeader') {
                $sql->join(
                    Shop::addSqlAssociation(
                        'module',
                        'm',
                        true,
                        'module_shop.enable_device & '.(int)Context::getContext()->getDevice()
                    )
                );
                $sql->innerJoin('module_shop', 'ms', 'ms.`id_module` = m.`id_module`');
            }
            $sql->innerJoin('hook_module', 'hm', 'hm.`id_module` = m.`id_module`');
            $sql->innerJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
            if ($hook_name != 'paymentOptions') {
                $sql->where('h.`name` != "paymentOptions"');
            } elseif ($frontend) { // For payment modules, we check that they are available in the contextual country
                if (Validate::isLoadedObject($context->country)) {
                    $sql->where(
                        '('.
                        '  ('.
                        '      h.`name` = "displayPayment" '.
                        '      OR h.`name` = "displayPaymentEU" '.
                        '      OR h.`name` = "paymentOptions"'.
                        '   )'.
                        '   AND ('.
                        '      SELECT `id_country` '.
                        '      FROM `'._DB_PREFIX_.'module_country` mc '.
                        '      WHERE mc.`id_module` = m.`id_module` '.
                        '      AND `id_country` = '.(int)$context->country->id.
                        '      AND `id_shop` = '.(int)$context->shop->id.
                        '      LIMIT 1'.
                        '   ) = '.(int)$context->country->id.
                        ')'
                    );
                }
                if (Validate::isLoadedObject($context->currency)) {
                    $sql->where(
                        '('.
                        '   ('.
                        '      h.`name` = "displayPayment" '.
                        '      OR h.`name` = "displayPaymentEU" '.
                        '      OR h.`name` = "paymentOptions"'.
                        '   ) '.
                        '   AND ('.
                        '      SELECT `id_currency` '.
                        '      FROM `'._DB_PREFIX_.'module_currency` mcr '.
                        '      WHERE mcr.`id_module` = m.`id_module` '.
                        '      AND `id_currency` IN ('.(int)$context->currency->id.', -1, -2) '.
                        '     LIMIT 1'.
                        '   ) IN ('.(int)$context->currency->id.', -1, -2)'.
                        ')'
                    );
                }
                if (Validate::isLoadedObject($context->cart)) {
                    $carrier = new Carrier($context->cart->id_carrier);
                    if (Validate::isLoadedObject($carrier)) {
                        $sql->where(
                            '('.
                            '   ('.
                            '      h.`name` = "displayPayment" '.
                            '      OR h.`name` = "displayPaymentEU" '.
                            '      OR h.`name` = "paymentOptions"'.
                            '   ) '.
                            '   AND ('.
                            '      SELECT `id_reference` '.
                            '      FROM `'._DB_PREFIX_.'module_carrier` mcar '.
                            '      WHERE mcar.`id_module` = m.`id_module` '.
                            '      AND `id_reference` = '.(int)$carrier->id_reference.
                            '      AND `id_shop` = '.(int)$context->shop->id.' '.
                            '      LIMIT 1'.
                            '   ) = '.(int)$carrier->id_reference.
                            ')'
                        );
                    }
                }
            }
            if (Validate::isLoadedObject($context->shop)) {
                $sql->where('hm.`id_shop` = '.(int)$context->shop->id);
            }

            if ($frontend) {
                if ($use_groups) {
                    $sql->leftJoin('module_group', 'mg', 'mg.`id_module` = m.`id_module`');
                    if (Validate::isLoadedObject($context->shop)) {
                        $sql->where(
                            'mg.id_shop = '
                            .(
                                (int)$context->shop->id).(count($groups) ?
                                    ' AND  mg.`id_group` IN ('.implode(', ', $groups).')' :
                                    ''
                            )
                        );
                    } elseif (count($groups)) {
                        $sql->where('mg.`id_group` IN ('.implode(', ', $groups).')');
                    }
                }
            }

            $sql->groupBy('hm.id_hook, hm.id_module');
            $sql->orderBy('hm.`position`');

            $list = array();
            if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                foreach ($result as $row) {
                    $row['hook'] = Tools::strtolower($row['hook']);
                    if (!isset($list[$row['hook']])) {
                        $list[$row['hook']] = array();
                    }

                    // CARLOS: Agrego la condicion de que el modulo este activo, cuando se desactiva no debe hacer nada
                    if (Module::isInstalled('lgcookieslaw') && Module::isEnabled('lgcookieslaw')) {
                        if (Tools::getValue('controller') == 'cms'
                            && Tools::getValue('id_cms', 0) == (int)Configuration::get('PS_LGCOOKIES_CMS')
                        ) {
                            $cms_page = true;
                        } else {
                            $cms_page = false;
                        }

                        if (!Configuration::get('PS_LGCOOKIES_TESTMODE') == 1 || $cms_page) {
                            if (!isset($_COOKIE[Configuration::get('PS_LGCOOKIES_NAME')]) ||
                                $cms_page ||
                                $_COOKIE[Configuration::get('PS_LGCOOKIES_NAME')] != 1 ||
                                strpos($_SERVER['REQUEST_URI'], 'disallow') !== false
                            ) {
                                if (!isset($cookieslaw)
                                    || is_null($cookieslaw)
                                    || empty($cookieslaw)
                                    || !in_array($row['id_module'], $cookieslaw)
                                ) {
                                    $list[$row['hook']][] = array(
                                        'id_hook' => $row['id_hook'],
                                        'module' => $row['module'],
                                        'id_module' => $row['id_module'],
                                    );
                                }
                            } else {
                                $list[$row['hook']][] = array(
                                    'id_hook' => $row['id_hook'],
                                    'module' => $row['module'],
                                    'id_module' => $row['id_module'],
                                );
                            }
                        } else {
                            $list[$row['hook']][] = array(
                                'id_hook' => $row['id_hook'],
                                'module' => $row['module'],
                                'id_module' => $row['id_module'],
                            );
                        }
                    } else {
                        $list[$row['hook']][] = array(
                            'id_hook' => $row['id_hook'],
                            'module' => $row['module'],
                            'id_module' => $row['id_module'],
                        );
                    }
                }
            }
            if ($hook_name != 'displayPayment'
                && $hook_name != 'displayPaymentEU'
                && $hook_name != 'paymentOptions'
                && $hook_name != 'displayBackOfficeHeader'
            ) {
                Cache::store($cache_id, $list);
                // @todo remove this in 1.6, we keep it in 1.5 for backward compatibility
                self::$_hook_modules_cache_exec = $list;
            }
        } else {
            $list = Cache::retrieve($cache_id);
        }

        // If hook_name is given, just get list of modules for this hook
        if ($hook_name) {
            $retro_hook_name = Tools::strtolower(Hook::getRetroHookName($hook_name));
            $hook_name = Tools::strtolower($hook_name);

            $return = array();
            $inserted_modules = array();
            if (isset($list[$hook_name])) {
                $return = $list[$hook_name];
            }
            foreach ($return as $module) {
                $inserted_modules[] = $module['id_module'];
            }
            if (isset($list[$retro_hook_name])) {
                foreach ($list[$retro_hook_name] as $retro_module_call) {
                    if (!in_array($retro_module_call['id_module'], $inserted_modules)) {
                        $return[] = $retro_module_call;
                    }
                }
            }

            return (count($return) > 0 ? $return : false);
        } else {
            return $list;
        }
    }
}
