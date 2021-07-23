<?php
/**
 * Products by Attributes
 *
 * @author    Presta-Module.com <support@presta-module.com> - http://www.presta-module.com
 * @copyright Presta-Module 2021 - http://www.presta-module.com
 * @license   Commercial
 * @version   2.0.5
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once _PS_ROOT_DIR_ . '/modules/pm_productsbyattributes/ProductsByAttributesCoreClass.php';
class pm_productsbyattributes extends ProductsByAttributesCoreClass
{
    // Default configuration
    protected $_defaultConfiguration = array(
        'selectedGroups' => array(),
        'changeProductName' => true,
        'hideCombinationsWithoutStock' => false,
        'hideCombinationsWithoutCover' => false,
        'hideColorSquares' => true,
        'addIDToAnchor' => true,
        'maintenanceMode' => false,
        'fullTree' => true,
        'enabledControllers' => array(
            'BestSales' => 1,
            'Category' => 1,
            'Manufacturer' => 1,
            'NewProducts' => 1,
            'PricesDrop' => 1,
            'Search' => 1,
            'Supplier' => 1,
        ),
        'nameSeparator' => ' - ',
        'autoReindex' => true,
        'sortCombinationBy' => '',
        'combinationToHighlight' => '',
    );
    public function __construct()
    {
        $this->need_instance = 0;
        $this->name = 'pm_productsbyattributes';
        $this->module_key = 'c44c197e40ce99724b7e5f6c631dacc4';
        $this->author = 'Presta-Module';
        $this->tab = 'front_office_features';
        $this->version = '2.0.5';
        $this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => '1.7.99.99');
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->ps_versions_compliancy['min'] = '1.7.1.0';
        }
        $this->bootstrap = true;
        $this->displayName = $this->l('Show Products by Attributes');
        $this->description = $this->l('Show as many products you have attributes into your category pages');
        parent::__construct();
    }
    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displayHeader')
            || (version_compare(_PS_VERSION_, '1.7.0.0', '<') && !$this->registerHook('actionProductListOverride'))
            || (version_compare(_PS_VERSION_, '1.7.0.0', '<') && !$this->registerHook('actionProductListModifier'))
            || (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && !$this->registerHook('productSearchProvider'))
            || (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && !$this->registerHook('actionGetProductPropertiesAfter'))
            || (version_compare(_PS_VERSION_, '1.7.0.0', '>=') && !$this->registerHook('filterProductSearch'))
            || !$this->registerHook('actionObjectAddAfter') || !$this->registerHook('actionObjectUpdateAfter') || !$this->registerHook('actionObjectDeleteBefore')
            || !$this->createCacheTable()
        ) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $id_hook = Hook::getIdByName('productSearchProvider');
            $this->updatePosition($id_hook, 0, 1);
        }
        $this->checkIfModuleIsUpdate(true, false, true);
        return true;
    }
    public function createCacheTable()
    {
        $res = (bool)Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pm_spa_cache`');
        $res &= (bool)Db::getInstance()->Execute('CREATE TABLE `'._DB_PREFIX_.'pm_spa_cache` (
            `id_product` int(11) UNSIGNED NOT NULL,
            `id_product_attribute` int(11) UNSIGNED NOT NULL,
            `id_shop` int(11) UNSIGNED NOT NULL,
            `id_attribute_list` text NOT NULL,
            PRIMARY KEY (`id_product`, `id_product_attribute`, `id_shop`) USING BTREE
            ) ENGINE = ' . _MYSQL_ENGINE_);
        $this->fillCacheTable();
        return $res;
    }
    protected function fillCacheTable($idProduct = null)
    {
        $res = true;
        if (!empty($idProduct)) {
            $res &= (bool)Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pm_spa_cache` WHERE `id_product`=' . (int)$idProduct . ' AND `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')');
        } else {
            $res &= (bool)Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pm_spa_cache` WHERE `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')');
        }
        foreach (Shop::getContextListShopID() as $idShop) {
            $shop = new Shop($idShop);
            $config = $this->getModuleConfiguration($shop);
            if (empty($config['selectedGroups']) || !is_array($config['selectedGroups'])) {
                continue;
            }
            $checkStock = (!empty($config['hideCombinationsWithoutStock']) || !Configuration::get('PS_DISP_UNAVAILABLE_ATTR', null, null, (int)$shop->id)) && Configuration::get('PS_STOCK_MANAGEMENT', null, null, (int)$shop->id);
            $checkProductCover = (bool)$config['hideCombinationsWithoutCover'];
            $joinStockTable = $checkStock;
            $combinationToHighlight = null;
            if (!empty($config['combinationToHighlight'])) {
                switch ($config['combinationToHighlight']) {
                    case 'quantity_asc':
                        $combinationToHighlight = 'stock.`quantity` ASC';
                        $joinStockTable = true;
                        break;
                    case 'quantity_desc':
                        $combinationToHighlight = 'stock.`quantity` DESC';
                        $joinStockTable = true;
                        break;
                    case 'price_asc':
                        $combinationToHighlight = 'pa_shop.`price` ASC';
                        break;
                    case 'price_desc':
                        $combinationToHighlight = 'pa_shop.`price` DESC';
                        break;
                    case 'unit_price_asc':
                        $combinationToHighlight = 'pa_shop.`unit_price_impact` ASC';
                        break;
                    case 'unit_price_desc':
                        $combinationToHighlight = 'pa_shop.`unit_price_impact` DESC';
                        break;
                }
            }
            $res &= (bool)Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'pm_spa_cache`
            (
                SELECT
                *
                FROM
                (
                SELECT
                    *
                FROM
                    (
                    SELECT
                        pa.`id_product`,
                        pa.`id_product_attribute`,
                        "' . (int)$shop->id . '" AS `id_shop`,
                        GROUP_CONCAT( pac.`id_attribute` ORDER BY pac.`id_attribute` ) AS `id_attribute_list`
                    FROM
                        `'._DB_PREFIX_.'product_attribute_combination` pac
                        JOIN `'._DB_PREFIX_.'product_attribute` pa ON (
                            pa.`id_product_attribute` = pac.`id_product_attribute`
                            AND pac.id_attribute IN (
                            SELECT
                                `id_attribute`
                            FROM
                                `'._DB_PREFIX_.'attribute`
                            WHERE
                                `id_attribute_group` IN (' . implode(',', array_map('intval', $config['selectedGroups'])) . ')
                            )
                        )
                        INNER JOIN '._DB_PREFIX_.'product_attribute_shop pa_shop ON (pa_shop.`id_product_attribute` = pa.`id_product_attribute` AND pa_shop.`id_shop`=' . (int)$shop->id . ')
                        JOIN `'._DB_PREFIX_.'product` p ON ( p.`id_product` = pa.`id_product` ' . (!empty($idProduct) ? ' AND p.`id_product`=' . (int)$idProduct : '') . ')
                        ' . ($joinStockTable ? Product::sqlStock('p', 'pa', false, $shop) : '') . '
                        WHERE 1
                        ' . ($checkProductCover ? ' AND pa.`id_product_attribute` IN (
                            SELECT pai.`id_product_attribute`
                            FROM `'._DB_PREFIX_.'image` i
                            JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (i.`id_image` = image_shop.`id_image` AND image_shop.`id_shop` = ' . (int)$shop->id . ')
                            JOIN `'._DB_PREFIX_.'product_attribute_image` pai  ON (pai.`id_image` = i.`id_image`)
                            GROUP BY pai.`id_product_attribute`
                        )' : '') . '
                        ' . ($checkStock ? ' AND stock.quantity > 0 ' : '') . '
                    GROUP BY
                        pa.`id_product`,
                        pa.`id_product_attribute`
                    ' . (!empty($combinationToHighlight) ? ' ORDER BY ' . pSQL($combinationToHighlight) : '') . '
                    ) AS `tmp_cartesian_table`
                GROUP BY
                    `id_product`,
                    `id_attribute_list`
                ) AS `tmp_cartesian_table_bis`
            )');
        }
        return $res;
    }
    public function getModuleConfiguration($shop = null)
    {//$d = debug_backtrace();var_dump($d[1]['function']);
        $config = parent::getModuleConfiguration($shop);
        if (!isset($config['selectedGroups'])) {
            $config['selectedGroups'] = array();
            $this->setModuleConfiguration($config);
        } elseif (!is_array($config['selectedGroups'])) {
            $config['selectedGroups'] = array($config['selectedGroups']);
            $this->setModuleConfiguration($config);
        }
        return $config;
    }
    public function processModuleUpdate($previousVersion, $currentVersion)
    {
        if (version_compare($previousVersion, '1.1.0', '<') && version_compare($this->version, '1.1.0', '>=')) {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                $this->registerHook('productSearchProvider');
                $this->registerHook('actionGetProductPropertiesAfter');
                $this->registerHook('filterProductSearch');
                $id_hook = Hook::getIdByName('productSearchProvider');
                $this->updatePosition($id_hook, 0, 1);
            }
        }
        if (version_compare($previousVersion, '2.0.0', '<') && version_compare($this->version, '2.0.0', '>=')) {
            $this->createCacheTable();
            $this->registerHook('actionObjectAddAfter');
            $this->registerHook('actionObjectUpdateAfter');
            $this->registerHook('actionObjectDeleteBefore');
        }
    }
    public function processContent()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
            $this->default_tab = 'configuration-1.5';
            $this->tabs = array();
            $this->tabs['configuration-1.5'] = array(
                'icon' => 'cogs',
                'label' => $this->l('Configuration'),
            );
        } else {
            $this->default_tab = 'configuration';
            $this->tabs = array();
            $this->tabs['configuration'] = array(
                'icon' => 'cogs',
                'label' => $this->l('Configuration'),
            );
            if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                $this->tabs['backward'] = array(
                    'icon' => 'rotate-left',
                    'label' => $this->l('Compatibility settings'),
                );
            }
            $this->tabs['maintenance'] = array(
                'icon' => 'code',
                'label' => $this->l('Maintenance'),
            );
        }
        $config = $this->getModuleConfiguration();
        $warnings = array();
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<') && Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY')) {
            $warnings[] = $this->l('We have detected that you show the "Add to cart" button if the product have somes attributes. Due to your theme, the "Add to cart" link may be not related to the splitted attribute of the product');
        }
        if ($config['maintenanceMode']) {
            $warnings[] = $this->l('Module is currently running in Maintenance Mode');
        }
        $this->context->smarty->assign('warnings', $warnings);
        $this->context->smarty->assign('attributeGroupOptions', $this->getAttributeGroupOptions());
        $this->context->smarty->assign('sortCombinationsByOptions', $this->getSortCombinationsByOptions());
        $this->context->smarty->assign('highlightCombinationsOptions', $this->getHighlightCombinationsOptions());
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            $this->context->smarty->assign('showColorOption', true);
        } else {
            $this->context->smarty->assign('showColorOption', false);
        }
        $this->context->smarty->assign('colorGroups', $this->getColorGroups());
        $this->context->smarty->assign('psDispUnavailableAttr', (bool)Configuration::get('PS_DISP_UNAVAILABLE_ATTR'));
        $this->context->smarty->assign('psStockManagement', (bool)Configuration::get('PS_STOCK_MANAGEMENT'));
        $this->context->smarty->assign('layeredModuleIsEnabled', $this->layeredModuleIsEnabled());
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->context->smarty->assign('showPagesOption', true);
        } else {
            $this->context->smarty->assign('showPagesOption', false);
        }
    }
    protected function postProcess()
    {
        if (Tools::getIsset('submitModuleConfiguration') && Tools::isSubmit('submitModuleConfiguration')) {
            $config = $this->getModuleConfiguration();
            foreach (array('changeProductName', 'hideCombinationsWithoutStock', 'hideColorSquares', 'addIDToAnchor', 'maintenanceMode', 'fullTree', 'hideCombinationsWithoutCover', 'autoReindex') as $configKey) {
                $config[$configKey] = (bool)Tools::getValue($configKey);
                if ($configKey == 'hideCombinationsWithoutStock' && !Configuration::get('PS_STOCK_MANAGEMENT')) {
                    $config[$configKey] = false;
                }
            }
            foreach (array('selectedGroups') as $configKey) {
                $config[$configKey] = Tools::getValue($configKey);
                if (!empty($config[$configKey]) && is_array($config[$configKey])) {
                    $config[$configKey] = array_map('intval', $config[$configKey]);
                } else {
                    $config[$configKey] = array();
                }
            }
            foreach (array('enabledControllers', 'nameSeparator', 'sortCombinationBy', 'combinationToHighlight') as $configKey) {
                $config[$configKey] = Tools::getValue($configKey);
            }
            $this->setModuleConfiguration($config);
            $this->fillCacheTable();
            $this->context->controller->confirmations[] = $this->l('Module configuration successfully saved');
        }
    }
    private function layeredModuleIsEnabled()
    {
        return (Module::isEnabled('ps_facetedsearch')) || (Module::isEnabled('blocklayered')) || (Module::isEnabled('pm_advancedsearch4'));
    }
    private function getColorGroups()
    {
        $content = '';
        if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            $sql = 'SELECT `id_attribute_group` FROM `'._DB_PREFIX_.'attribute_group` WHERE `is_color_group` = 1';
            $groups = DB::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
            if (is_array($groups)) {
                $attribute_groups_html = '';
                foreach ($groups as $group) {
                    $attribute_groups_html .= $group['id_attribute_group'].', ';
                }
                $content = Tools::substr($attribute_groups_html, 0, -2);
            }
        }
        return $content;
    }
    protected function isInMaintenance()
    {
        static $isInMaintenance = null;
        if ($isInMaintenance === null) {
            $config = $this->getModuleConfiguration();
            if (!empty($config['maintenanceMode'])) {
                $ips = explode(',', Configuration::get('PS_MAINTENANCE_IP'));
                $isInMaintenance = !in_array($_SERVER['REMOTE_ADDR'], $ips);
            }
        }
        return $isInMaintenance;
    }
    public function hasAtLeastOneAttributeGroup()
    {
        static $hasAtLeastOneAttributeGroup = null;
        if ($hasAtLeastOneAttributeGroup === null) {
            $conf = $this->getModuleConfiguration();
            if (Combination::isFeatureActive() && isset($conf['selectedGroups']) && is_array($conf['selectedGroups']) && count($conf['selectedGroups'])) {
                $hasAtLeastOneAttributeGroup = true;
            } else {
                $hasAtLeastOneAttributeGroup = false;
            }
        }
        return $hasAtLeastOneAttributeGroup;
    }
    protected function hasAutoReindexEnabled()
    {
        static $hasAutoReindexEnabled = null;
        if ($hasAutoReindexEnabled === null) {
            $conf = $this->getModuleConfiguration();
            $hasAutoReindexEnabled = !empty($conf['autoReindex']);
        }
        return $hasAutoReindexEnabled;
    }
    public function hookActionProductListOverride($params)
    {
        if ($this->isInMaintenance()) {
            return;
        }
        if (!$this->hasAtLeastOneAttributeGroup()) {
            $params['hookExecuted'] = false;
            return;
        }
        $conf = $this->getModuleConfiguration();
        if ((!isset($params['module']) || $params['module'] != 'pm_advancedsearch4') && Module::isEnabled('pm_advancedsearch4') && $this->context->controller instanceof CategoryController) {
            $as4Instance = Module::getInstanceByName('pm_advancedsearch4');
            if (method_exists($as4Instance, 'isFullTreeModeEnabled') && $as4Instance->isFullTreeModeEnabled()) {
                return;
            }
            if (method_exists($as4Instance, 'getModuleConfigurationStatic')) {
                $as4Configuration = $as4Instance::getModuleConfigurationStatic();
                if (!empty($as4Configuration['fullTree'])) {
                    return;
                }
            }
        }
        if (isset($params['module']) && $params['module'] == 'pm_advancedsearch4') {
            $this->splitProductsListOfSearchResults($params);
            $this->splitProductsList($params['catProducts']);
            $params['splitDone'] = true;
            $params['hookExecuted'] = $params['splitDone'];
        } else {
            $module = Module::getInstanceByName('blocklayered');
            if (!is_object($module) || !$module->active) {
                $id_category = (int)Tools::getValue('id_category');
                if (!$id_category || !Validate::isUnsignedId($id_category)) {
                    $this->errors[] = Tools::displayError('Missing category ID');
                }
                $this->hideColorSquares();
                $params['nbProducts'] = $this->getCategoryProducts((int)$id_category, null, null, null, $this->context->controller->orderBy, $this->context->controller->orderWay, true, (bool)$conf['fullTree']);
                $this->context->controller->pagination((int)$params['nbProducts']);
                $params['catProducts'] = $this->getCategoryProducts((int)$id_category, (int)$this->context->language->id, (int)$this->context->controller->p, (int)$this->context->controller->n, $this->context->controller->orderBy, $this->context->controller->orderWay, false, (bool)$conf['fullTree']);
                $this->splitProductsList($params['catProducts']);
                $params['hookExecuted'] = true;
            }
        }
    }
    public function hookActionProductListModifier($params)
    {
        if ($this->isInMaintenance() || !$this->hasAtLeastOneAttributeGroup()) {
            return false;
        }
        $module = Module::getInstanceByName('blocklayered');
        if (is_object($module) && $module->active) {
            $params['module'] = 'blocklayered';
            $this->hideColorSquares();
            $params['catProducts'] = $params['cat_products'];
            $params['nbProducts'] = $params['nb_products'];
            $this->splitProductsListOfSearchResults($params);
            $this->splitProductsList($params['catProducts']);
            $params['add_colors_to_product_list'] = false;
            $params['cat_products'] = $params['catProducts'];
            $params['nb_products'] = $params['nbProducts'];
        }
    }
    public function hookProductSearchProvider()
    {
        if ($this->isInMaintenance() || !$this->hasAtLeastOneAttributeGroup()) {
            return null;
        }
        $conf = $this->getModuleConfiguration();
        $currentController = get_class($this->context->controller);
        if ($currentController == 'IqitSearchSearchiqitModuleFrontController') {
            $currentController = 'SearchController';
        }
        $controllerClass = Tools::strReplaceFirst('Controller', '', $currentController);
        if (isset($conf['enabledControllers'][$controllerClass]) && $conf['enabledControllers'][$controllerClass]) {
            require_once _PS_ROOT_DIR_ . '/modules/pm_productsbyattributes/src/Pm_ProductsByAttributesProductSearchProvider.php';
            return new Pm_ProductsByAttributesProductSearchProvider($this, $conf);
        }
        return null;
    }
    public function hookActionGetProductPropertiesAfter($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            return;
        }
        if (isset($params['product']['product_name'])) {
            $params['product']['name'] = $params['product']['product_name'];
        }
    }
    public function hookFilterProductSearch($params)
    {
        $conf = $this->getModuleConfiguration();
        foreach ($params['searchVariables']['products'] as &$product) {
            if (empty($product['split-by-spa'])) {
                continue;
            }
            if (!empty($conf['hideColorSquares'])) {
                if (version_compare(_PS_VERSION_, '1.7.5.0', '<')) {
                    $product['main_variants'] = array();
                } else {
                    $product->offsetSet('main_variants', array(), true);
                }
            }
            if (version_compare(_PS_VERSION_, '1.7.6.0', '>=') && version_compare(_PS_VERSION_, '1.7.6.1', '<=')) {
                $product->offsetSet('canonical_url', $product['url'], true);
            }
        }
    }
    public function hookActionObjectAddAfter($params)
    {
        if (!isset($params['object']) || !Validate::isLoadedObject($params['object']) || !$this->hasAutoReindexEnabled()) {
            return;
        }
        if ($params['object'] instanceof Product) {
            return $this->fillCacheTable((int)$params['object']->id);
        } elseif ($params['object'] instanceof Combination && !empty($params['object']->id_product)) {
            return $this->fillCacheTable((int)$params['object']->id_product);
        }
    }
    public function hookActionObjectUpdateAfter($params)
    {
        if (!isset($params['object']) || !Validate::isLoadedObject($params['object']) || !$this->hasAutoReindexEnabled()) {
            return;
        }
        if ($params['object'] instanceof Product) {
            return $this->fillCacheTable((int)$params['object']->id);
        } elseif ($params['object'] instanceof Combination && !empty($params['object']->id_product)) {
            return $this->fillCacheTable((int)$params['object']->id_product);
        }
    }
    public function hookActionObjectDeleteBefore($params)
    {
        if (!isset($params['object']) || !Validate::isLoadedObject($params['object']) || !$this->hasAutoReindexEnabled()) {
            return;
        }
        if ($params['object'] instanceof Product) {
            return $this->fillCacheTable((int)$params['object']->id);
        } elseif ($params['object'] instanceof Combination && !empty($params['object']->id_product)) {
            return $this->fillCacheTable((int)$params['object']->id_product);
        }
    }
    public function splitProductsListOfSearchResults(&$params)
    {
        $conf = $this->getModuleConfiguration();
        $selectedSearchAttributes = array();
        $packIdList = false;
        if (class_exists('AdvancedPack')) {
            if (method_exists('AdvancedPack', 'getIdsPacks')) {
                $packIdList = AdvancedPack::getIdsPacks(true);
            }
        }
        if (isset($params['module']) && $params['module'] == 'pm_advancedsearch4') {
            foreach ($params['selected_criterion'] as $id_criterion_group => $selected_criterions) {
                foreach ($selected_criterions as $selected_criterion) {
                    if ($params['selected_criteria_groups_type'][(int)$id_criterion_group]['criterion_group_type'] != 'attribute') {
                        continue;
                    }
                    if (!in_array((int)$params['selected_criteria_groups_type'][(int)$id_criterion_group]['id_criterion_group_linked'], $conf['selectedGroups'])) {
                        continue;
                    }
                    if (!empty($params['id_search'])) {
                        $idSearch = (int)$params['id_search'];
                    } else {
                        $idSearch = (int)Tools::getValue('id_search', (int)Tools::getValue('id_seo_id_search', 0));
                    }
                    if (empty($idSearch)) {
                        continue;
                    }
                    $isVisible = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT visible FROM '._DB_PREFIX_.'pm_advancedsearch_criterion_group_'.(int)$idSearch.' WHERE id_criterion_group = '.(int)$id_criterion_group);
                    if (empty($isVisible)) {
                        continue;
                    }
                    $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_criterion_linked FROM '._DB_PREFIX_.'pm_advancedsearch_criterion_'.(int)$idSearch.'_link WHERE id_criterion = '.(int)$selected_criterion);
                    foreach ($rows as $row) {
                        $selectedAttribute = new Attribute((int)$row['id_criterion_linked']);
                        if (Validate::isLoadedObject($selectedAttribute)) {
                            if (!isset($selectedSearchAttributes[(int)$selectedAttribute->id_attribute_group])) {
                                $selectedSearchAttributes[(int)$selectedAttribute->id_attribute_group] = array();
                            }
                            $selectedSearchAttributes[(int)$selectedAttribute->id_attribute_group][] = $selectedAttribute->id;
                        }
                    }
                }
            }
        } elseif (isset($params['module']) && $params['module'] == 'blocklayered') {
            foreach ($_GET as $key => $value) {
                if (Tools::substr($key, 0, 27) == 'layered_id_attribute_group_') {
                    $tmp_tab = explode('_', $value);
                    $attribute_selected = (int)$tmp_tab[0];
                    $id_criterion_group = (int)$tmp_tab[1];
                    if (!in_array((int)$id_criterion_group, $conf['selectedGroups'])) {
                        continue;
                    }
                    $selectedAttribute = new Attribute((int)$attribute_selected);
                    if (Validate::isLoadedObject($selectedAttribute)) {
                        if (!isset($selectedSearchAttributes[(int)$selectedAttribute->id_attribute_group])) {
                            $selectedSearchAttributes[(int)$selectedAttribute->id_attribute_group] = array();
                        }
                        $selectedSearchAttributes[(int)$selectedAttribute->id_attribute_group][] = $selectedAttribute->id;
                    }
                }
            }
            $urlSelectedFilters = Tools::getValue('selected_filters');
            if ($urlSelectedFilters !== false) {
                $anchor = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR');
                if (!$anchor) {
                    $anchor = '-';
                }
                $urlAttributes = explode('/', ltrim($urlSelectedFilters, '/'));
                if (!empty($urlAttributes)) {
                    foreach ($urlAttributes as $urlAttribute) {
                        if (strpos($urlAttribute, 'page-') === 0) {
                            $urlAttribute = str_replace('-', $anchor, $urlAttribute);
                        }
                        $urlParameters = explode($anchor, $urlAttribute);
                        $attributeName  = array_shift($urlParameters);
                        if (in_array($attributeName, array('price', 'weight',' page'))) {
                            continue;
                        }
                        foreach ($urlParameters as $urlParameter) {
                            $blockLayeredData = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT data FROM `'._DB_PREFIX_.'layered_friendly_url` WHERE `url_key` = "' . pSQL(md5('/' . $attributeName . $anchor . $urlParameter)) . '"');
                            if (!$blockLayeredData) {
                                continue;
                            }
                            foreach (Tools::unSerialize($blockLayeredData) as $attributeType => $attributeParameters) {
                                if ($attributeType != 'id_attribute_group') {
                                    continue;
                                }
                                foreach ($attributeParameters as $attributeParameter) {
                                    $attributeParameter = array_map('intval', explode('_', $attributeParameter));
                                    $idAttribute = (int)$attributeParameter[1];
                                    $selectedAttribute = new Attribute((int)$idAttribute);
                                    if (Validate::isLoadedObject($selectedAttribute)) {
                                        if (!isset($selectedSearchAttributes[(int)$selectedAttribute->id_attribute_group])) {
                                            $selectedSearchAttributes[(int)$selectedAttribute->id_attribute_group] = array();
                                        }
                                        $selectedSearchAttributes[(int)$selectedAttribute->id_attribute_group][] = $selectedAttribute->id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $newProductList = array();
        $already_done = array();
        $checkStock = (!empty($conf['hideCombinationsWithoutStock']) || !Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) && Configuration::get('PS_STOCK_MANAGEMENT');
        if (Tools::getValue('orderby') == 'price') {
            Tools::orderbyPrice($params['catProducts'], Tools::strtolower(Tools::getValue('orderway', 'asc')));
        }
        $idProductListToCheck = array();
        foreach ($params['catProducts'] as $product) {
            $idProductListToCheck[] = (int)$product['id_product'];
        }
        $eligibleProducts = pm_productsbyattributes::getEligibleProducts($idProductListToCheck);
        foreach ($params['catProducts'] as &$product) {
            if (!in_array((int)$product['id_product'], $eligibleProducts)) {
                $combinations = array();
            } else {
                $combinations = pm_productsbyattributes::getAttributeCombinationsById((int)$product['id_product'], null, (int)$this->context->language->id, $conf['selectedGroups'], true);
                if (!is_array($combinations)) {
                    continue;
                }
            }
            if (!count($combinations)) {
                if (isset($already_done[(int)$product['id_product'].'_0'])) {
                    continue;
                }
                $product['spa-no-eligible-combinations'] = true;
                $newProductList[] = $product;
                $already_done[(int)$product['id_product'].'_0'] = true;
            }
            $isPack = (is_array($packIdList) && in_array((int)$product['id_product'], $packIdList));
            if ($isPack) {
                $newProductList[] = $product;
                $already_done[(int)$product['id_product'].'_0'] = true;
                continue;
            }
            foreach ($combinations as $combination) {
                if (isset($already_done[(int)$combination['id_product'].'_'.(int)$combination['id_product_attribute']])) {
                    continue;
                }
                if (in_array((int)$combination['id_attribute_group'], $conf['selectedGroups'])) {
                    if (isset($params['module']) && ($params['module'] == 'pm_advancedsearch4' || $params['module'] == 'blocklayered') && count($selectedSearchAttributes) > 0) {
                        $attributesMatched = true;
                        foreach ($selectedSearchAttributes as $selectedAttributes) {
                            $productAttributes = self::getAttributeCombinationsById((int)$combination['id_product'], (int)$combination['id_product_attribute'], (int)$this->context->language->id);
                            $matchesCount = 0;
                            foreach ($productAttributes as $product_attribute) {
                                if (in_array($product_attribute['id_attribute'], $selectedAttributes)) {
                                    $matchesCount++;
                                }
                            }
                            if ($matchesCount > 0 && $matchesCount <= count($selectedAttributes)) {
                                $attributesMatched &= true;
                            } else {
                                $attributesMatched &= false;
                            }
                        }
                        if (!$attributesMatched) {
                            $already_done[(int)$combination['id_product'].'_'.(int)$combination['id_product_attribute']] = true;
                            continue;
                        }
                    }
                    if ($checkStock && (int)$combination['quantity'] <= 0) {
                        $already_done[(int)$combination['id_product'].'_'.(int)$combination['id_product_attribute']] = true;
                        continue;
                    }
                    if (!$checkStock || ($checkStock && (int)$combination['quantity'] > 0)) {
                        $product['pai_id_product_attribute'] = (int)$combination['id_product_attribute'];
                        $product['cache_default_attribute'] = (int)$combination['id_product_attribute'];
                        $product['split-by-spa'] = true;
                        if (!empty($conf['changeProductName'])) {
                            $product['product_name'] = pm_productsbyattributes::getFullProductName($product['name'], (int)$product['id_product'], (int)$combination['id_product_attribute'], (int)$this->context->language->id);
                        }
                        $product['quantity_sql'] = $combination['quantity'];
                        $product['is_color_group'] = (bool)$combination['is_color_group'];
                        $already_done[(int)$combination['id_product'].'_'.(int)$combination['id_product_attribute']] = true;
                        $newProductList[] = $product;
                    }
                }
            }
        }
        $params['nbProducts'] = count($newProductList);
        if (isset($params['products_per_page'])) {
            if (!empty($params['p']) && !empty($params['n'])) {
                $params['catProducts'] = array_slice($newProductList, (int)$params['products_per_page'] * ((int)$params['p'] - 1), $params['n']);
            } else {
                $params['catProducts'] = array_slice($newProductList, (int)$params['products_per_page'] * ((int)Tools::getValue('p', 1) - 1), Tools::getValue('n', (int)$params['products_per_page']));
            }
        } else {
            $params['catProducts'] = $newProductList;
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<') && method_exists($this->context->controller, 'addColorsToProductList')) {
            $this->context->controller->addColorsToProductList($params['catProducts']);
        }
    }
    public function getCategoryProducts($id_category, $id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $fullTree = false)
    {
        $context = Context::getContext();
        $currentCategory = new Category((int)$id_category);
        if (!Validate::isLoadedObject($currentCategory)) {
            if (method_exists($context->controller, 'getCategory')) {
                $currentCategory = $context->controller->getCategory();
            }
        }
        if (!Validate::isLoadedObject($currentCategory)) {
            if ($get_total) {
                return 0;
            } else {
                return array();
            }
        }
        if (!$id_lang) {
            $id_lang = (int)$this->context->language->id;
        }
        $conf = $this->getModuleConfiguration();
        $front = in_array($context->controller->controller_type, array('front', 'modulefront'));
        $id_supplier = (int)Tools::getValue('id_supplier');
        $packIdList = false;
        if (class_exists('AdvancedPack')) {
            if (method_exists('AdvancedPack', 'getIdsPacks')) {
                $packIdList = AdvancedPack::getIdsPacks(true);
            }
        }
        $checkStock = ($conf['hideCombinationsWithoutStock'] || !Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) && Configuration::get('PS_STOCK_MANAGEMENT');
        if ($get_total) {
            $sql = '
                    SELECT COUNT(total) as total
                    FROM
                    (
                        (
                            SELECT COUNT(p.`id_product`) as total
                            FROM `'._DB_PREFIX_.'category_product` cp
                            RIGHT JOIN `'._DB_PREFIX_.'category` c ON (c.`id_category` = cp.`id_category` AND c.nleft >= '.(int)$currentCategory->nleft.' AND c.nright <= '.(int)$currentCategory->nright.')
                            LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
                            '.Shop::addSqlAssociation('product', 'p').'
                            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.`id_product` = p.`id_product`
                            LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            JOIN `'._DB_PREFIX_.'pm_spa_cache` pa_cartesian ON (pa_cartesian.`id_product` = p.`id_product` AND pa_cartesian.`id_product_attribute` = pa.`id_product_attribute` AND pa_cartesian.`id_shop` = ' . (int)$context->shop->id . ')
                            ' . ($checkStock ? Product::sqlStock('p', 'pa') : '') . '
                            JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`'
                            .(version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`id_product_attribute` = pac.`id_product_attribute` AND product_attribute_shop.id_shop='.(int)$context->shop->id.')' : 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (product_attribute_shop.`id_product_attribute` = pac.`id_product_attribute` AND product_attribute_shop.id_shop='.(int)$context->shop->id.')') . '
                            WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
                            ' . ($checkStock ? ' AND stock.quantity > 0 ' : '') . '
                            AND product_shop.`active` = 1
                            AND cp.`id_category` '.(!$fullTree ? '= '.(int)$currentCategory->id : ' > 0')
                            .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                            .(is_array($packIdList) && count($packIdList) ? ' AND cp.id_product NOT IN (' . implode(',', array_map('intval', $packIdList)) . ') ' : '')
                            .($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
                            .' GROUP BY cp.`id_product`, pa.`id_product_attribute`
                        )
                        UNION ALL
                        (
                            SELECT COUNT(p.`id_product`) as total
                            FROM `'._DB_PREFIX_.'category_product` cp
                            RIGHT JOIN `'._DB_PREFIX_.'category` c ON (c.`id_category` = cp.`id_category` AND c.nleft >= '.(int)$currentCategory->nleft.' AND c.nright <= '.(int)$currentCategory->nright.')
                            LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
                            '.Shop::addSqlAssociation('product', 'p').'
                            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.`id_product` = p.`id_product` AND pa.`default_on` = 1
                            LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute` AND (a.`id_attribute_group` IN (' . implode(',', array_map('intval', $conf['selectedGroups'])) . '))'
                            .(version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`id_product_attribute` = pac.`id_product_attribute` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')' : 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (product_attribute_shop.`id_product_attribute` = pac.`id_product_attribute` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')')
                            .Product::sqlStock('p', 0).'
                            WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
                            AND product_shop.`active` = 1
                            AND cp.`id_category` '.(!$fullTree ? '= '.(int)$currentCategory->id : ' > 0')
                            .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                            .($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
                            .' GROUP BY cp.`id_product`
                            HAVING (COUNT(a.`id_attribute`) = 0' . (is_array($packIdList) && count($packIdList) ? ' OR cp.id_product IN (' . implode(',', array_map('intval', $packIdList)) . ') ' : '') . ')
                        )
                    ) total_query';
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $order_by  = Validate::isOrderBy($order_by)   ? Tools::strtolower($order_by)  : 'position';
        $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';
        $need_order_value_field = null;
        if ($order_by == 'date_add' || $order_by == 'date_upd') {
            $need_order_value_field = true;
            $order_by_prefix = 'p';
        } elseif ($order_by == 'position') {
            $need_order_value_field = true;
            $order_by_prefix = 'cp';
        } elseif ($order_by == 'id_product') {
            $need_order_value_field = true;
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $need_order_value_field = true;
            $order_by_prefix = 'pl';
        }
        $sortCombinationColumnName = null;
        $sortCombinationColumnWay = null;
        if (!empty($conf['sortCombinationBy'])) {
            switch ($conf['sortCombinationBy']) {
                case 'quantity_asc':
                    $sortCombinationColumnName = 'stock.`quantity`';
                    $sortCombinationColumnWay = 'ASC';
                    break;
                case 'quantity_desc':
                    $sortCombinationColumnName = 'stock.`quantity`';
                    $sortCombinationColumnWay = 'DESC';
                    break;
                case 'price_asc':
                    $sortCombinationColumnName = 'product_attribute_shop.`price`';
                    $sortCombinationColumnWay = 'ASC';
                    break;
                case 'price_desc':
                    $sortCombinationColumnName = 'product_attribute_shop.`price`';
                    $sortCombinationColumnWay = 'DESC';
                    break;
                case 'unit_price_asc':
                    $sortCombinationColumnName = 'product_attribute_shop.`unit_price_impact`';
                    $sortCombinationColumnWay = 'ASC';
                    break;
                case 'unit_price_desc':
                    $sortCombinationColumnName = 'product_attribute_shop.`unit_price_impact`';
                    $sortCombinationColumnWay = 'DESC';
                    break;
            }
        }
        $sql = '(SELECT '.(!empty($need_order_value_field) ? $order_by_prefix.'.'.bqSQL($order_by).' AS `order_value`, ' : '').'p.*, product_shop.*, pl.`name`, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity, IFNULL(stock.quantity, 0) AS quantity_sql, '
                    .(version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? 'IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute' : 'IFNULL(pa.`id_product_attribute`, 0) AS id_product_attribute').',
                    product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
                    pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, image_shop.`id_image` id_image,
                    il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
                    DATEDIFF(product_shop.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00",
                    INTERVAL '.(int)$nb_days_new_product.' DAY)) > 0 AS new, product_shop.price AS orderprice, cp.`position`, a.`position` as `position_attribute`, pa.`id_product_attribute` as pai_id_product_attribute, MAX(ag.`is_color_group`),
                    CONCAT_WS("-", "spa", "a", p.id_product, pa.id_product_attribute) as `id_product_pack`' . (!empty($sortCombinationColumnName) ? ', ' . pSQL($sortCombinationColumnName) . ' AS `spa_sort_column`' : '') . '
                FROM `'._DB_PREFIX_.'category_product` cp
                RIGHT JOIN `'._DB_PREFIX_.'category` c ON (c.`id_category` = cp.`id_category` AND c.nleft >= '.(int)$currentCategory->nleft.' AND c.nright <= '.(int)$currentCategory->nright.')
                LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')'
                .(version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? 'LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')' : 'LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1')).'
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.`id_product` = p.`id_product`
                LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                JOIN `'._DB_PREFIX_.'pm_spa_cache` pa_cartesian ON (pa_cartesian.`id_product` = p.`id_product` AND pa_cartesian.`id_product_attribute` = pa.`id_product_attribute` AND pa_cartesian.`id_shop` = ' . (int)$context->shop->id . ')
                JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute` AND (a.`id_attribute_group` IN (' . implode(',', array_map('intval', $conf['selectedGroups'])) . '))
                LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)'
                .(version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`id_product_attribute` = pac.`id_product_attribute` AND product_attribute_shop.id_shop='.(int)$context->shop->id.')' : 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (product_attribute_shop.`id_product_attribute` = pac.`id_product_attribute` AND product_attribute_shop.id_shop='.(int)$context->shop->id.')')
                .Product::sqlStock('p', 'pa').'
                WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
                    AND product_shop.`active` = 1
                    AND cp.`id_category` '.(!$fullTree ? '= '.(int)$currentCategory->id : ' > 0')
                    . ($checkStock ? ' AND stock.quantity > 0 ' : '')
                    .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                    .($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
                    .(is_array($packIdList) && count($packIdList) ? ' AND cp.id_product NOT IN (' . implode(',', array_map('intval', $packIdList)) . ') ' : '')
                    .' GROUP BY cp.`id_product`, pa.`id_product_attribute`)';
        $sql .= '
        UNION ALL
        ';
        $sql .= '(SELECT '.(!empty($need_order_value_field) ? $order_by_prefix.'.'.bqSQL($order_by).' AS `order_value`, ' : '').'p.*, product_shop.*, pl.`name`, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity, IFNULL(stock.quantity, 0) AS quantity_sql, '
                    .(version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? 'IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute' : 'IFNULL(pa.`id_product_attribute`, 0) AS id_product_attribute').',
                    product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
                    pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, image_shop.`id_image` id_image,
                    il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
                    DATEDIFF(product_shop.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00", INTERVAL '.(int)$nb_days_new_product.' DAY)) > 0 AS new, product_shop.price AS orderprice,
                    cp.`position`, a.`position` as `position_attribute`, pa.`id_product_attribute` as pai_id_product_attribute, "0" as `is_color_group`,
                    "spa-nochanges" as `id_product_pack`' . (!empty($sortCombinationColumnName) ? ', ' . pSQL($sortCombinationColumnName) . ' AS `spa_sort_column`' : '') . '
                FROM `'._DB_PREFIX_.'category_product` cp
                RIGHT JOIN `'._DB_PREFIX_.'category` c ON (c.`id_category` = cp.`id_category` AND c.nleft >= '.(int)$currentCategory->nleft.' AND c.nright <= '.(int)$currentCategory->nright.')
                LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')'
                .(version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? 'LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')' : 'LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1')).'
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.`id_product` = p.`id_product`
                LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute` AND (a.`id_attribute_group` IN (' . implode(',', array_map('intval', $conf['selectedGroups'])) . '))'
                .(version_compare(_PS_VERSION_, '1.6.1.0', '>=') ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`id_product_attribute` = pac.`id_product_attribute` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')' : 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (product_attribute_shop.`id_product_attribute` = pac.`id_product_attribute` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')')
                .Product::sqlStock('p', 0).'
                WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
                    AND product_shop.`active` = 1
                    AND cp.`id_category` '.(!$fullTree ? '= '.(int)$currentCategory->id : ' > 0')
                    .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                    .($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
                   .' GROUP BY cp.id_product
                        HAVING (COUNT(a.`id_attribute`) = 0' . (is_array($packIdList) && count($packIdList) ? ' OR cp.id_product IN (' . implode(',', array_map('intval', $packIdList)) . ') ' : '') . ')
                   )';
        if ($p < 1) {
            $p = 1;
        }
        $order_by_prefix = false;
        $order_by2 = false;
        if ($order_by == 'date_add' || $order_by == 'date_upd' || $order_by == 'id_product' || $order_by == 'name') {
            $order_by = 'order_value';
        } elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name') {
            $order_by = 'manufacturer_name';
        } elseif ($order_by == 'quantity') {
            $order_by = 'quantity_sql';
        } elseif ($order_by == 'price') {
            $order_by = 'orderprice';
        } elseif ($order_by == 'position') {
            $order_by = 'order_value';
            if (empty($sortCombinationColumnName)) {
                $order_by2 = 'position_attribute';
            }
        }
        $sql .= ' ORDER BY '.(!empty($order_by_prefix) ? $order_by_prefix.'.' : '').'`'.bqSQL($order_by).'`'.(!empty($order_by2) ? ', `'.bqSQL($order_by2).'`' : '').' '.pSQL($order_way) . (!empty($sortCombinationColumnName) ? ', `spa_sort_column` ' . pSQL($sortCombinationColumnWay) : '');
        $sql .= ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
        if (!$result) {
            return array();
        }
        if ($order_by == 'orderprice') {
            Tools::orderbyPrice($result, $order_way);
        }
        return Product::getProductsProperties($id_lang, $result);
    }
    public function splitProductsList(&$products)
    {
        $conf = $this->getModuleConfiguration();
        $combined = array();
        $packIdList = false;
        if (class_exists('AdvancedPack')) {
            if (method_exists('AdvancedPack', 'getIdsPacks')) {
                $packIdList = AdvancedPack::getIdsPacks(true);
            }
        }
        $psRewritingSettings = Configuration::get('PS_REWRITING_SETTINGS');
        $addAnchor = !empty($conf['addIDToAnchor']);
        foreach ($products as $p => &$product) {
            $isPack = (is_array($packIdList) && in_array((int)$product['id_product'], $packIdList));
            if (!$isPack && (!isset($product['id_product_pack']) || $product['id_product_pack'] != 'spa-nochanges')) {
                $combined[(int)$product['id_product']] = true;
                if (isset($product['pai_id_product_attribute'])) {
                    $product['id_product_attribute'] = (int)$product['pai_id_product_attribute'];
                    $product['cache_default_attribute'] = (int)$product['pai_id_product_attribute'];
                } else {
                    $product['pai_id_product_attribute'] = (int)$product['id_product_attribute'];
                    $product['cache_default_attribute'] = (int)$product['pai_id_product_attribute'];
                }
                $product['split-by-spa'] = true;
                if (isset($product['quantity_sql'])) {
                    $product['quantity'] = (int)$product['quantity_sql'];
                }
                $combinationImage = self::getBestImageAttribute((int)$this->context->shop->id, (int)$this->context->language->id, (int)$product['id_product'], (int)$product['pai_id_product_attribute']);
                $customIdImage = null;
                if (isset($combinationImage['id_image'])) {
                    $customIdImage = (int)$combinationImage['id_image'];
                } else {
                    if (!empty($product['spa-no-eligible-combinations']) || !isset($conf['hideCombinationsWithoutCover']) || !$conf['hideCombinationsWithoutCover']) {
                        $cover = Product::getCover((int)$product['id_product']);
                        $customIdImage = (int)$cover['id_image'];
                    } else {
                        unset($products[(int)$p]);
                        continue;
                    }
                }
                if (!empty($customIdImage)) {
                    $product['cover_image_id'] = (int)$customIdImage;
                }
                $product['id_product_pack'] = 'spa-'.$product['id_product'].'-'.$product['id_product_attribute'];
                $product = Product::getProductProperties((int)$this->context->language->id, $product);
                if (!empty($conf['changeProductName'])) {
                    $product['name'] = pm_productsbyattributes::getFullProductName($product['name'], (int)$product['id_product'], (int)$product['id_product_attribute'], (int)$this->context->language->id);
                }
                if (!isset($product['category']) || $product['category'] == '') {
                    $product['category'] =  Category::getLinkRewrite((int)$product['id_category_default'], (int)$this->context->language->id);
                }
                $product['link'] = $this->context->link->getProductLink((int)$product['id_product'], $product['link_rewrite'], $product['category'], $product['ean13'], null, null, (int)$product['pai_id_product_attribute'], $psRewritingSettings, false, $addAnchor);
                if (isset($combinationImage['id_image'])) {
                    $product['id_image'] = (int)$combinationImage['id_image'];
                } else {
                    if (!empty($product['spa-no-eligible-combinations']) || !isset($conf['hideCombinationsWithoutCover']) || !$conf['hideCombinationsWithoutCover']) {
                        $product['attribute_image'] = (int)$customIdImage;
                        $product['id_image'] = Product::defineProductImage(array(
                            'id_product' => (int)$product['id_product'],
                            'id_image' => (int)$customIdImage,
                        ), (int)$this->context->language->id);
                    }
                }
            } else {
                $product = Product::getProductProperties((int)$this->context->language->id, $product);
                if (!$isPack) {
                    $product['quantity'] = (int)$product['quantity_sql'];
                }
                if (isset($combined[(int)$product['id_product']])) {
                    unset($products[(int)$p]);
                } else {
                    $combined[(int)$product['id_product']] = true;
                }
            }
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<') && method_exists($this->context->controller, 'addColorsToProductList')) {
            $this->context->controller->addColorsToProductList($products);
        }
        return $products;
    }
    public static function getBestImageAttribute($id_shop, $id_lang, $id_product, $id_product_attribute)
    {
        if (method_exists('Image', 'getBestImageAttribute')) {
            return Image::getBestImageAttribute((int)$id_shop, (int)$id_lang, (int)$id_product, (int)$id_product_attribute);
        } else {
            $cache_id = 'Image::getBestImageAttribute'.'-'.(int)$id_product.'-'.(int)$id_product_attribute.'-'.(int)$id_lang.'-'.(int)$id_shop;
            if (!Cache::isStored($cache_id)) {
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                        SELECT image_shop.`id_image` id_image, il.`legend`
                        FROM `'._DB_PREFIX_.'image` i
                        INNER JOIN `'._DB_PREFIX_.'image_shop` image_shop
                            ON (i.id_image = image_shop.id_image AND image_shop.id_shop = '.(int)$id_shop.')
                            INNER JOIN `'._DB_PREFIX_.'product_attribute_image` pai
                            ON (pai.`id_image` = i.`id_image` AND pai.`id_product_attribute` = '.(int)$id_product_attribute.')
                        LEFT JOIN `'._DB_PREFIX_.'image_lang` il
                            ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
                        WHERE i.`id_product` = '.(int)$id_product.' ORDER BY i.`position` ASC');
                Cache::store($cache_id, $row);
            } else {
                $row = Cache::retrieve($cache_id);
            }
            return $row;
        }
    }
    protected function hideColorSquares()
    {
        $conf = $this->getModuleConfiguration();
        if (isset($conf['hideColorSquares']) && $conf['hideColorSquares']) {
            $hasColorGroup = (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT 1 FROM `'._DB_PREFIX_.'attribute_group` WHERE `is_color_group`=1 AND `id_attribute_group` IN (' . implode(',', array_map('intval', $conf['selectedGroups'])) . ')');
            if ($hasColorGroup) {
                $this->context->controller->addJS($this->_path . 'views/js/hide-color-list-container.js');
            }
        }
    }
    protected function getAttributeGroupOptions()
    {
        $conf = $this->getModuleConfiguration();
        $attributeGroups = AttributeGroup::getAttributesGroups((int)$this->context->language->id);
        $return = array();
        foreach ($conf['selectedGroups'] as $selectedAttributeGroup) {
            foreach ($attributeGroups as $attributeGroup) {
                if ((int)$attributeGroup['id_attribute_group'] == (int)$selectedAttributeGroup) {
                    $return[(int)$attributeGroup['id_attribute_group']] = $attributeGroup['name'];
                    break;
                }
            }
        }
        foreach ($attributeGroups as $attributeGroup) {
            if (!isset($return[(int)$attributeGroup['id_attribute_group']])) {
                $return[(int)$attributeGroup['id_attribute_group']] = $attributeGroup['name'];
            }
        }
        $module = Module::getInstanceByName('pm_advancedpack');
        if (is_object($module) && isset($return[(int)AdvancedPack::getPackAttributeGroupId()])) {
            unset($return[(int)AdvancedPack::getPackAttributeGroupId()]);
        }
        return $return;
    }
    protected function getSortCombinationsByOptions()
    {
        $options = array(
            '' => $this->l('--- Default sorting ---'),
            'quantity_asc' => $this->l('Available quantity (asc)'),
            'quantity_desc' => $this->l('Available quantity (desc)'),
            'price_asc' => $this->l('Price impact (asc)'),
            'price_desc' => $this->l('Price impact (desc)'),
            'unit_price_asc' => $this->l('Unit price impact (asc)'),
            'unit_price_desc' => $this->l('Unit price impact (desc)'),
        );
        if (!Configuration::get('PS_STOCK_MANAGEMENT')) {
            unset($options['quantity_asc']);
            unset($options['quantity_desc']);
        }
        return $options;
    }
    protected function getHighlightCombinationsOptions()
    {
        $options = array(
            '' => $this->l('--- Default sorting ---'),
            'quantity_asc' => $this->l('with the lowest available quantity'),
            'quantity_desc' => $this->l('with the largest available quantity'),
            'price_asc' => $this->l('with the lowest price impact'),
            'price_desc' => $this->l('with the largest price impact'),
            'unit_price_asc' => $this->l('with the lowest unit price impact'),
            'unit_price_desc' => $this->l('with the largest unit price impact'),
        );
        if (!Configuration::get('PS_STOCK_MANAGEMENT')) {
            unset($options['quantity_asc']);
            unset($options['quantity_desc']);
        }
        return $options;
    }
    protected static function getEligibleProducts($idProductList)
    {
        static $conf = null;
        static $checkStock = false;
        if ($conf === null) {
            $conf = self::getModuleConfigurationStatic();
            $checkStock = ($conf['hideCombinationsWithoutStock'] || !Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) && Configuration::get('PS_STOCK_MANAGEMENT');
        }
        $result = array();
        foreach (array_chunk($idProductList, 500) as $idProductListChunked) {
            $sql = 'SELECT pa.`id_product`
                    FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                    ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                    LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                    JOIN `'._DB_PREFIX_.'pm_spa_cache` pa_cartesian ON (pa_cartesian.`id_product` = pa.`id_product` AND pa_cartesian.`id_product_attribute` = pa.`id_product_attribute` AND pa_cartesian.`id_shop` = ' . (int)Context::getContext()->shop->id . ')
                    ' . ($checkStock ? 'JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = pa.`id_product`)' : '') . '
                    ' . ($checkStock ? Product::sqlStock('p', 'pa') : '') . '
                    WHERE pa.`id_product` IN (' . implode(',', array_map('intval', $idProductListChunked)) . ')
                    ' . ($checkStock ? ' AND stock.quantity > 0 ' : '') . '
                    GROUP BY pa.`id_product` HAVING COUNT(pa.`id_product_attribute`) > 0';
            $result = array_merge($result, Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql));
        }
        $validIdProductList = array();
        foreach ($result as $row) {
            $validIdProductList[] = (int)$row['id_product'];
        }
        return $validIdProductList;
    }
    public static function getAttributeCombinationsById($idProduct, $idProductAttribute, $idLang, $idAttributeGroupList = array(), $withQuantity = false,$id_pa_in = false)
    {
        static $cache = array();
        static $conf = null;
        static $checkStock = false;
        if ($conf === null) {
            $conf = self::getModuleConfigurationStatic();
            $checkStock = ($conf['hideCombinationsWithoutStock'] || !Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) && Configuration::get('PS_STOCK_MANAGEMENT');
        }
        if ($withQuantity && !Configuration::get('PS_STOCK_MANAGEMENT')) {
            $withQuantity = $checkStock = false;
        }
        if (!empty($idProductAttribute) && isset($cache[$idProduct])) {
            $result = array();
            foreach ($cache[$idProduct] as $row) {
                if ($row['id_product_attribute'] == $idProductAttribute) {
                    $result[] = $row;
                }
            }
            return $result;
        }
        $sortCombinationBy = null;
        if (!empty($conf['sortCombinationBy'])) {
            switch ($conf['sortCombinationBy']) {
                case 'quantity_asc':
                    $sortCombinationBy = 'stock.`quantity` ASC';
                    $withQuantity = true;
                    break;
                case 'quantity_desc':
                    $sortCombinationBy = 'stock.`quantity` DESC';
                    $withQuantity = true;
                    break;
                case 'price_asc':
                    $sortCombinationBy = 'product_attribute_shop.`price` ASC';
                    break;
                case 'price_desc':
                    $sortCombinationBy = 'product_attribute_shop.`price` DESC';
                    break;
                case 'unit_price_asc':
                    $sortCombinationBy = 'product_attribute_shop.`unit_price_impact` ASC';
                    break;
                case 'unit_price_desc':
                    $sortCombinationBy = 'product_attribute_shop.`unit_price_impact` DESC';
                    break;
            }
        }

        $sql = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`, al.`name` AS attribute_name, a.`id_attribute`, ag.`is_color_group`' . ($withQuantity ? ', stock.`quantity` as `sa_quantity`' : '') . '
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)$idLang . ')
                ' . (!empty($idAttributeGroupList) ? 'JOIN `'._DB_PREFIX_.'pm_spa_cache` pa_cartesian ON (pa_cartesian.`id_product` = pa.`id_product` AND pa_cartesian.`id_product_attribute` = pa.`id_product_attribute` AND pa_cartesian.`id_shop` = ' . (int)Context::getContext()->shop->id . ')' : '') . '
                ' . ($checkStock || $withQuantity ? 'JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = pa.`id_product`)' : '') . '
                ' . ($checkStock || $withQuantity ? Product::sqlStock('p', 'pa') : '') . '
                WHERE pa.`id_product` = ' . (int)$idProduct . '
                ' . ($checkStock ? ' AND stock.quantity > 0 ' : '') . '
                ' . (!empty($idProductAttribute) ? ' AND pa.`id_product_attribute` = ' . (int)$idProductAttribute : '') . '
                ' . ($id_pa_in ? ' AND pa.`id_product_attribute` in ('.$id_pa_in.') ' : '') . '
                GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                ORDER BY ' . (empty($sortCombinationBy) ? 'ag.`position`, a.`position`, pa.`id_product_attribute`' : pSQL($sortCombinationBy));
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);


        if ($withQuantity) {
            foreach ($result as &$row) {
                $row['quantity'] = (int)$row['sa_quantity'];
                unset($row['sa_quantity']);
            }
        }
        if (empty($idProductAttribute) && !isset($cache[$idProduct])) {
            $cache[$idProduct] = $result;
        }
        return $result;
    }
    public static function getFullProductName($originalProductName, $idProduct, $idProductAttribute, $idLang)
    {
        static $conf = null;
        if ($conf === null) {
            $conf = self::getModuleConfigurationStatic();
        }
        $tmpProductName = array($originalProductName);
        $productAttributes = self::getAttributeCombinationsById((int)$idProduct, (int)$idProductAttribute, (int)$idLang);
        $sortedProductAttributes = array();
        foreach ($productAttributes as $productAttribute) {
            $configPosition = array_search($productAttribute['id_attribute_group'], $conf['selectedGroups']);
            if ($configPosition !== false) {
                $sortedProductAttributes[$configPosition] = $productAttribute;
            }
        }
        ksort($sortedProductAttributes);
        foreach ($sortedProductAttributes as $productAttribute) {
            if (in_array((int)$productAttribute['id_attribute_group'], $conf['selectedGroups'])) {
                $tmpProductName[] = $productAttribute['attribute_name'];
            }
        }
        return implode($conf['nameSeparator'], $tmpProductName);
    }
    public function getHideColorSquaresConf()
    {
        $conf = $this->getModuleConfiguration();
        if (isset($conf['hideColorSquares']) && $conf['hideColorSquares']) {
            return true;
        } else {
            return false;
        }
    }
    public function hookDisplayHeader()
    {
        if ($this->isInMaintenance() || !$this->hasAtLeastOneAttributeGroup()) {
            return;
        }
        $module = Module::getInstanceByName('blocklayered');
        if (is_object($module) && $module->active) {
            return '<style type="text/css">.product-count { visibility: hidden; }</style>';
        }
    }
    public function getSplittedGroups()
    {
        $config = $this->getModuleConfiguration();
        if (!isset($config['selectedGroups']) || empty($config['selectedGroups'])) {
            return array();
        }
        if (!empty($config['selectedGroups']) && !is_array($config['selectedGroups'])) {
            return array((int)$config['selectedGroups']);
        }
        return $config['selectedGroups'];
    }
}
