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

use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
class Pm_ProductsByAttributesProductSearchProvider implements FacetsRendererInterface, ProductSearchProviderInterface
{
    private $module;
    private $conf;
    private $fullTreeAlreadyDone;
    public function __construct(pm_productsbyattributes $module, array $conf = array())
    {
        $this->module = $module;
        $this->conf = $conf;
        $this->fullTreeAlreadyDone = false;
    }
    protected function getDefaultProductSearchProvider()
    {
        $provider = null;
        switch (get_class(Context::getContext()->controller)) {
            case 'BestSalesController':
                return new PrestaShop\PrestaShop\Adapter\BestSales\BestSalesProductSearchProvider(
                    Context::getContext()->getTranslator()
                );
            case 'CategoryController':
                if (!$this->conf['fullTree']) {
                    $this->fullTreeAlreadyDone = true;
                }
                require_once _PS_ROOT_DIR_ . '/modules/pm_productsbyattributes/src/Pm_ProductsByAttributesCategoryProvider.php';
                return new Pm_ProductsByAttributesCategoryProvider(
                    Context::getContext()->getTranslator(),
                    new Category(
                        (int)Tools::getValue('id_category'),
                        Context::getContext()->language->id
                    ),
                    $this->module
                );
            case 'ManufacturerController':
                return new PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerProductSearchProvider(
                    Context::getContext()->getTranslator(),
                    new Manufacturer(
                        (int)Tools::getValue('id_manufacturer'),
                        Context::getContext()->language->id
                    )
                );
            case 'NewProductsController':
                return new PrestaShop\PrestaShop\Adapter\NewProducts\NewProductsProductSearchProvider(
                    Context::getContext()->getTranslator()
                );
            case 'PricesDropController':
                return new PrestaShop\PrestaShop\Adapter\PricesDrop\PricesDropProductSearchProvider(
                    Context::getContext()->getTranslator()
                );
            case 'SearchController':
            case 'IqitSearchSearchiqitModuleFrontController':
                return new PrestaShop\PrestaShop\Adapter\Search\SearchProductSearchProvider(
                    Context::getContext()->getTranslator()
                );
            case 'SupplierController':
                return new PrestaShop\PrestaShop\Adapter\Supplier\SupplierProductSearchProvider(
                    Context::getContext()->getTranslator(),
                    new Supplier(
                        (int)Tools::getValue('id_supplier'),
                        Context::getContext()->language->id
                    )
                );
            default:
                break;
        }
        return $provider;
    }
    protected function getProductSearchProvider(ProductSearchQuery $query)
    {
        $providers = Hook::exec(
            'productSearchProvider',
            array('query' => $query),
            null,
            true
        );
        if (!is_array($providers)) {
            $providers = array();
        }
        foreach ($providers as $module_name => $provider) {
            if ($module_name != 'pm_productsbyattributes' && $provider instanceof ProductSearchProviderInterface) {
                if (Validate::isModuleName($module_name)) {
                    $this->providerModule = Module::getInstanceByName($module_name);
                }
                return $provider;
            } else {
                $provider = null;
            }
        }
        return $this->getDefaultProductSearchProvider();
    }
    public function renderFacets(
        ProductSearchContext $context,
        ProductSearchResult $result
    ) {
        if (isset($this->searchProvider) && is_object($this->searchProvider) && $this->searchProvider instanceof FacetsRendererInterface) {
            return $this->searchProvider->renderFacets($context, $result);
        }
        return '';
    }
    public function renderActiveFilters(
        ProductSearchContext $context,
        ProductSearchResult $result
    ) {
        if (isset($this->searchProvider) && is_object($this->searchProvider) && $this->searchProvider instanceof FacetsRendererInterface) {
            return $this->searchProvider->renderActiveFilters($context, $result);
        }
        return '';
    }
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $this->searchProvider = $this->getProductSearchProvider($query);
        $resultsPerPage = (int)$query->getResultsPerPage();
        $page = (int)$query->getPage();
        $provider = $this->getProductSearchProvider($query);
        $query->setPage(1);
        $countResult = $provider->runQuery($context, $query, 'count');
        $query->setResultsPerPage((int)$countResult->getTotalProductsCount());
        if (($encodedSortOrder = Tools::getValue('order'))) {
            $query->setSortOrder(SortOrder::newFromString(
                $encodedSortOrder
            ));
        }
        $result = $provider->runQuery($context, $query);
        $query->setResultsPerPage((int)$resultsPerPage);
        $query->setPage((int)$page);
        if (!$result->getCurrentSortOrder()) {
            $result->setCurrentSortOrder($query->getSortOrder());
        }
        $facetedSearchSelectedFilters = $this->getPsFacetedSearchSelectedFilters($provider, $result, $query);
        $globalContext = Context::getContext();
        $idLang = (int)$context->getIdLang();
        $productsDataSet = array();
        $totalProcessedProducts = 0;
        $visibleProductsOffsetStart = ((int)$resultsPerPage * ($page - 1));
        $visibleProductsOffsetEnd = ((int)$resultsPerPage * $page);
        $checkStock = (!empty($this->conf['hideCombinationsWithoutStock']) || !Configuration::get('PS_DISP_UNAVAILABLE_ATTR'));
        if (!$this->fullTreeAlreadyDone) {
            $packIdList = false;
            if (class_exists('AdvancedPack')) {
                if (method_exists('AdvancedPack', 'getIdsPacks')) {
                    $packIdList = AdvancedPack::getIdsPacks(true);
                }
            }
            $productsList = $result->getProducts();
            if ($result->getCurrentSortOrder()->getEntity() == 'product' && $result->getCurrentSortOrder()->getField() == 'price') {
                Tools::orderbyPrice($productsList, Tools::strtolower($result->getCurrentSortOrder()->getDirection()));
            }
            $already_done = array();
            $id_pa_in = $this->getIdProductAttributeIn();
            foreach ($productsList as $product) {
                $almost_one = false;
                $combinations = pm_productsbyattributes::getAttributeCombinationsById((int)$product['id_product'], null, $idLang, $this->conf['selectedGroups'], true,$id_pa_in);

                if (is_array($combinations) && count($combinations)) {
                    foreach ($combinations as $combination) {
                        if (isset($already_done[(int)$combination['id_product'].'_'.(int)$combination['id_product_attribute']])) {
                            continue;
                        }
                        if (is_array($packIdList) && in_array((int)$product['id_product'], $packIdList)) {
                            if (!isset($already_done[(int)$combination['id_product']])) {
                                $productsDataSet[] = $product;
                                $totalProcessedProducts++;
                                $already_done[(int)$combination['id_product']] = true;
                                $almost_one = true;
                            }
                            continue;
                        }
                        if (!in_array((int)$combination['id_attribute_group'], $this->conf['selectedGroups'])) {
                            continue;
                        }
                        if ($checkStock && (int)$combination['quantity'] <= 0) {
                            continue;
                        }
                        if (count($facetedSearchSelectedFilters) > 0) {
                            $doContinue = false;
                            $count_attributes_matched = 0;
                            $product_attributes = pm_productsbyattributes::getAttributeCombinationsById((int)$combination['id_product'], (int)$combination['id_product_attribute'], $idLang);
                            foreach ($product_attributes as $product_attribute) {
                                if (isset($facetedSearchSelectedFilters[(int)$product_attribute['id_attribute_group']][(int)$product_attribute['id_attribute']])) {
                                    $count_attributes_matched++;
                                }
                            }
                            if (count($facetedSearchSelectedFilters) == $count_attributes_matched) {
                                $doContinue = true;
                            }
                        } else {
                            $doContinue = true;
                        }
                        if ($doContinue && ($totalProcessedProducts < $visibleProductsOffsetStart || $totalProcessedProducts > $visibleProductsOffsetEnd)) {
                            $productsDataSet[] = $product;
                            $totalProcessedProducts++;
                            $almost_one = true;
                            $already_done[(int)$combination['id_product'].'_'.(int)$combination['id_product_attribute']] = true;
                            continue;
                        }
                        if ($doContinue && (!$checkStock || ($checkStock && (int)$combination['quantity'] > 0))) {
                            $product['pai_id_product_attribute'] = (int)$combination['id_product_attribute'];
                            $product['cache_default_attribute'] = (int)$combination['id_product_attribute'];
                            $product['id_product_attribute'] = (int)$combination['id_product_attribute'];
                            $product['split-by-spa'] = true;
                            if (!isset($product['name'])) {
                                $product = (new ProductAssembler($globalContext))->assembleProduct($product);
                            }
                            if (!empty($this->conf['changeProductName'])) {
                                $product['product_name'] = pm_productsbyattributes::getFullProductName($product['name'], (int)$product['id_product'], (int)$product['id_product_attribute'], $idLang);
                                $product['id_product_pack'] = 'spa-'.(int)$product['id_product'].'-'.(int)$product['id_product_attribute'];
                            } else {
                                $product['product_name'] = $product['name'];
                            }
                            $product['quantity_sql'] = $combination['quantity'];
                            $product['is_color_group'] = (bool)$combination['is_color_group'];
                            if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
                                $combination_image = pm_productsbyattributes::getBestImageAttribute((int)$globalContext->shop->id, (int)$idLang, (int)$product['id_product'], (int)$product['id_product_attribute']);
                                if (isset($combination_image['id_image'])) {
                                    $product['cover_image_id'] = (int)$combination_image['id_image'];
                                }
                            }
                            $productsDataSet[] = $product;
                            $totalProcessedProducts++;
                            $almost_one = true;
                            $already_done[(int)$combination['id_product'].'_'.(int)$combination['id_product_attribute']] = true;
                        }
                    }
                }
                if (!$almost_one) {
                    $productsDataSet[] = $product;
                    $totalProcessedProducts++;
                }
            }
        } else {
            foreach ($result->getProducts() as $product) {
                if (!empty($this->conf['changeProductName'])) {
                    $product['product_name'] = pm_productsbyattributes::getFullProductName($product['name'], (int)$product['id_product'], (int)$product['id_product_attribute'], $idLang);
                    $product['id_product_pack'] = 'spa-'.(int)$product['id_product'].'-'.(int)$product['id_product_attribute'];
                } else {
                    $product['product_name'] = $product['name'];
                }
                $product['split-by-spa'] = true;
                $productsDataSet[] = $product;
                $totalProcessedProducts++;
            }
        }

        $result->setTotalProductsCount((int)count($productsDataSet));
        $productsDataSet = array_slice($productsDataSet, ((int)$resultsPerPage * ($page - 1)), (int)$resultsPerPage);
        $result->setProducts($productsDataSet);
        return $result;
    }

    public function getIdProductAttributeIn(){
      $id_category = Tools::getValue('id_category');
      $r =false;
      if ($id_category) {
        $id_fs_cat = Configuration::get("GECPS_FS_CAT");
        $id_ps_cat = Configuration::get("GECPS_PS_CAT");


        if ($id_category == $id_fs_cat) {
          $r = $this->getIdProductAttributeInFlashSale();
        }

        $category = new Category($id_category);

        if ($category->id_parent == $id_ps_cat) {
          $r = $this->getIdProductAttributeInPrivateSale();
        }
      }
      return $r;
    }

    public function getIdProductAttributeInFlashSale(){
      $fs_p = gecps::getFlashSaleProductIds();
      $r =false;
      foreach($fs_p as $id_p => $id_pas){
        if ($id_pas) {
          foreach ($id_pas as $key => $id_pa) {
            if ($id_pa) {
              $r .= $id_pa.',';
            }
          }
        }
      }
      if ($r) {
        $r = trim($r,',');
      }
      return $r;
    }


    public function getIdProductAttributeInPrivateSale(){
      $ps_p = gecps::getPrivateSaleProductIds();
      $r = false;
      foreach($ps_p as $id_p => $id_pas){
        if ($id_pas) {
          foreach ($id_pas as $key => $id_pa) {
            if ($id_pa) {
              $r .= $id_pa.',';
            }
          }
        }
      }
      if ($r) {
        $r = trim($r,',');
      }
      return $r;
    }

    protected function getPsFacetedSearchSelectedFilters($provider, ProductSearchResult $result, ProductSearchQuery $query)
    {
        $facetedSearchSelectedFilters = array();
        if ($provider instanceof PrestaShop\Module\FacetedSearch\Product\SearchProvider && !empty($this->providerModule)) {
            $facetCollection = $result->getFacetCollection();
            $facets = $facetCollection->getFacets();
            if (!empty($facets) && is_array($facets)) {
                foreach ($facets as $facet) {
                    if (!$facet->getProperty('id_attribute_group')) {
                        continue;
                    }
                    foreach ($facet->getFilters() as $filter) {
                        if (!$filter->isActive()) {
                            continue;
                        }
                        if (!isset($facetedSearchSelectedFilters[(int)$facet->getProperty('id_attribute_group')])) {
                            $facetedSearchSelectedFilters[(int)$facet->getProperty('id_attribute_group')] = array();
                        }
                        $facetedSearchSelectedFilters[(int)$facet->getProperty('id_attribute_group')][(int)$filter->getValue()] = true;
                    }
                }
            }
        }
        if ($provider instanceof Ps_FacetedsearchProductSearchProvider) {
            require_once _PS_ROOT_DIR_ . '/modules/ps_facetedsearch/src/Ps_FacetedsearchFiltersConverter.php';
            $filtersConverter = new Ps_FacetedsearchFiltersConverter();
            $facetCollectionFromEncodedFacets = $provider->getFacetCollectionFromEncodedFacets($query);
            $facetedSearchFilters = $filtersConverter->getFacetedSearchFiltersFromFacets(
                $facetCollectionFromEncodedFacets->getFacets()
            );
            foreach ($facetedSearchFilters as $key => $filter_values) {
                if (!count($filter_values)) {
                    continue;
                }
                preg_match('/^(.*[^_0-9])/', $key, $res);
                $key = $res[1];
                switch ($key) {
                    case 'id_attribute_group':
                        foreach ($filter_values as $filter_value) {
                            $filter_value_array = explode('_', $filter_value);
                            if (!isset($facetedSearchSelectedFilters[$filter_value_array[0]])) {
                                $facetedSearchSelectedFilters[$filter_value_array[0]] = array();
                            }
                            $facetedSearchSelectedFilters[$filter_value_array[0]][(int)$filter_value_array[1]] = true;
                        }
                        break;
                }
            }
        }
        return $facetedSearchSelectedFilters;
    }
}
