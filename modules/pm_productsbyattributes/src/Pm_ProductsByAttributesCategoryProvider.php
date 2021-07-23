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

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use Symfony\Component\Translation\TranslatorInterface;
class Pm_ProductsByAttributesCategoryProvider implements ProductSearchProviderInterface
{
    private $module;
    private $translator;
    private $category;
    private $sortOrderFactory;
    public function __construct(
        TranslatorInterface $translator,
        Category $category,
        pm_productsbyattributes $module
    ) {
        $this->module = $module;
        $this->translator = $translator;
        $this->category = $category;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }
    private function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        $config = $this->module->getModuleConfiguration();
        return $this->module->getCategoryProducts(
            (int)$this->category->id,
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay(),
            $type !== 'products',
            !empty($config['fullTree'])
        );
    }
    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $products = $this->getProductsOrCount($context, $query, 'products');
        $count = $this->getProductsOrCount($context, $query, 'count');
        $result = new ProductSearchResult();
        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);
            $result->setAvailableSortOrders(
                $this->sortOrderFactory->getDefaultSortOrders()
            );
        }
        return $result;
    }
}
