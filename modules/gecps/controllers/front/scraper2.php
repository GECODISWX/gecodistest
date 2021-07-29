<?php
if (!class_exists('simple_html_dom_node')) {
  include(_PS_MODULE_DIR_."gecps/classes/simple_html_dom.php");
}
include(_PS_MODULE_DIR_."iqitelementor/src/IqitElementorCategory.php");
include(_PS_MODULE_DIR_."iqitelementor/src/IqitElementorLanding.php");

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
      'fo_base' => 'https://www.habitatetjardin.com',
      'sitemap_url' => 'https://www.habitatetjardin.com/plan.html',
      'c_shops' => array('fr'=>1,'es'=>3),
      'iso3'=>['FRA'=>8,'ESP'=>6],
      'iso'=>['FRA'=>8,'ESP'=>6]
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
            AND cp.id_category = $id_category
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

  public function getBestSellerProductsByCategory2($cats,$id_shop,$limit){
    $id_products = [];
    foreach ($cats as $key => $cat) {
      $sql = "select DISTINCT psa.id_product from ps_product_sale psa, ps_product_shop psh where psa.id_product in
              (SELECT id_product from ps_category_product where id_category = $cat)
              AND psh.id_product = psa.id_product
              AND psh.active = 1
              AND psh.id_shop=$id_shop
              Order by sum desc LIMIT 2";
      $r = Db::getInstance()->executeS($sql);var_dump($cat,$r);
      foreach ($r as $key => $l) {
        if (!in_array($l['id_product'],$id_products)) {
          $id_products[]=$l['id_product'];
          break;
        }
      }
    }
    // var_dump($id_products);
    return $id_products;
  }

  public function makeHomeTabProductSelection(){
    $c_shops=$this->params['c_shops'];
    foreach ($c_shops as $c => $id_shop) {
      $i = Configuration::get("iqit_homepage_layout",null,null,$id_shop);
      $cats = explode(',',Configuration::get("GECPS_HOME_TAB_CATS",null,null,$id_shop));
      $id_langs = Language::getLanguages(true,$id_shop,true);
      $id_lang = $id_langs[0];
      $elementor = new IqitElementorLanding($i,$id_lang);
      $data = json_decode($elementor->data,true);
      if (!isset($data[1]['elements'][0]['elements'][0]['settings']['tabs'])) {
        continue;
      }
      if (strpos('home_tab_section',$data[1]['settings']['css_classes'])===false) {
        continue;
      }
      $tabs_data = $data[1]['elements'][0]['elements'][0]['settings']['tabs'];
      foreach ($tabs_data as $key2 => $tab_data) {
        if ($tab_data['product_source']!='ms') {
          continue;
        }
        $id_products = $this->getBestSellerProductsByCategory2($cats,$id_shop,$tab_data['products_limit']);
        $data[1]['elements'][0]['elements'][0]['settings']['tabs'][$key2]['products_ids']=$id_products;
      }
      $elementor->data = json_encode($data);
      $elementor->save();

    }

  }

}
