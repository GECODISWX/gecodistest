<?php
class gecpstools2ModuleFrontController extends ModuleFrontController
{
  public $params = array();

  public function initContent()
  {
    //parent::initContent();
    //$this->initParams();
	   $this->checkAction();
    $this->setTemplate('module:gecps/views/templates/front/tools2.tpl');


  }

  public function checkAction(){
    if (isset($_GET['action'])) {
      if (method_exists($this,$_GET['action'])) {
        $this->{$_GET['action']}();
      }
    }

  }

  public function checkProductDiffInXmls(){

    $xml_products_done_md5 = $this->getProductsMd5FromXmlFile(_PS_ROOT_DIR_ . '/download/product_files/done/*.xml');
    $xml_products_md5 = $this->getProductsMd5FromXmlFile(_PS_ROOT_DIR_ . '/download/product_files/*.xml');
    $diff = array();
    $new = array();
    foreach ($xml_products_md5 as $key => $xml_product) {
      $xml_product_md5 = $xml_product['md5'];
      if (isset($xml_products_done_md5[$key])) {
        if ($xml_product_md5 != $xml_products_done_md5[$key]['md5']) {
          $diff[] = $key;
        }
      }
      else {
          $new[] = $key;
      }

    }
var_dump(count($diff));
    foreach ($diff as $key => $diff_ref) {
      if ($key!=1) {
        continue;
      }



      // echo $xml_products_done_md5[$diff_ref]['xml']->asXMl();
      // echo "<br>";
      // echo $xml_products_md5[$diff_ref]['xml']->asXMl();
    }

  }

  public function test(){
    $obj1 = simplexml_load_string('<foo><event url="example.com" /></foo>');
    $obj2 = simplexml_load_string('<foo><event url="another.example.com" /></foo>');
    $obj1_dom = dom_import_simplexml($obj1);
    foreach ($obj2->event as $event) {
        $event_dom = dom_import_simplexml($event);
        $event_dom_copy = $obj1_dom->ownerDocument->importNode($event_dom, true);
        $obj1_dom->appendChild($event_dom_copy);
}

// Check that we have the right output, not trusting var_dump or print_r
// Note that we don't need to convert back to SimpleXML
// - $obj1 and $obj1_dom refer to the same data internally
echo $obj1->asXML();
  }

  public function getProductsMd5FromXmlFile($path){
    $files_done = glob($path);
    $last_file_done = $files_done[count($files_done)-1];
    $xml_product_done = simplexml_load_file($last_file_done);
    $xml_product_done_md5 = array();
    $i = 0;
    foreach ($xml_product_done->Products->Product as $product) {

      if ($i>0) {
        //continue;
      }
      if ($product->Reference->__toString() != '5184') {
        //continue;
      }
      if (count($product->Combinaisons->children()) > 0) {
        $comb_key = 0;
        foreach ($product->Combinaisons->children() as $key => $combinaison){
          unset($product->Combinaisons->Combinaison[$comb_key]->Stock);
          unset($product->Combinaisons->Combinaison[$comb_key]->OnSale);
          unset($product->Combinaisons->Combinaison[$comb_key]->Discount);
          unset($product->Combinaisons->Combinaison[$comb_key]->Price);

          $comb_key++;
        }
      }
      unset($xml_product_done->Products->Product[$i]->Stock);
      unset($xml_product_done->Products->Product[$i]->OnSale);
      unset($xml_product_done->Products->Product[$i]->Discount);
      unset($xml_product_done->Products->Product[$i]->Price);

      if (count($product->Features->children()) > 0) {
        $features = array();
        $key = 0;
        foreach ($product->Features->children() as $feature) {
          $features[$feature->Name->fr->__toString()] = $feature;
          //unset($product->Features->Feature[$key]);
          $key++;
          //var_dump($feature->Name->fr->__toString());

        }
        ksort($features);
        $key = 0;
        $dom_features = dom_import_simplexml($product->Features);
        foreach ($features as $f_key => $feature) {
          $dom_feature = dom_import_simplexml($feature);
          $dom_feature_copy = $dom_features->ownerDocument->importNode($dom_feature, true);
          $dom_features->appendChild($dom_feature_copy);
          $key++;
        }
        foreach ($product->Features->children() as $feature) {
          //var_dump($feature->Name->fr->__toString());

        }
      }



      $xml_product_done_md5[trim($product->Reference)] = array(
        'md5' => md5($product->asXML()),
        'xml' => $product
      );
      // echo $product->Reference;
      // echo md5($product->asXML()).'<br>';
      //echo $product->asXML();
      $i++;
    }
    return $xml_product_done_md5;
  }

  public function getProductsXmlFromFile(){
    $file_string = file_get_contents(_PS_DOWNLOAD_DIR_GECPS_."product_files/export_produits.xml");
    //$file_string = file_get_contents(_PS_DOWNLOAD_DIR_GECPS_."product_files/done/20210511175336-export_produits.xml");
    $invalid_characters = '/[^\x9\xa\x20-\xD7FF\xE000-\xFFFD]/';
    $file_string = preg_replace($invalid_characters, '', $file_string);
    $xml_products = simplexml_load_string($file_string);
    return $xml_products;
  }


  public function importProducts(){

    $xml_products = $this->getProductsXmlFromFile();
    $this->unpdateProductsFromXml($xml_products);
  }

  public function unpdateProductsFromXml($xml_products){

    if (isset($xml_products->Products) && count($xml_products->Products->children()) > 0)
    {
      $fr_c = 0;
      $es_c = 0;
      foreach ($xml_products->Products->Product as $key=> $product) {
        $c = $this->updateProductStatu($product);
        if ($c['fr']) {
          $fr_c++;
        }
        if ($c['es']) {
          $es_c++;
        }
      }
      var_dump($fr_c,$es_c);
    }
  }

  public function updateProductStatu($product){
    $c_shops= array('fr'=>1,'es'=>3);
    $ref = $product->Reference->__toString();
    $id_product = Product::getIdByReference($ref);
    $r = array();
    foreach ($product->Active->Children() as $key => $c) {
      if ($ref != '931') {
        //continue;
      }

      if (isset($c_shops[$key])) {
        $r[$key] = (int)$c;
        //Db::getInstance()->update('product_shop',['active'=>$c],'id_product = '.$id_product." AND id_shop = ".$c_shops[$key]);
      }
    }
    return $r;
  }

  public function updateShippingCost($product){
    $ref = $product->Reference->__toString();
    if ($ref != "938") {
      //return;
    }
    if ($product->AdditionnalShippingCost->__toString()) {
      $additional_shipping_cost = str_replace(',', '.', trim($product->AdditionnalShippingCost));
      $additional_shipping_cost = number_format(floatval($additional_shipping_cost), 2);
      $additional_shipping_cost = (int)str_replace(',', '', $additional_shipping_cost);
    }
    else {
        $additional_shipping_cost = 0;
    }
    Db::getInstance()->update(
      'product',
      ['additional_shipping_cost'=>$additional_shipping_cost],
      'reference = '.$ref
    );

    $id_product = Product::getIdByReference($ref);
    if ($id_product) {
      Db::getInstance()->update(
        'product_shop',
        ['additional_shipping_cost'=>$additional_shipping_cost],
        'id_product = '.$id_product
      );
    }
  }
}
