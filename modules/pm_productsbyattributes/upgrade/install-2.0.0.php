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
function upgrade_module_2_0_0($module)
{
    // Create cache table
    $module->createCacheTable();
    $module->registerHook('actionObjectAddAfter');
    $module->registerHook('actionObjectUpdateAfter');
    $module->registerHook('actionObjectDeleteBefore');
    return true;
}
