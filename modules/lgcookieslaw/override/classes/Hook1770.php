<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

class Hook extends HookCore
{
    /**
     * Returns a list of modules that are registered for a given hook, each following this schema:
     *
     * ```
     *     [
     *         'id_hook' => $hookId,
     *         'module' => $moduleName,
     *         'id_module' => $moduleId
     *     ]
     * ```
     *
     * If no hook name is given, it returns all the hook registrations, indexed by lower cased hook name.
     *
     * @param string|null $hookName Hook name (null to return all hooks)
     *
     * @return array[]|false returns an array of hook registrations,
     * or false if the provided hook name is not registered
     *
     * @throws PrestaShopDatabaseException
     *
     * @since 1.5.0
     */
    public static function getHookModuleExecList($hookName = null)
    {
        $allHookRegistrations = self::getAllHookRegistrationsLG(Context::getContext(), $hookName);

        // If no hook_name is given, return all registered hooks
        if (null === $hookName) {
            return $allHookRegistrations;
        }

        $normalizedHookName = strtolower($hookName);
        $modulesToInvoke = (isset($allHookRegistrations[$normalizedHookName])) ?
            $allHookRegistrations[$normalizedHookName] : [];

        // add modules that are registered to aliases of this hook
        $aliases = self::getHookAliasesForLG($hookName);
        if (!empty($aliases)) {
            $alreadyIncludedModuleIds = array_column($modulesToInvoke, 'id_module');
            foreach ($aliases as $alias) {
                $hookAlias = strtolower($alias);
                if (isset($allHookRegistrations[$hookAlias])) {
                    foreach ($allHookRegistrations[$hookAlias] as $registeredAlias) {
                        if (!in_array($registeredAlias['id_module'], $alreadyIncludedModuleIds)) {
                            $modulesToInvoke[] = $registeredAlias;
                        }
                    }
                }
            }
        }

        return !empty($modulesToInvoke) ? $modulesToInvoke : false;
    }

    /**
     * Returns all backward compatibility hook names for a given canonical hook name.
     *
     * @param string $canonicalHookName Canonical hook name
     *
     * @return string[] List of aliases
     *
     * @since 1.7.1.0
     */
    private static function getHookAliasesForLG(string $canonicalHookName): array
    {
        $cacheId = 'hook_aliases_' . $canonicalHookName;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $allAliases = self::getAllHookAliasesLG();

        $aliases = $allAliases[$canonicalHookName] ?? [];

        Cache::store($cacheId, $aliases);

        return $aliases;
    }

