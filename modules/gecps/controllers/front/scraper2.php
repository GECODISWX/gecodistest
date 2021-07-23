<?php
if (!class_exists('simple_html_dom_node')) {
  include(_PS_MODULE_DIR_."gecps/classes/simple_html_dom.php");
}
include(_PS_MODULE_DIR_."iqitelementor/src/IqitElementorCategory.php");

class gecpsscraper2ModuleFrontController extends ModuleFrontController
{
  public $params;

  public function initContent()
  {
    //parent::initContent();
    $this->initParams();
	  $this->checkAction();
    $this->setTemplate('module:gecps/views/templates/front/scraper2.tpl');


  }

  public function initParams(){
    $this->params =array(

    );
  }

  public function checkAction(){
    if (isset($_GET['action'])) {
      if (method_exists($this,$_GET['action'])) {
        $this->{$_GET['action']}();
      }
    }

  }

  public function getPromoProductsByCategory($id_category,$id_shop,$limit){
    $sql = "SELECT DISTINCT sp.id_product FROM `ps_specific_price` sp, ps_category_product cp, ps_product_shop ps, ps_product p
            WHERE cp.id_product = sp.id_product
            AND cp.id_category = $id_category
            AND sp.id_product = ps.id_product
            AND ps.active =1
            AND ps.id_shop=$id_shop
            AND sp.id_shop = $id_shop
            AND p.id_product = sp.id_product
            ORDER BY p.reference desc LIMIT $limit";
    $r =  Db::getInstance()->executeS($sql);
    return $r;

  }

  public function getFlashSaleProductsByCategory($id_category,$id_shop,$limit){
    $sql = "SELECT DISTINCT sp.id_product FROM `ps_specific_price` sp, ps_category_product cp, ps_product_shop ps, ps_product p
            WHERE cp.id_product = sp.id_product
            AND cp.id_category = $id_category
            AND sp.id_product = ps.id_product
            AND ps.active =1
            AND ps.id_shop=$id_shop
            AND sp.id_shop = $id_shop
            AND p.id_product = sp.id_product
            AND sp.from <= CURRENT_TIMESTAMP
            AND sp.to >= CURRENT_TIMESTAMP
            ORDER BY p.reference desc LIMIT $limit";
    $r =  Db::getInstance()->executeS($sql);
    return $r;
  }

  public function getBestSellerProductsByCategory($id_category,$id_shop,$limit){
    $sql = "SELECT ps.id_product FROM `ps_product_sale` ps, ps_category_product cp
            WHERE ps.id_product = cp.id_product
            AND cp.id_category = 367
            ORDER BY ps.sale_nbr DESC LIMIT $limit";
    $r =  Db::getInstance()->executeS($sql);
    return $r;
  }

  public function getFreeShippingProductsByCategory($id_category,$id_shop,$limit){
    $freeshipping_id_cat = Configuration::get("GECPS_FREESHIPPING_CAT",null,null,$id_shop);
    $sql ="SELECT DISTINCT ps.id_product FROM ps_product_shop ps,ps_category_product cp
            WHERE ps.id_product = cp.id_product
            AND cp.id_category = $id_category
            AND ps.id_product IN (
            	SELECT DISTINCT ps.id_product FROM ps_product_shop ps,ps_category_product cp
            	WHERE ps.id_product = cp.id_product
                AND cp.id_category = $freeshipping_id_cat
            )
            and ps.active =1
            ORDER BY ps.id_product DESC LIMIT $limit";
    $r =  Db::getInstance()->executeS($sql);
    return $r;
  }

  public function getIdProductsFromList($r){
    $id_products = [];
    foreach ($r as $key => $l) {
      $id_products[]=$l['id_product'];
    }
    return $id_products;
  }

  public function makeUniversTabProductSelection(){
    $sql = "SELECT c.id_category,iecl.* FROM `ps_iqit_elementor_category_lang` iecl, ps_iqit_elementor_category iec, ps_category c
            WHERE iecl.id_elementor = iec.id_elementor
            AND c.id_category = iec.id_category
            AND c.id_parent = 2
            AND iecl.data not in ('','[]')";
    $r = Db::getInstance()->executeS($sql);

    foreach ($r as $key => $l) {
      // if ($key>0) {
      //   continue;
      // }
      if ($l['id_category']!=367 || $l['id_shop']!=3) {
        //continue;
      }

      $id_langs = Language::getLanguages(true,$l['id_shop'],true);
      if (!in_array($l['id_lang'],$id_langs)) {
        continue;
      }

      $elementor = new IqitElementorCategory($l['id_elementor'],$l['id_lang'],$l['id_shop']);
      $data = json_decode($l['data'],true);
      if (!isset($data[0]['elements'][0]['elements'][0]['settings']['tabs'])) {
        continue;
      }
      $tabs_data = $data[0]['elements'][0]['elements'][0]['settings']['tabs'];
      foreach ($tabs_data as $key2 => $tab_data) {
        $tab_data['products_limit']=12;
        $id_products =[];
        if ($key2==0) {//promo
          $r2 = $this->getPromoProductsByCategory($l['id_category'],$l['id_shop'],$tab_data['products_limit']);
          $id_products = $this->getIdProductsFromList($r2);

        }
        elseif($key2==1) {//flash sale
          $r2 = $this->getFlashSaleProductsByCategory($l['id_category'],$l['id_shop'],$tab_data['products_limit']);
          $id_products = $this->getIdProductsFromList($r2);

        }
        elseif($key2==2) {//free shipping
          $r2 = $this->getFreeShippingProductsByCategory($l['id_category'],$l['id_shop'],$tab_data['products_limit']);
          $id_products = $this->getIdProductsFromList($r2);
        }
        elseif($key2==3) {//best seller
          $r2 = $this->getBestSellerProductsByCategory($l['id_category'],$l['id_shop'],$tab_data['products_limit']);
          $id_products = $this->getIdProductsFromList($r2);
        }

        $data[0]['elements'][0]['elements'][0]['settings']['tabs'][$key2]['products_ids']=$id_products;

      }

      $elementor->data = json_encode($data);
      $elementor->save();

    }
  }

  public function test(){

  }

}
