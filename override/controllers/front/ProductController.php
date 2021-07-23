<?php

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Product\ProductExtraContentFinder;
use PrestaShop\PrestaShop\Core\Product\ProductInterface;

class ProductController extends ProductControllerCore
{
  public function displayAjaxRefresh()
  {
      $product = $this->getTemplateVarProduct();
      $minimalProductQuantity = $this->getProductMinimalQuantity($product);
      $isPreview = ('1' === Tools::getValue('preview'));
      $isQuickView = ('1' === Tools::getValue('quickview'));

      if ($isQuickView) {
          $this->setQuickViewMode();
      }

      ob_end_clean();
      header('Content-Type: application/json');
      $this->ajaxRender(Tools::jsonEncode([
          'product_prices' => $this->render('catalog/_partials/product-prices'),
          'product_cover_thumbnails' => $this->render('catalog/_partials/product-cover-thumbnails'),
          'product_customization' => $this->render(
              'catalog/_partials/product-customization',
              [
                  'customizations' => $product['customizations'],
              ]
          ),
          'product_details' => $this->render('catalog/_partials/product-details'),
          'product_variants' => $this->render('catalog/_partials/product-variants'),
          'product_discounts' => $this->render('catalog/_partials/product-discounts'),
          'product_add_to_cart' => $this->render('catalog/_partials/product-add-to-cart'),
          'product_additional_info' => $this->render('catalog/_partials/product-additional-info'),
          'product_images_modal' => $this->render('catalog/_partials/product-images-modal'),
          'product_flags' => $this->render('catalog/_partials/product-flags'),
          'product_url' => $this->context->link->getProductLink(
              $product['id_product'],
              null,
              null,
              null,
              $this->context->language->id,
              null,
              $product['id_product_attribute'],
              false,
              false,
              true,
              $isPreview ? ['preview' => '1'] : []
          ),
          'product_minimal_quantity' => $minimalProductQuantity,
          'product_has_combinations' => !empty($this->combinations),
          'id_product_attribute' => $product['id_product_attribute'],
          'product_title' => $product['title'],
          'is_quick_view' => $isQuickView,
          'reference_to_display' => $product['reference_to_display'],
      ]));
  }

}