    /**
     * Retrieves all modules registered to any hook, indexed by hok name.
     *
     * Each registration looks like this:
     *
     * ```
     *     [
     *         'id_hook' => $hookId,
     *         'module' => $moduleName,
     *         'id_module' => $moduleId
     *     ]
     * ```
     *
     * @param Context $context
     * @param string|null $hookName Hook name (to be used when the hook registration is dynamic and context sensitive)
     *
     * @return array[][]
     *
     * @throws PrestaShopDatabaseException
     */
    private static function getAllHookRegistrationsLG(Context $context, ?string $hookName): array
    {
        $shop = $context->shop;
        $customer = $context->customer;

        $cache_id = self::MODULE_LIST_BY_HOOK_KEY
            . ($shop instanceof Shop && isset($shop->id) ? '_' . $shop->id : '')
            . ($customer instanceof Customer ? '_' . $customer->id : '');

        $useCache = (
            !in_array(
                $hookName,
                [
                    'displayPayment',
                    'displayPaymentEU',
                    'paymentOptions',
                    'displayBackOfficeHeader',
                    'displayAdminLogin',
                ]
            )
        );

        if ($useCache && Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }

        $groups = [];
        $use_groups = Group::isFeatureActive();
        $frontend = !$context->employee instanceof Employee;
        if ($frontend) {
            // Get groups list
            if ($use_groups) {
                if ($customer instanceof Customer && $customer->isLogged()) {
                    $groups = $customer->getGroups();
                } elseif ($customer instanceof Customer && $customer->isLogged(true)) {
                    $groups = [(int) Configuration::get('PS_GUEST_GROUP')];
                } else {
                    $groups = [(int) Configuration::get('PS_UNIDENTIFIED_GROUP')];
                }
            }
        }

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
        if (!in_array($hookName, ['displayBackOfficeHeader', 'displayAdminLogin'])) {
            $sql->join(
                Shop::addSqlAssociation(
                    'module',
                    'm',
                    true,
                    'module_shop.enable_device & ' . (int) Context::getContext()->getDevice()
                )
            );
            $sql->innerJoin('module_shop', 'ms', 'ms.`id_module` = m.`id_module`');
        }
        $sql->innerJoin('hook_module', 'hm', 'hm.`id_module` = m.`id_module`');
        $sql->innerJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
        if ($hookName !== 'paymentOptions') {
            $sql->where('h.`name` != "paymentOptions"');
        } elseif ($frontend) {
            // For payment modules, we check that they are available in the contextual country
            if (Validate::isLoadedObject($context->country)) {
                $sql->where(
                    '(
                        h.`name` IN ("displayPayment", "displayPaymentEU", "paymentOptions")
                        AND (
                            SELECT `id_country`
                            FROM `' . _DB_PREFIX_ . 'module_country` mc
                            WHERE mc.`id_module` = m.`id_module`
                            AND `id_country` = ' . (int) $context->country->id . '
                            AND `id_shop` = ' . (int) $shop->id . '
                            LIMIT 1
                        ) = ' . (int) $context->country->id . ')'
                );
            }
            if (Validate::isLoadedObject($context->currency)) {
                $sql->where(
                    '(
                        h.`name` IN ("displayPayment", "displayPaymentEU", "paymentOptions")
                        AND (
                            SELECT `id_currency`
                            FROM `' . _DB_PREFIX_ . 'module_currency` mcr
                            WHERE mcr.`id_module` = m.`id_module`
                            AND `id_currency` IN (' . (int) $context->currency->id . ', -1, -2)
                            LIMIT 1
                        ) IN (' . (int) $context->currency->id . ', -1, -2))'
                );
            }
            if (Validate::isLoadedObject($context->cart)) {
                $carrier = new Carrier($context->cart->id_carrier);
                if (Validate::isLoadedObject($carrier)) {
                    $sql->where(
                        '(
                            h.`name` IN ("displayPayment", "displayPaymentEU", "paymentOptions")
                            AND (
                                SELECT `id_reference`
                                FROM `' . _DB_PREFIX_ . 'module_carrier` mcar
                                WHERE mcar.`id_module` = m.`id_module`
                                AND `id_reference` = ' . (int) $carrier->id_reference . '
                                AND `id_shop` = ' . (int) $shop->id . '
                                LIMIT 1
                            ) = ' . (int) $carrier->id_reference . ')'
                    );
                }
            }
        }
        if (Validate::isLoadedObject($shop) && $hookName !== 'displayAdminLogin') {
            $sql->where('hm.`id_shop` = ' . (int) $shop->id);
        }

        if ($frontend) {
            if ($use_groups) {
                $sql->leftJoin('module_group', 'mg', 'mg.`id_module` = m.`id_module`');
                if (Validate::isLoadedObject($shop)) {
                    $sql->where(
                        'mg.id_shop = ' . ((int) $shop->id)
                        . (count($groups) ? ' AND  mg.`id_group` IN (' . implode(', ', $groups) . ')' : '')
                    );
                } elseif (count($groups)) {
                    $sql->where('mg.`id_group` IN (' . implode(', ', $groups) . ')');
                }
            }
        }

        $sql->groupBy('hm.id_hook, hm.id_module');
        $sql->orderBy('hm.`position`');

        $allHookRegistrations = [];
        if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            foreach ($result as $row) {
                $row['hook'] = strtolower($row['hook']);
                if (!isset($allHookRegistrations[$row['hook']])) {
                    $allHookRegistrations[$row['hook']] = [];
                }

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
                                $allHookRegistrations[$row['hook']][] = array(
                                    'id_hook' => $row['id_hook'],
                                    'module' => $row['module'],
                                    'id_module' => $row['id_module'],
                                );
                            }
                        } else {
                            $allHookRegistrations[$row['hook']][] = array(
                                'id_hook' => $row['id_hook'],
                                'module' => $row['module'],
                                'id_module' => $row['id_module'],
                            );
                        }
                    } else {
                        $allHookRegistrations[$row['hook']][] = array(
                            'id_hook' => $row['id_hook'],
                            'module' => $row['module'],
                            'id_module' => $row['id_module'],
                        );
                    }
                } else {
                    $allHookRegistrations[$row['hook']][] = array(
                        'id_hook' => $row['id_hook'],
                        'module' => $row['module'],
                        'id_module' => $row['id_module'],
                    );
                }
            }
        }

        if ($useCache) {
            Cache::store($cache_id, $allHookRegistrations);
            // @todo remove this in 1.6, we keep it in 1.5 for backward compatibility
            self::$_hook_modules_cache_exec = $allHookRegistrations;
        }

        return $allHookRegistrations;
    }

    /**
     * Get the list of hook aliases, indexed by hook name
     *
     * @since 1.7.1.0
     *
     * @return array<string, array<string>> Array of hookName => hookAliases[]
     */
    private static function getAllHookAliasesLG(): array
    {
        $cacheId = 'hook_aliases';
        if (!Cache::isStored($cacheId)) {
            $hookAliasList = Db::getInstance()->executeS('SELECT `name`, `alias` FROM `' . _DB_PREFIX_ . 'hook_alias`');
            $hookAliases = [];
            if ($hookAliasList) {
                foreach ($hookAliasList as $ha) {
                    $hookAliases[$ha['name']][] = $ha['alias'];
                }
            }
            Cache::store($cacheId, $hookAliases);

            return $hookAliases;
        }

        return Cache::retrieve($cacheId);
    }
}
