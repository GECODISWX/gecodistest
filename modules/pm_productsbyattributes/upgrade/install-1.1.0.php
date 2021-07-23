<?php
/**
 *
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license   Commercial
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

if (!defined('_PS_VERSION_')) {
    exit;
}
function upgrade_module_1_1_0($module)
{
    if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
        $module->registerHook('productSearchProvider');
        $module->registerHook('actionGetProductPropertiesAfter');
        $module->registerHook('filterProductSearch');
        $id_hook = Hook::getIdByName('productSearchProvider');
        $module->updatePosition($id_hook, 0, 1);
    }
    return true;
}
