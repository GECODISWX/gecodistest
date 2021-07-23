<?php
class gecpstoolsModuleFrontController extends ModuleFrontController
{
  public $params = array();

  public function initContent()
  {
    //parent::initContent();
    $this->initParams();
	   $this->checkAction();
    $this->setTemplate('module:gecps/views/templates/front/tools.tpl');


  }

  public function checkAction(){
    if (isset($_GET['action'])) {
      if (method_exists($this,$_GET['action'])) {
        $this->{$_GET['action']}();
      }
    }
    if (isset($_POST['action'])) {
      if (method_exists($this,$_POST['action'])) {
        $this->{$_POST['action']}();
      }
    }

  }

  public function initParams(){
    $this->params = array(
      'flag_path' => _PS_MODULE_DIR_.'gecps/product_importing.flag',
    );
  }

  public function downloadStockFile(){
    $this->downloadStockFileByLang('fr');
    $this->downloadStockFileByLang('es');
  }

  public function downloadStockFileByLang($lang_code='fr'){
    $url = "http://bo.habitatetjardin.com/DEX/prestashop/export_stock_$lang_code.xml";
    $stock_file_dir = _PS_DOWNLOAD_DIR_GECPS_.'stock_files/';
    //$stock_file_dir = _PS_ROOT_DIR_.'/export_produits/';
    $file_name = $stock_file_dir.basename($url);
    if (file_exists($file_name)) {
      $copy_path = $stock_file_dir.'done/'.date('YmdHis').'-'.basename($url);
      copy($file_name,$copy_path);
      $sh_str_remove = "find ".$stock_file_dir.'done/'."* -type f -mtime +7 -exec rm {} \;";
      exec($sh_str_remove);
    }
    if(file_put_contents( $file_name,file_get_contents($url))) {
        echo "Stock File $lang_code downloaded successfully.<br>";
    }
    else {
        echo "Stock File $file_name downloading failed.<br>";
    }
  }

  public function downloadProductFile(){
    $url = "http://bo.habitatetjardin.com/DEX/prestashop/fichier/export_produits.xml";
    $stock_file_dir = _PS_DOWNLOAD_DIR_GECPS_.'product_files/';
    //$stock_file_dir = _PS_ROOT_DIR_.'/export_produits/';
    $file_name = $stock_file_dir.basename($url);
    if (file_exists($file_name)) {
      $copy_path = $stock_file_dir.'done/'.date('YmdHis').'-'.basename($url);
      copy($file_name,$copy_path);
      $sh_str_remove = "find ".$stock_file_dir.'done/'."* -type f -mtime +7 -exec rm {} \;";
      exec($sh_str_remove);
    }
    if(file_put_contents( $file_name,file_get_contents($url))) {
        echo "Product File downloaded successfully.<br>";
    }
    else {
        echo "Product File downloading failed.<br>";
    }
  }


  public function importProductsStocks(){
    $this->importProductsStocksByLang('fr');
  }

  public function importProductsStocksByLang($lang_code = 'fr'){

  }

  public function checkAndSetProductImportFlag(){
    $flag_path = $this->params['flag_path'];
    if (file_exists($flag_path)) {
      if (isset($_GET['force']) && $_GET['force']) {
        if (file_exists($flag_path)) {
          unlink($flag_path);
        }

      }
      else {
        echo 'Another script is still running...';
        die();
      }

    }
    else {
      file_put_contents($flag_path,' ');
    }
  }

  public function removeProductImportFlag(){
    if (file_exists($this->params['flag_path'])) {
      unlink($this->params['flag_path']);
    }
  }

  public function getProductsXmlFromFile(){
    $file_string = file_get_contents(_PS_DOWNLOAD_DIR_GECPS_."product_files/export_produits.xml");
    $invalid_characters = '/[^\x9\xa\x20-\xD7FF\xE000-\xFFFD]/';
    $file_string = preg_replace($invalid_characters, '', $file_string);
    $xml_products = simplexml_load_string($file_string);
    return $xml_products;
  }

  public function addCData($name, $value, &$parent) {
   $child = $parent->addChild($name);

   if ($child !== NULL) {
     $child_node = dom_import_simplexml($child);
     $child_owner = $child_node->ownerDocument;
     $child_node->appendChild($child_owner->createCDATASection($value));
   }

   return $child;
 }

  public function copyImg($id_entity, $id_image, $url, $entity = 'products', $regenerate = false) {
      $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
      $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));


      switch ($entity) {
          default:
          case 'products':
              $image_obj = new Image($id_image);
              $path = $image_obj->getPathForCreation();
              break;
          case 'categories':
              $path = _PS_CAT_IMG_DIR_ . (int) $id_entity;
              break;
          case 'manufacturers':
              $path = _PS_MANU_IMG_DIR_ . (int) $id_entity;
              break;
          case 'suppliers':
              $path = _PS_SUPP_IMG_DIR_ . (int) $id_entity;
              break;
      }
      $url = str_replace(' ', '%20', trim($url));


      // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
      if (!ImageManager::checkImageMemoryLimit($url))
          return false;


      // 'file_exists' doesn't work on distant file, and getimagesize makes the import slower.
      // Just hide the warning, the processing will be the same.
      if (Tools::copy($url, $tmpfile)) {
          ImageManager::resize($tmpfile, $path . '.jpg');
          $images_types = ImageType::getImagesTypes($entity);


          if ($regenerate)
              foreach ($images_types as $image_type) {
                  ImageManager::resize($tmpfile, $path . '-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height']);
                  if (in_array($image_type['id_image_type'], $watermark_types))
                      Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
              }
      }
      else {
          unlink($tmpfile);
          return false;
      }
      unlink($tmpfile);
      return true;
  }

  public function getProductToUpdate($product){
    $update_product = false;
    if ($product->Reference->__toString() && strlen(trim($product->Reference)) <= 64) {
      $update_product = Db::getInstance()->getValue('
        SELECT id_product
        FROM '._DB_PREFIX_.'product
        WHERE reference = \''.pSql(trim($product->Reference)).'\'
      ');
    } elseif (count($product->Combinaisons->children()) > 0) {
      foreach ($product->Combinaisons->children() as $combinaison) {
        if ($combinaison->Reference->__toString() && strlen(trim($combinaison->Reference)) <= 64) {
          $update_product = Db::getInstance()->getValue('
            SELECT id_product
            FROM '._DB_PREFIX_.'product_attribute
            WHERE reference = \''.pSql(trim($combinaison->Reference)).'\'
          ');
          break;
        }
      }
    }

    if (!$update_product && !$product->Reference->__toString()) {
      if (count($product->Combinaisons->children()) > 0) {
        $stop = false;
        foreach ($product->Combinaisons->children() as $combinaison) {
          if ($combinaison->Reference->__toString() && strlen(trim($combinaison->Reference)) <= 64) {
            $stop = true;
          }
        }
        if (!$stop) {
          $this->go_next_product = 1;

        }
      } else {
        $this->go_next_product = 1;

      }
    }
    return $update_product;
  }

  public function getNewProductByUpdateProduct($update_product){
    if (!$update_product) {
      $newProduct = new Product();
    } else {
      $newProduct = new Product($update_product);
    }
    return $newProduct;
  }

  public function setManufacturer($product,&$newProduct,$shops){
    if ($product->Manufacturer->__toString()) {
      if ($manufacturer = Manufacturer::getIdByName(str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($product->Manufacturer), 0, 64, "utf-8")))) {
        $newProduct->id_manufacturer = (int)$manufacturer;
      } else {
        $newManufacturer = new Manufacturer();
        $newManufacturer->name = str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($product->Manufacturer), 0, 64, "utf-8"));
        $newManufacturer->active = 1;
        $newManufacturer->save();

        $newManufacturer->associateTo($shops);

        if ($newManufacturer->id && $newManufacturer->name) {
          $newProduct->id_manufacturer = (int)$newManufacturer->id;
        } else {
          $newProduct->id_manufacturer = 0;
        }
      }
    }
  }

  public function setSupplier($product,&$newProduct,$shops){
    if ($product->Supplier->__toString()) {
      if ($supplier = Supplier::getIdByName(str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($product->Supplier), 0, 64, "utf-8")))) {
        $newProduct->id_supplier = (int)$supplier;
      } else {
        $newSupplier = new Supplier();
        $newSupplier->name = str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($product->Supplier), 0, 64, "utf-8"));
        $newSupplier->active = 1;
        $newSupplier->save();

        $newSupplier->associateTo($shops);

        if ($newSupplier->id && $newSupplier->name) {
          $newProduct->id_supplier = (int)$newSupplier->id;
        } else {
          $newProduct->id_supplier = 0;
        }
      }
    }
  }

  public function getProductCategories($product,&$newProduct,$shops){
    $categories_to_add = array();
    if (count($product->Categories->Category) > 0) {
      foreach ($product->Categories->Category as $value) {
        $id_parent = 2;
        $name_parent = '';

        $breadcrumbs = $value->Breadcrumb;
        $breadcrumb_lang = array();
        foreach ($breadcrumbs->children() as $iso => $breadcrumb) {
          $breadcrumb_lang[$iso] = explode(">", $breadcrumb);//var_dump($breadcrumb_lang[$iso]);
        }

        $lastKey = array_search(end($breadcrumb_lang['fr']), $breadcrumb_lang['fr']);
        foreach ($breadcrumb_lang['fr'] as $key => $name_category) {
          if ($category = Category::searchByName(1, str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($name_category), 0, 128, "utf-8")), true, true)) {
            if (str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($name_category), 0, 128, "utf-8")) != $name_parent) {
              $category_names = array();
              $category_links = array();
              foreach ($breadcrumb_lang as $iso => $categories) {
                $id_lang = Language::getIdByIso($iso);
                if (!isset($categories[$key])) {
                  $categories[$key] = $categories[$key-1];
                }
                $category_names[(int)$id_lang] = str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($categories[$key]), 0, 128, "utf-8"));
                $rewrite = Tools::str2url(str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($categories[$key]), 0, 128, "utf-8")));
                $category_links[(int)$id_lang] = $rewrite;
              }

              $objCategory = new Category((int)$category['id_category']);
              $objCategory->id_parent = (int)$id_parent;
              $objCategory->name = $category_names;
              $objCategory->link_rewrite = $category_links;
              $objCategory->update();
              $objCategory->associateTo($shops);

              $id_parent = (int)$category['id_category'];
              $name_parent = str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($name_category), 0, 128, "utf-8"));
            }

            // if ($key == $lastKey) {
            if (1) {
              $categories_to_add[] = (int)$category['id_category'];
              if ($value->Default == 1) {
                $newProduct->id_category_default = (int)$category['id_category'];
              }
            }
          } else {
            $category_names = array();
            $category_links = array();
            foreach ($breadcrumb_lang as $iso => $categories) {
              $id_lang = Language::getIdByIso($iso);
              if (!isset($categories[$key])) {
                $categories[$key] = $categories[$key-1];
              }
              $category_names[(int)$id_lang] = str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($categories[$key]), 0, 128, "utf-8"));
              $rewrite = Tools::str2url(str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($categories[$key]), 0, 128, "utf-8")));
              $category_links[(int)$id_lang] = $rewrite;
            }

            $newCategory = new Category();
            $newCategory->active = 1;
            $newCategory->id_parent = (int)$id_parent;
            $newCategory->name = $category_names;
            $newCategory->link_rewrite = $category_links;
            $newCategory->add();

            $newCategory->associateTo($shops);

            $id_parent = (int)$newCategory->id;
            $name_parent = str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($name_category), 0, 128, "utf-8"));

            // if ($key == $lastKey) {
            if (1) {
              $categories_to_add[] = (int)$newCategory->id;
              if ($value->Default == 1) {
                $newProduct->id_category_default = (int)$newCategory->id;
              }
            }
          }
        }

      }
    }
    return array_unique($categories_to_add);
  //  $newProduct->addToCategories($categories_to_add);
  }

  public function setProductCategories(&$newProduct,$categories_to_add){
    $newProduct->addToCategories($categories_to_add);
  }

  public function getProductNames($product){
    $product_names = array();
    if (count($product->Name->children()) > 0) {
      foreach ($product->Name->children() as $iso => $name) {
        $id_lang = Language::getIdByIso($iso);
        $product_names[(int)$id_lang] = str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($name), 0, 128, "utf-8"));
      }
    }
    return $product_names;
  }

  public function setProductNames($product,&$newProduct,$shops){
    $product_names = $this->getProductNames($product);
    if (count($product->Name->children()) > 0) {
      $newProduct->name = $product_names;
    }
  }

  public function setProductDescriptions($product,&$newProduct,$shops){
    $product_descriptions = array();
    if (count($product->Description->children()) > 0) {
      foreach ($product->Description->children() as $iso => $description) {
        $id_lang = Language::getIdByIso($iso);
        // $description = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $description);
        if (preg_match('/<[\s]*(i?frame|form|input|embed|object)/ims', $description->__toString())) {
          $product_descriptions[(int)$id_lang] = '';
        } else {
          $product_descriptions[(int)$id_lang] = $description->__toString();
        }
      }

      $newProduct->description = $product_descriptions;
    }
  }

  public function setProductShortDescriptions($product,&$newProduct,$shops){
    $product_shortdescriptions = array();
    if (count($product->ShortDescription->children()) > 0) {
      foreach ($product->ShortDescription->children() as $iso => $shortdescription) {
        $id_lang = Language::getIdByIso($iso);
        // $shortdescription = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $shortdescription);
        if (preg_match('/<[\s]*(i?frame|form|input|embed|object)/ims', $shortdescription->__toString())) {
          $product_shortdescriptions[(int)$id_lang] = '';
        } else {
          $product_shortdescriptions[(int)$id_lang] = $shortdescription->__toString();
        }
      }

      $newProduct->description_short = $product_shortdescriptions;
    }
  }

  public function setProductMunimalQuantity($product,&$newProduct,$shops){
    if ($product->MinimalQuantity->__toString()) {
      $newProduct->minimal_quantity = (int)$product->MinimalQuantity;
    }
  }

  public function setProductAvailableNow($product,&$newProduct,$shops){
    $product_available_now = array();
    if (count($product->TextInStock->children()) > 0) {
      foreach ($product->TextInStock->children() as $iso => $textinstock) {
        $id_lang = Language::getIdByIso($iso);
        $textinstock = strip_tags($textinstock);
        $textinstock = str_replace(array("<", ">", "=", "{", "}"), '', $textinstock);
        $product_available_now[(int)$id_lang] = mb_substr($textinstock, 0, 255, "utf-8");
      }

      $newProduct->available_now = $product_available_now;
    }
  }

  public function setProductAvailableLater($product,&$newProduct,$shops){
    $product_available_later = array();
    if (count($product->TextOutOfStock->children()) > 0) {
      foreach ($product->TextOutOfStock->children() as $iso => $textoutofstock) {
        $id_lang = Language::getIdByIso($iso);
        $textoutofstock = strip_tags($textoutofstock);
        $textoutofstock = str_replace(array("<", ">", "=", "{", "}"), '', $textoutofstock);
        $product_available_later[(int)$id_lang] = mb_substr($textoutofstock, 0, 255, "utf-8");
      }

      $newProduct->available_later = $product_available_later;
    }
  }

  public function setProductAdditionnalShippingCost($product,&$newProduct,$shops){
    if ($product->AdditionnalShippingCost->__toString()) {
      $additional_shipping_cost = str_replace(',', '.', trim($product->AdditionnalShippingCost));
      $additional_shipping_cost = number_format(floatval($additional_shipping_cost), 2);
      $newProduct->additional_shipping_cost = (int)str_replace(',', '', $additional_shipping_cost);
    }
  }

  public function setProductWholesalePrice($product,&$newProduct,$shops){
    if ($product->WholesalePrice->__toString()) {
      $product_wholesale_price = str_replace(',', '.', trim($product->WholesalePrice));
      $product_wholesale_price = number_format(floatval($product_wholesale_price), 6);
      $newProduct->wholesale_price = str_replace(',', '', $product_wholesale_price);
    }
  }

  public function setProductPrice($product,&$newProduct,$shops){
    if (count($product->Combinaisons->children()) > 0 || !$product->Price->__toString()) {
      $newProduct->price = number_format(0, 6);
    } else {
      $product_price = str_replace(',', '.', trim($product->Price));
      $product_price = number_format(floatval($product_price), 6);
      $newProduct->price = str_replace(',', '', $product_price);
      // if (isset($product->Ecotax)) {
      //   $product_ecotax = str_replace(',', '.', trim($product->Ecotax->__toString()));
      //   $product_ecotax = number_format(floatval($product_ecotax), 6);var_dump($product_ecotax);
      //   $newProduct->price -= $product_ecotax;
      // }
    }


    if ($product->OnSale->__toString()) {
      $newProduct->on_sale = (int)$product->OnSale;
    }

    if ($product->ShowPrice->__toString()) {
      $newProduct->show_price = (int)$product->ShowPrice;
    }
  }

  public function setProductOnlineOnly($product,&$newProduct,$shops){
    if ($product->OnlineOnly->__toString()) {
      if (trim(ucfirst($product->OnlineOnly)) == 'Vrai') {
        $newProduct->online_only = 0;
      } elseif (trim(ucfirst($product->OnlineOnly)) == 'Faux') {
        $newProduct->online_only = 0;
      } else {
        // $newProduct->online_only = (int)$product->OnlineOnly;
        $newProduct->online_only = 0;
      }
    }
  }

  public function setProductUnity($product,&$newProduct,$shops){
    if ($product->Unity->__toString()) {
      $newProduct->unity = mb_substr(trim($product->Unity), 0, 255, "utf-8");
    }

    if ($product->UnitPrice->__toString()) {
      $newProduct->unit_price = trim($product->UnitPrice);
    }
  }

  public function setProductEcoTax($product,&$newProduct,$shops){
    if (isset($product->Ecotax)) {
      $product_ecotax = str_replace(',', '.', trim($product->Ecotax));
      $product_ecotax = number_format(floatval($product_ecotax), 6);
      //$newProduct->ecotax = str_replace(',', '', $product_ecotax);
      $newProduct->ecotax =0;
    }
  }

  public function setProductReference($product,&$newProduct,$shops){
    $newProduct->reference = trim($product->Reference);
  }

  public function setProductSupplierReference($product,&$newProduct,$shops){
    $newProduct->supplier_reference = mb_substr(trim($product->SupplierReference), 0, 64, "utf-8");
  }

  public function setProductShippingInfos($product,&$newProduct,$shops){
    if ($product->Width->__toString()) {
      $product_width = str_replace(',', '.', trim($product->Width));
      $product_width = number_format(floatval($product_width), 6);
      $newProduct->width = str_replace(',', '', $product_width);
    }
    if ($product->Height->__toString()) {
      $product_height = str_replace(',', '.', trim($product->Height));
      $product_height = number_format(floatval($product_height), 6);
      $newProduct->height = str_replace(',', '', $product_height);
    }
    if ($product->Depth->__toString()) {
      $product_depth = str_replace(',', '.', trim($product->Depth));
      $product_depth = number_format(floatval($product_depth), 6);
      $newProduct->depth = str_replace(',', '', $product_depth);
    }
    if ($product->Weight->__toString()) {
      $product_weight = str_replace(',', '.', trim($product->Weight));
      $product_weight = number_format(floatval($product_weight), 6);
      $newProduct->weight = str_replace(',', '', $product_weight);
    }
    if ($product->EAN13->__toString()) {
      $newProduct->ean13 = mb_substr(trim($product->EAN13), 0, 13);
    }
    if ($product->UPC->__toString()) {
      $newProduct->upc = mb_substr(trim($product->UPC), 0, 12);
    }
  }

  public function setProductLinks($product,&$newProduct,$shops){
    $product_links = array();
    $product_names = $this->getProductNames($product);
      foreach($shops as $is_shop){
        $id_langs = Language::getLanguages(true,$is_shop,true);
        foreach ($id_langs as $key => $id_lang) {
          $urlrewrite = Tools::str2url($product_names[(int)$id_lang]);
          $product_links[(int)$id_lang] = mb_substr(trim($urlrewrite), 0, 128, "utf-8");
        }
      }

      $newProduct->link_rewrite = $product_links;

  }

  public function setProductMeta($product,&$newProduct,$shops){
    if (count($product->MetaDescription->children()) > 0) {
      foreach ($product->MetaDescription->children() as $iso => $metadescription) {
        $id_lang = Language::getIdByIso($iso);
        // $metadescription = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $metadescription);
        $metadescription = strip_tags($metadescription);
        $metadescription = str_replace(array("<", ">", "=", "{", "}"), '', $metadescription);
        $product_metadescriptions[(int)$id_lang] = mb_substr(trim($metadescription), 0, 512, "utf-8");
      }

      $newProduct->meta_description = $product_metadescriptions;
    }
    $product_metatitles = array();
    if (count($product->MetaTitle->children()) > 0) {
      foreach ($product->MetaTitle->children() as $iso => $metatitle) {
        $id_lang = Language::getIdByIso($iso);
        // $metatitle = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $metatitle);
        $metatitle = str_replace(array("<", ">", "=", "{", "}"), '', $metatitle);
        $product_metatitles[(int)$id_lang] = mb_substr(trim($metatitle), 0, 128, "utf-8");
      }

      $newProduct->meta_title = $product_metatitles;
    }

  }

  public function SetProductActive($product,&$newProduct,$shops){
    $newProduct->active = 1;
    if ($product->AvailableForOrder->__toString()) {
      $newProduct->available_for_order = (int)$product->AvailableForOrder;
    }
    if ($product->AvailableDate->__toString()) {
      $datetime_available = new DateTime();
      $date = $datetime_available->createFromFormat('d/m/Y', trim($product->AvailableDate));
      $availabledate = $date->format('Y-m-d');
      $newProduct->available_date = $availabledate;
    }

    if ($product->Condition->__toString()) {
      $newProduct->condition = trim($product->Condition);
    } else {
      $newProduct->condition = 'new';
    }
  }

  public function SetProductOthers($product,&$newProduct,$shops){
    $newProduct->low_stock_alert = 0;
    $newProduct->quantity_discount = 0;
    $newProduct->customizable = 0;
    $newProduct->uploadable_files = 0;
    $newProduct->text_fields = 0;
    $newProduct->redirect_type = '302-category';
    $newProduct->id_type_redirected = 0;
    $newProduct->show_condition = 0;
    $newProduct->indexed = 1;
    $newProduct->visibility = 'both';
    $newProduct->state = 1;
    $newProduct->out_of_stock = 2;
    $newProduct->cache_is_pack = 0;
    $newProduct->cache_has_attachments = 0;
    $newProduct->is_virtual = 0;
    $newProduct->cache_default_attribute = 0;

    if ($product->TaxRulesId->__toString()) {
      $newProduct->id_tax_rules_group = (int)$product->TaxRulesId;
    }
  }

  public function saveProduct($product,&$newProduct,$update_product,$shops){
    foreach($shops as $shop){
      Shop::setContext(Shop::CONTEXT_SHOP, (int)$shop);
      if ($shop == 1) {
        $newProduct->id_tax_rules_group = 1;
      }
      elseif($shop == 3) {
        $newProduct->id_tax_rules_group = 4;
        //$this->setProductActiveByShop($product,$newProduct,$shop);
      }
      $newProduct->save();
      // if (!$update_product) {
      //   $newProduct->add();
      // } else {
      //   $newProduct->update();
      // }
    }
    Shop::setContext(Shop::CONTEXT_SHOP, $shops[0]);

  }

  public function setProductActiveByShop($product,&$newProduct,$shop){
    $cats_excl = Configuration::get("GECPS_CATS_EXCL",null,null,$shop);
    if ($cats_excl) {
      $sql = "SELECT p.id_product FROM ps_product p, ps_category_product cp where p.id_product = cp.id_product and cp.id_category in ($cats_excl) and p.reference = '".trim($newProduct->reference)."' ";
      $r = Db::getInstance()->executeS($sql);
      if ($r) {
        $newProduct->active = 0;
      }
    }
  }

  public function addProductToShops($product,&$newProduct,$shops){
    foreach ($shops as $shop) {
      $exists = Db::getInstance()->getValue('
        SELECT `id_product`
        FROM `'._DB_PREFIX_.'product_shop`
        WHERE `id_product` = '.(int)$newProduct->id.'
        AND `id_shop` = '.(int)$shop
      );
      if (!$exists) {
        Db::getInstance()->execute('
          INSERT INTO '._DB_PREFIX_.'product_shop
          (
            id_product,
            id_shop,
            id_category_default,
            id_tax_rules_group,
            on_sale,
            online_only,
            ecotax,
            minimal_quantity,
            price,
            wholesale_price,
            additional_shipping_cost,
            active,
            redirect_type,
            available_for_order,
            available_date,
            show_condition,
            show_price,
            indexed,
            visibility,
            cache_default_attribute,
            date_add,
            date_upd
          )
          VALUES
          (
            '.(int)$newProduct->id.',
            '.(int)$shop.',
            '.(int)$newProduct->id_category_default.',
            '.(int)$newProduct->id_tax_rules_group.',
            '.(int)$newProduct->on_sale.',
            '.(int)$newProduct->online_only.',
            '.(float)$newProduct->ecotax.',
            '.(int)$newProduct->minimal_quantity.',
            '.(float)$newProduct->price.',
            '.(float)$newProduct->wholesale_price.',
            '.(float)$newProduct->additional_shipping_cost.',
            '.(int)$newProduct->active.',
            \''.pSQL($newProduct->redirect_type).'\',
            '.(int)$newProduct->available_for_order.',
            \''.pSQL($newProduct->available_date).'\',
            '.(int)$newProduct->show_condition.',
            '.(int)$newProduct->show_price.',
            '.(int)$newProduct->indexed.',
            \''.pSQL($newProduct->visibility).'\',
            '.(int)$newProduct->cache_default_attribute.',
            \''.pSQL($newProduct->date_add).'\',
            \''.pSQL($newProduct->date_upd).'\'
          )
        ');
      }
    }

    $shop_fr = 1;
    $shop_es = 3;

    if (count($product->Active->children()) > 0) {
      if ($product->Active->fr->__toString() == '0' || $product->Active->fr->__toString() == '1') {
        Db::getInstance()->execute('
          UPDATE '._DB_PREFIX_.'product_shop
          SET active = '.(int)$product->Active->fr.'
          WHERE id_product = '.(int)$newProduct->id.'
          AND id_shop = '.$shop_fr
        );
      }

      if ($product->Active->es->__toString() == '0' || $product->Active->es->__toString() == '1') {
        Db::getInstance()->execute('
          UPDATE '._DB_PREFIX_.'product_shop
          SET active = '.(int)$product->Active->es.'
          WHERE id_product = '.(int)$newProduct->id.'
          AND id_shop = '.$shop_es
        );
      }
    }
  }

  public function SetProductTags($product,&$newProduct,$shops){
    if (count($product->Tags->children()) > 0) {
      foreach ($product->Tags->children() as $iso => $tags) {
        $id_lang = Language::getIdByIso($iso);
        Tag::addTags($id_lang, $newProduct->id, $tags);
      }
    }

    if (count($product->MetaKeywords->children()) > 0) {
      foreach ($product->MetaKeywords->children() as $iso => $metakeywords) {
        $id_lang = Language::getIdByIso($iso);
        Tag::addTags($id_lang, $newProduct->id, $metakeywords);
      }
    }
  }

  public function setProductImages($product,&$newProduct,$update_product,$shops){
    if (count($product->Images->children()) > 0 && (!$update_product || (int)$product->DeleteExistingImages->__toString() == 1)) {
      $newProduct->deleteImages();

      $position = 2;
      foreach ($product->Images->children() as $img) {
        $image = new Image();
        $image->id_product = (int)$newProduct->id;
        if ($img->Default->__toString() && (int)$img->Default == 1) {
          $image->position = 1;
          $image->cover =  true;
        } else {
          $image->position = $position;
          $image->cover =  false;
          $position++;
        }

        $image_legends = array();
        if (count($img->Legend->children()) > 0) {
          foreach ($img->Legend->children() as $iso => $legend) {
            $id_lang = Language::getIdByIso($iso);
            // $legend = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $legend);
            $legend = str_replace(array("<", ">", "=", "{", "}"), '', $legend);
            $image_legends[(int)$id_lang] = mb_substr(trim($legend), 0, 128, "utf-8");
          }

          $image->legend = $image_legends;
        }

        $image->add();

        foreach ($shops as $shop) {
          $exists = Db::getInstance()->getValue('
            SELECT `id_image`
            FROM `'._DB_PREFIX_.'image_shop`
            WHERE `id_image` = '.(int)$image->id.'
            AND `id_shop` = '.(int)$shop
          );
          if (!$exists && $image->cover) {
            Db::getInstance()->execute('
              INSERT INTO '._DB_PREFIX_.'image_shop
              (id_product, id_image, id_shop, cover)
              VALUES
              ('.(int)$image->id_product.', '.(int)$image->id.', '.(int)$shop.', '.(int)$image->cover.')
            ');
          } elseif (!$exists && !$image->cover) {
            Db::getInstance()->execute('
              INSERT INTO '._DB_PREFIX_.'image_shop
              (id_product, id_image, id_shop)
              VALUES
              ('.(int)$image->id_product.', '.(int)$image->id.', '.(int)$shop.')
            ');
          }
        }

        if (!$this->copyImg((int)$newProduct->id, $image->id, trim($img->Url))) {
          $image->delete();
        } else {
          $newProduct_img[$image->id] = trim($img->Url);
        }
      }
    }
  }

  public function getImageNameFormUrl($url){
    $tmp = explode('/',$url);
    return $tmp[count($tmp)-1];
  }

  public function downloadProductImage($img){
    $url = str_replace(' ', '%20', trim($img->Url->__toString()));
    $tmpfile = _PS_TMP_IMG_DIR_.$this->getImageNameFormUrl($url);
    if(Tools::copy($url, $tmpfile)){
      return $tmpfile;
    }
    else {
      return false;
    };


  }

  public function deleteProductImageAllShops($product,$shops){
    foreach($shops as $shop){
      Shop::setContext(Shop::CONTEXT_SHOP, (int)$shop);
      $product->deleteImages();
    }
    Shop::setContext(Shop::CONTEXT_SHOP, $shops[0]);
  }

  public function getImageAspFile($img,$test=0){
    $url = str_replace(' ', '%20', trim($img));
    if ($url=="") {
      return $url;
    }
    $tmp = explode('/files/produits/',$url);

    return $tmp[1];
  }

  public function setProductImages2($product,&$newProduct,$update_product,$shops){
    $newProduct_img =array();

    if (count($product->Images->children()) > 0 && (isset($_GET['reset_images']) || !$update_product || (int)$product->DeleteExistingImages->__toString() == 1)) {

      $this->deleteProductImageAllShops($newProduct,$shops);
      if (count($product->Images->children()) > 0) {
        $img_datas = array();
        $position = 2;
        $i =0;
        foreach ($product->Images->children() as $img_key => $img) {
          $i++;
          $tmpfile = $this->downloadProductImage($img);
          if ($tmpfile) {
            $md5_file = md5_file($tmpfile);
            $duplicate_found = false;
            foreach($img_datas as $key => $img_data){
              if ($img_data['md5'] == $md5_file) {
                // $duplicate_found = true;
                // $newProduct_img[$img_data['id_image'].'-'.$i] = trim($img->Url);
                break;
              }

            }

            if (!$duplicate_found || count($img_datas)==0) {
              $image = new Image();
              $image->id_product = (int)$newProduct->id;
              $image->asp_file = $this->getImageAspFile($img->Url->__toString());
              if ($img->Default->__toString() && (int)$img->Default == 1) {
                $image->position = 1;
                $image->cover =  true;
              } else {
                $image->position = $position;
                $image->cover =  false;
                $position++;
              }

              $image_legends = array();
              if (count($img->Legend->children()) > 0) {
                foreach ($img->Legend->children() as $iso => $legend) {
                  $id_lang = Language::getIdByIso($iso);
                  // $legend = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $legend);
                  $legend = str_replace(array("<", ">", "=", "{", "}"), '', $legend);
                  $image_legends[(int)$id_lang] = mb_substr(trim($legend), 0, 128, "utf-8");
                }

                $image->legend = $image_legends;
              }

              $image->add();

              foreach ($shops as $shop) {
                $exists = Db::getInstance()->getValue('
                  SELECT `id_image`
                  FROM `'._DB_PREFIX_.'image_shop`
                  WHERE `id_image` = '.(int)$image->id.'
                  AND `id_shop` = '.(int)$shop
                );
                if (!$exists && $image->cover) {
                  Db::getInstance()->execute('
                    INSERT INTO '._DB_PREFIX_.'image_shop
                    (id_product, id_image, id_shop, cover)
                    VALUES
                    ('.(int)$image->id_product.', '.(int)$image->id.', '.(int)$shop.', '.(int)$image->cover.')
                  ');
                } elseif (!$exists && !$image->cover) {
                  Db::getInstance()->execute('
                    INSERT INTO '._DB_PREFIX_.'image_shop
                    (id_product, id_image, id_shop)
                    VALUES
                    ('.(int)$image->id_product.', '.(int)$image->id.', '.(int)$shop.')
                  ');
                }
              }

              $image_file_path = _PS_TMP_IMG_DIR_.$this->getImageNameFormUrl(trim($img->Url));

              if (!$this->copyImg((int)$newProduct->id, $image->id, $image_file_path)) {
                $image->delete();
              } else {
                $newProduct_img[$image->id] = trim($img->Url);
              }


              $img_datas[] = array(
                'md5' => $md5_file,
                'id_image' => $image->id,
                'urls' => array($img->Url->__toString()),
              );
            }
            else{

              unlink($tmpfile);

            }

          }
        }
        // foreach($img_datas as $img_data){
        //   echo '<img width="200px" src="'.$img_data['url'].'" />';
        //   echo $img_data['md5'].'<br>';
        // }
        //var_dump($img_datas);
      }
    }
//var_dump($newProduct_img);
    return $newProduct_img;
  }

  public function removeProductFeatures($product,&$newProduct,$shops){

      $sql ="DELETE FROM `ps_feature_product` WHERE `id_product` = ".$newProduct;
      Db::getInstance()->execute($sql);


  }

  public function setProductFeatures($product,&$newProduct,$update_product,$shops){
    if ($update_product) {
      $this->removeProductFeatures($product,$update_product,$shops);
      $update_product = new Product($update_product);
    }
    //$product_features = $newProduct->getFeatures();

    $categories = array('univers'=>array(),'category'=>array(),'subcategory'=>array());
    foreach ($product->Categories->Category as $value) {
      $breadcrumbs = $value->Breadcrumb;
      $breadcrumb_lang = array();
      foreach ($breadcrumbs->children() as $iso => $breadcrumb) {
        $breadcrumb_lang[$iso] = explode(">", $breadcrumb);
        foreach($breadcrumb_lang[$iso] as $key => $cat){
          if ($key == 0) {
            $categories['univers'][$iso] = $cat;
          }
          elseif ($key == 1) {
            $categories['category'][$iso] = $cat;
          }
          elseif ($key == 2) {
            $categories['subcategory'][$iso] = $cat;
          }
        }
      }
    }
    foreach($categories as $key => $cat){
      $count = count($product->Features);
      $new_feature = $product->Features->addChild('feature');
      $new_feature_name = $new_feature->addChild('Name');
      $new_feature_value = $new_feature->addChild('Value');
      foreach($cat as $iso => $cat_name){
        $cat_name = str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($cat_name), 0, 128, "utf-8"));
        $this->addCData($iso,$key,$new_feature_name);
        $this->addCData($iso,$cat_name,$new_feature_value);
      }
    }
    //echo $product->Features->asXml();
    $feature_fr_added = array();
    if (count($product->Features->children()) > 0) {
      foreach ($product->Features->children() as $feature) {
        if (count($feature->Name->children()) > 0 && $feature->Name->fr->__toString() && !empty(trim($feature->Name->fr))) {
          $id_fr = Language::getIdByIso('fr');

          $feature_fr = trim(ucfirst($feature->Name->fr));
          // $feature_fr = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $feature_fr);
          $feature_fr = str_replace(array("<", ">", "=", "{", "}"), '', $feature_fr);
          $feature_fr = mb_substr(strip_tags($feature_fr), 0, 128, "utf-8");

          if (in_array(strtolower($feature_fr),$feature_fr_added)) {
            continue;
          }
          $exists = Db::getInstance()->getValue('
            SELECT `id_feature`
            FROM `'._DB_PREFIX_.'feature_lang`
            WHERE `id_lang` = '.(int)$id_fr.'
            AND `name` = \''.pSQL($feature_fr).'\'
          ');

          if (!$exists) {
            $newFeature = new Feature();

            $feature_names = array();
            foreach ($feature->Name->children() as $iso => $name) {
              $id_lang = Language::getIdByIso($iso);
              // $name = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $name);
              $name = strip_tags($name);
              $name = str_replace(array("<", ">", "=", "{", "}"), '', $name);
              $feature_names[(int)$id_lang] = mb_substr(trim(ucfirst($name)), 0, 128, "utf-8");
            }

            $newFeature->name = $feature_names;

            $newFeature->add();
            $newFeature->associateTo($shops);

            if (count($feature->Value->children()) > 0 && $feature->Value->fr->__toString() && !empty(trim($feature->Value->fr))) {
              $newFeatureValue = new FeatureValue();
              $newFeatureValue->id_feature = $newFeature->id;

              $feature_values = array();
              foreach ($feature->Value->children() as $iso => $value) {
                $id_lang = Language::getIdByIso($iso);
                // $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                $value = strip_tags($value);
                $value = str_replace(array("<", ">", "=", "{", "}"), '', $value);
                $feature_values[(int)$id_lang] = mb_substr(trim(ucfirst($value)), 0, 255, "utf-8");
              }

              $newFeatureValue->value = $feature_values;

              $newFeatureValue->add();

              if ($update_product) {
                $update_product->addFeaturesToDB($newFeature->id, $newFeatureValue->id);
              }
              else {
                $newProduct->addFeaturesToDB($newFeature->id, $newFeatureValue->id);
              }

            }
          } else {
            $newFeature = new Feature((int)$exists);

            $feature_names = array();
            foreach ($feature->Name->children() as $iso => $name) {
              $id_lang = Language::getIdByIso($iso);
              // $name = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $name);
              $name = strip_tags($name);
              $name = str_replace(array("<", ">", "=", "{", "}"), '', $name);
              $feature_names[(int)$id_lang] = mb_substr(trim(ucfirst($name)), 0, 128, "utf-8");
            }

            $newFeature->name = $feature_names;

            $newFeature->update();

            if (count($feature->Value->children()) > 0 && $feature->Value->fr->__toString() && !empty(trim($feature->Value->fr))) {

              $value_fr = trim(ucfirst($feature->Value->fr));
              // $value_fr = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value_fr);
              $value_fr = str_replace(array("<", ">", "=", "{", "}"), '', $value_fr);
              $value_fr = mb_substr(strip_tags($value_fr), 0, 255, "utf-8");

              $value_exists = Db::getInstance()->getValue('
                SELECT `id_feature_value`
                FROM `'._DB_PREFIX_.'feature_value_lang`
                WHERE `id_lang` = '.(int)$id_fr.'
                AND `value` = \''.pSQL($value_fr).'\'
              ');

              if (!$value_exists) {
                $newFeatureValue = new FeatureValue();
                $newFeatureValue->id_feature = (int)$exists;

                $feature_values = array();
                foreach ($feature->Value->children() as $iso => $value) {
                  $id_lang = Language::getIdByIso($iso);
                  // $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                  $value = strip_tags($value);
                  $value = str_replace(array("<", ">", "=", "{", "}"), '', $value);
                  $feature_values[(int)$id_lang] = mb_substr(trim(ucfirst($value)), 0, 255, "utf-8");
                }

                $newFeatureValue->value = $feature_values;


                $newFeatureValue->add();


                if ($update_product) {
                  $update_product->addFeaturesToDB($exists, $newFeatureValue->id);
                }
                else {
                  $newProduct->addFeaturesToDB($exists, $newFeatureValue->id);
                }
              } else {
                $newFeatureValue = new FeatureValue((int)$value_exists);
                $newFeatureValue->id_feature = (int)$exists;

                $feature_values = array();
                foreach ($feature->Value->children() as $iso => $value) {
                  $id_lang = Language::getIdByIso($iso);
                  // $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                  $value = strip_tags($value);
                  $value = str_replace(array("<", ">", "=", "{", "}"), '', $value);
                  $feature_values[(int)$id_lang] = mb_substr(trim(ucfirst($value)), 0, 255, "utf-8");
                }

                $newFeatureValue->value = $feature_values;

                $newFeatureValue->update();


                if ($update_product) {
                  $update_product->addFeaturesToDB($exists, $value_exists);
                }
                else {
                  $newProduct->addFeaturesToDB($exists, $value_exists);
                }
              }
            }
          }
          $feature_fr_added[] = strtolower($feature_fr);
        }
      }
    }
  }

  public function setProductSpecialPrices($product,&$newProduct,$shops){
    if (count($product->Combinaisons->children()) == 0 && count($product->Discount->children()) > 0 && (($product->Discount->Amount->__toString() && $product->Discount->Amount > 0) || ($product->Discount->Percent->__toString() && $product->Discount->Percent > 0))) {
      if ($product->Discount->DateFrom->__toString()) {
        $datetime_from = new DateTime();
        $date_from = $datetime_from->createFromFormat('d/m/Y', trim($product->Discount->DateFrom));
        $from = $date_from->format('Y-m-d H:i:s');
      } else {
        $from = '0000-00-00 00:00:00';
      }
      if ($product->Discount->DateTo->__toString()) {
        $datetime_to = new DateTime();
        $date_to = $datetime_from->createFromFormat('d/m/Y', trim($product->Discount->DateTo));
        $to = $date_to->format('Y-m-d H:i:s');
      } else {
        $to = '0000-00-00 00:00:00';
      }

      $exists_specific = SpecificPrice::exists((int)$newProduct->id, 0, 0, 0, 0, 0, 0, 1, $from, $to);

      if (!$exists_specific) {
        $objSpecificPrice = new SpecificPrice();
        $objSpecificPrice->id_shop = 0;
        $objSpecificPrice->id_product_attribute = 0;
        $objSpecificPrice->id_currency = 0;
        $objSpecificPrice->id_specific_price_rule = 0;
        $objSpecificPrice->id_country = 0;
        $objSpecificPrice->id_group = 0;
        $objSpecificPrice->id_customer = 0;
        $objSpecificPrice->price = number_format(-1, 6);
        $objSpecificPrice->from_quantity = 1;
        $objSpecificPrice->reduction_tax = 1;
      } else {
        $objSpecificPrice = new SpecificPrice($exists_specific);
      }

      $objSpecificPrice->id_product = (int)$newProduct->id;

      if ($product->Discount->Amount->__toString() && $product->Discount->Amount > 0) {
        $price_reduction = str_replace(',', '.', trim($product->Discount->Amount));
        $price_reduction = number_format(floatval($price_reduction), 6);
        $objSpecificPrice->reduction = str_replace(',', '', $price_reduction);
        $objSpecificPrice->reduction_type = 'amount';
      } else {
        $objSpecificPrice->reduction = number_format(floatval($product->Discount->Percent) / 100, 6);
        $objSpecificPrice->reduction_type = 'percentage';
      }

      $objSpecificPrice->from = $from;
      $objSpecificPrice->to = $to;

      if (!$exists_specific) {
        $objSpecificPrice->add();
      } else {
        $objSpecificPrice->update();
      }
    }
  }

  public function getPsImagesByProductId($id_product){
    $r = Db::getInstance()->executeS("SELECT * FROM ps_image WHERE id_product = ".$id_product);
    $img = [];
    foreach($r as $l){
      $img[$l['id_image']] = $l['asp_file'];
    }
    return $img;
  }

  public function setProductCombinations($product,&$newProduct,$shops,$newProduct_img){
    if (count($product->Combinaisons->children()) > 0) {
      $default_comb_is_set = false;
      $asp_file_array = $this->getPsImagesByProductId($newProduct->id);
      foreach ($product->Combinaisons->children() as $combinaison) {

        $attributes_to_add = array();

        if (count($combinaison->Attributes->children()) > 0) {
          foreach ($combinaison->Attributes->children() as $attribute) {

            if (count($attribute->Name->children()) > 0 && $attribute->Name->fr->__toString() && !empty(trim($attribute->Name->fr))) {
              $id_fr = Language::getIdByIso('fr');

              $attribute_name = str_replace(array("<", ">", "=", "{", "}"), '', $attribute->Name->fr);

              $exists = Db::getInstance()->getValue('
                SELECT `id_attribute_group`
                FROM `'._DB_PREFIX_.'attribute_group_lang`
                WHERE `id_lang` = '.(int)$id_fr.'
                AND `name` = \''.pSQL(mb_substr(trim(ucfirst($attribute_name)), 0, 128, "utf-8")).'\''
              );

              if (!$exists) {
                $newAttributeGroup = new AttributeGroup();
                $newAttributeGroup->group_type = 'select';
                $newAttributeGroup->is_color_group = 0;

                $attribute_groups = array();
                foreach ($attribute->Name->children() as $iso => $name) {
                  $id_lang = Language::getIdByIso($iso);
                  // $name = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $name);
                  $name = str_replace(array("<", ">", "=", "{", "}"), '', $name);
                  $attribute_groups[(int)$id_lang] = mb_substr(trim(ucfirst($name)), 0, 128, "utf-8");
                }

                $newAttributeGroup->name = $attribute_groups;
                $newAttributeGroup->public_name = $attribute_groups;

                $newAttributeGroup->add();
                $newAttributeGroup->associateTo($shops);

                if (count($attribute->Value->children()) > 0 && $attribute->Value->fr->__toString() && !empty(trim($attribute->Value->fr))) {
                  $newAttribute = new Attribute();
                  $newAttribute->id_attribute_group = $newAttributeGroup->id;

                  $attribute_values = array();
                  foreach ($attribute->Value->children() as $iso => $value) {
                    $id_lang = Language::getIdByIso($iso);
                    // $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                    $value = str_replace(array("<", ">", "=", "{", "}"), '', $value);
                    $attribute_values[(int)$id_lang] = mb_substr(trim(ucfirst($value)), 0, 128, "utf-8");
                  }

                  $newAttribute->name = $attribute_values;

                  $newAttribute->add();
                  $newAttribute->associateTo($shops);

                  $attributes_to_add[] = $newAttribute->id;
                }
              } else {
                $newAttributeGroup = new AttributeGroup((int)$exists);

                $attribute_groups = array();
                foreach ($attribute->Name->children() as $iso => $name) {
                  $id_lang = Language::getIdByIso($iso);
                  // $name = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $name);
                  $name = str_replace(array("<", ">", "=", "{", "}"), '', $name);
                  $attribute_groups[(int)$id_lang] = mb_substr(trim(ucfirst($name)), 0, 128, "utf-8");
                }


                $newAttributeGroup->name = $attribute_groups;
                $newAttributeGroup->public_name = $attribute_groups;

                $newAttributeGroup->update();

                if (count($attribute->Value->children()) > 0 && $attribute->Value->fr->__toString() && !empty(trim($attribute->Value->fr))) {
                  $attribute_value = str_replace(array("<", ">", "=", "{", "}"), '', $attribute->Value->fr);

                  $value_exists = Db::getInstance()->getValue('
                    SELECT al.`id_attribute`
                    FROM `'._DB_PREFIX_.'attribute_lang` al, `'._DB_PREFIX_.'attribute` a
                    WHERE `id_lang` = '.(int)$id_fr.'
                    AND al.id_attribute = a.id_attribute
                    AND a.id_attribute_group = '.$newAttributeGroup->id.'
                    AND al.`name` = \''.pSQL(mb_substr(trim(ucfirst($attribute_value)), 0, 128, "utf-8")).'\''
                  );

                  if (!$value_exists) {
                    $newAttribute = new Attribute();
                    $newAttribute->id_attribute_group = (int)$exists;

                    $attribute_values = array();
                    foreach ($attribute->Value->children() as $iso => $value) {
                      $id_lang = Language::getIdByIso($iso);
                      // $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                      $value = str_replace(array("<", ">", "=", "{", "}"), '', $value);
                      $attribute_values[(int)$id_lang] = mb_substr(trim(ucfirst($value)), 0, 128, "utf-8");
                    }

                    $newAttribute->name = $attribute_values;

                    $newAttribute->add();
                    $newAttribute->associateTo($shops);

                    $attributes_to_add[] = $newAttribute->id;
                  } else {
                    $newAttribute = new Attribute((int)$value_exists);
                    $newAttribute->id_attribute_group = (int)$exists;

                    $attribute_values = array();
                    foreach ($attribute->Value->children() as $iso => $value) {
                      $id_lang = Language::getIdByIso($iso);
                      // $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                      $value = str_replace(array("<", ">", "=", "{", "}"), '', $value);
                      $attribute_values[(int)$id_lang] = mb_substr(trim(ucfirst($value)), 0, 128, "utf-8");
                    }

                    $newAttribute->name = $attribute_values;

                    $newAttribute->update();

                    $attributes_to_add[] = $value_exists;
                  }
                }
              }
            }

          }
        }

        $update_product_attribute = false;
        if ($combinaison->Reference->__toString()) {
          $update_product_attribute = Db::getInstance()->getValue('
            SELECT id_product_attribute
            FROM '._DB_PREFIX_.'product_attribute
            WHERE reference = \''.pSql(trim($combinaison->Reference)).'\'
            AND id_product = '.(int)$newProduct->id
          );
        }

        if ($combinaison->WholesalePrice->__toString()) {
          $wholesale_price = number_format(floatval($combinaison->WholesalePrice), 6);
        } else {
          $wholesale_price = 0;
        }
        if ($combinaison->Price->__toString()) {
          $price = str_replace(',', '.', trim($combinaison->Price));
          $price = number_format(floatval($price), 6);
          $price = str_replace(',', '', $price);
        } else {
          $price = 0;
        }
        if ($combinaison->ImpactOnWeight->__toString()) {
          $weight = str_replace(',', '.', trim($combinaison->ImpactOnWeight));
          $weight = number_format(floatval($weight), 6);
          $weight = str_replace(',', '', $weight);
        } else {
          $weight = 0;
        }
        $unit_impact = 0;
        if ($combinaison->Ecotax->__toString()) {
          $ecotax = str_replace(',', '.', trim($combinaison->Ecotax));
          $ecotax = number_format(floatval($ecotax), 6);
          $ecotax = str_replace(',', '', $ecotax);
        } else {
          $ecotax = 0;
        }
        $ecotax = 0;
        if ($combinaison->Stock->__toString()) {
          $quantity = (int)$combinaison->Stock;
        } else {
          $quantity = 0;
        }
        $id_images = array();
        if (count($combinaison->Images->children()) > 0 ) {
          foreach ($combinaison->Images->children() as $img) {
            $asp_file = $this->getImageAspFile($img->__toString());
            $id_image = array_search($asp_file,$asp_file_array);
            if ($id_image) {
              $id_images[] = $id_image;
            }

          }
        }
        if ($combinaison->Reference->__toString()) {
          $reference = trim($combinaison->Reference);
        } else {
          $reference = '';
        }
        if ($combinaison->SupplierReference->__toString()) {
          $id_supplier = (int)$newProduct->id_supplier;
          $supplier_reference = mb_substr(trim($combinaison->SupplierReference), 0, 64, "utf-8");
        } else {
          $id_supplier = 0;
          $supplier_reference = '';
        }
        if ($combinaison->EAN13->__toString()) {
          $ean13 = mb_substr(trim($combinaison->EAN13), 0, 13, "utf-8");
        } else {
          $ean13 = '';
        }
        if ($combinaison->Default->__toString() && !$default_comb_is_set) {
          $default = 1;
          $default_comb_is_set = true;
        } else {
          $default = 0;
        }
        $location = null;
        if ($combinaison->UPC->__toString()) {
          $upc = mb_substr(trim($combinaison->UPC), 0, 12, "utf-8");
        } else {
          $upc = '';
        }
        if ($combinaison->MinimalQuantity->__toString()) {
          $minimal_quantity = (int)$combinaison->MinimalQuantity;
        } else {
          $minimal_quantity = 1;
        }

        if ($default == 1) {
          Db::getInstance()->execute('
            UPDATE '._DB_PREFIX_.'product_attribute
            SET default_on = NULL
            WHERE id_product = '.(int) $newProduct->id
          );

          Db::getInstance()->execute('
            UPDATE '._DB_PREFIX_.'product_attribute_shop
            SET default_on = NULL
            WHERE id_product = '.(int) $newProduct->id
          );

          if ($update_product_attribute) {
            Db::getInstance()->execute('
              UPDATE '._DB_PREFIX_.'product_attribute_shop
              SET default_on = '.$default.'
              WHERE id_product_attribute = '.(int) $update_product_attribute
            );
          }
        }

        if (!$update_product_attribute) {
          $id_product_attribute = $newProduct->addCombinationEntity(
            $wholesale_price,
            $price,
            $weight,
            $unit_impact,
            $ecotax,
            $quantity,
            $id_images,
            $reference,
            $id_supplier,
            $ean13,
            $default,
            $location,
            $upc,
            $minimal_quantity,
            $shops
          );

          $newCombination = new Combination($id_product_attribute);
        } else {
          $newCombination = new Combination((int)$update_product_attribute);

          if ($combinaison->Reference->__toString()) {
            $newCombination->reference = trim($combinaison->Reference);
          }
          if ($combinaison->EAN13->__toString()) {
            $newCombination->ean13 = mb_substr(trim($combinaison->EAN13), 0, 13, "utf-8");
          }

          $newCombination->default_on = $default;
          if ($default) {
            $default_comb_is_set =true;
          }

          if ($combinaison->UPC->__toString()) {
            $newCombination->upc = mb_substr(trim($combinaison->UPC), 0, 12, "utf-8");
          }

          $newCombination->update();

          // $id_images = array();
          // if (count($combinaison->Images->children()) > 0 && !empty($newProduct_img)) {
          //   foreach ($combinaison->Images->children() as $image) {
          //     $tmp = array_search(trim($image), $newProduct_img);
          //     $tmp2 = explode('-',$tmp);
          //     $id_image = $tmp2[0];
          //     if (!in_array($id_image,$id_images)) {
          //       $id_images[] = $id_image;
          //     }
          //
          //   }
          //   $newCombination->setImages($id_images);
          // }
          if (count($id_images)>0) {
            $newCombination->setImages($id_images);
          }
        }



        $newCombination->setAttributes($attributes_to_add);

        if (isset($id_images) && !empty($id_images)) {
          $newCombination->setImages($id_images);
        }

        if ($combinaison->SupplierReference->__toString()) {
          $newCombination->supplier_reference = mb_substr(trim($combinaison->SupplierReference), 0, 64, "utf-8");
          $newCombination->update();
          $newProduct->addSupplierReference($id_supplier, $newCombination->id, mb_substr(trim($combinaison->SupplierReference), 0, 64, "utf-8"), (float)$newCombination->wholesale_price);
        }

        if (!$update_product_attribute) {
          if ($combinaison->Stock->__toString()) {
            foreach ($shops as $id_shop) {
              StockAvailable::setQuantity($newProduct->id, $newCombination->id, (int)$combinaison->Stock, $id_shop);
            }
          }

          if (count($combinaison->Discount->children()) > 0 && (($combinaison->Discount->Amount->__toString() && $combinaison->Discount->Amount > 0) || ($combinaison->Discount->Percent->__toString() && $combinaison->Discount->Percent > 0))) {
            if ($combinaison->Discount->DateFrom->__toString()) {
              $datetime_from = new DateTime();
              $date_from = $datetime_from->createFromFormat('d/m/Y', trim($combinaison->Discount->DateFrom));
              $from = $date_from->format('Y-m-d H:i:s');
            } else {
              $from = '0000-00-00 00:00:00';
            }
            if ($combinaison->Discount->DateTo->__toString()) {
              $datetime_to = new DateTime();
              $date_to = $datetime_to->createFromFormat('d/m/Y', trim($combinaison->Discount->DateTo));
              $to = $date_to->format('Y-m-d H:i:s');
            } else {
              $to = '0000-00-00 00:00:00';
            }

            // $exists_specific = SpecificPrice::exists((int)$newProduct->id, (int)$newCombination->id, 0, 0, 0, 0, 0, 1, $from, $to);
            //
            // if (!$exists_specific) {
            //   $objSpecificPrice2 = new SpecificPrice();
            //   $objSpecificPrice2->id_shop = 0;
            //   $objSpecificPrice2->id_currency = 0;
            //   $objSpecificPrice2->id_specific_price_rule = 0;
            //   $objSpecificPrice2->id_country = 0;
            //   $objSpecificPrice2->id_group = 0;
            //   $objSpecificPrice2->id_customer = 0;
            //   $objSpecificPrice2->price = number_format(-1, 6);
            //   $objSpecificPrice2->from_quantity = 1;
            //   $objSpecificPrice2->reduction_tax = 1;
            // } else {
            //   $objSpecificPrice2 = new SpecificPrice($exists_specific);
            // }
            //
            // $objSpecificPrice2->id_product = (int)$newProduct->id;
            // $objSpecificPrice2->id_product_attribute = (int)$newCombination->id;
            //
            // if ($combinaison->Discount->Amount->__toString() && $combinaison->Discount->Amount > 0) {
            //   $price_reduction = str_replace(',', '.', trim($combinaison->Discount->Amount));
            //   $price_reduction = number_format(floatval($price_reduction), 6);
            //   $objSpecificPrice2->reduction = str_replace(',', '', $price_reduction);
            //   $objSpecificPrice2->reduction_type = 'amount';
            // } else {
            //   $objSpecificPrice2->reduction = number_format(floatval($combinaison->Discount->Percent) / 100, 6);
            //   $objSpecificPrice2->reduction_type = 'percentage';
            // }
            //
            // $objSpecificPrice2->from = $from;
            // $objSpecificPrice2->to = $to;
            //
            // if (!$exists_specific) {
            //   $objSpecificPrice2->add();
            // } else {
            //   $objSpecificPrice2->update();
            // }
          }
        }
      }
    }
  }


  public function getProductsMd5FromXmlFile($path){
    $diff_only_comb_ignore_node =array(
      "Stock","OnSale","Discount","Price"
    );
    $diff_only_product_ignore_node =array(
      "Stock","OnSale","Discount","Price"
    );
    $files_done = glob($path);
    $last_file_done = $files_done[count($files_done)-1];
    $xml_product_done = simplexml_load_file($last_file_done);
    $xml_product_done_md5 = array();
    $i = 0;
    foreach ($xml_product_done->Products->Product as $product) {
      $product_to_check = $product;
      if ($i>0) {
        //continue;
      }
      if (count($product_to_check->Combinaisons->children()) > 0) {
        $comb_key = 0;
        foreach ($product_to_check->Combinaisons->children() as $key => $combinaison){
          foreach($diff_only_comb_ignore_node as $node_name){
            if (isset( $product_to_check->Combinaisons->Combinaison[$comb_key]->$node_name)) {
              unset($product_to_check->Combinaisons->Combinaison[$comb_key]->$node_name);
            }
          }

          $comb_key++;
        }
      }
      foreach($diff_only_product_ignore_node as $node_name){
        if (isset( $xml_product_done->Products->Product[$i]->$node_name)) {
          unset($xml_product_done->Products->Product[$i]->$node_name);
        }
      }


      if (count($product_to_check->Features->children()) > 0) {
        $features = array();
        $key = 0;
        foreach ($product_to_check->Features->children() as $feature) {
          $features[$feature->Name->fr->__toString()] = $feature;
          $key++;

        }
        ksort($features);
        $key = 0;
        $dom_features = dom_import_simplexml($product_to_check->Features);
        foreach ($features as $f_key => $feature) {
          $dom_feature = dom_import_simplexml($feature);
          $dom_feature_copy = $dom_features->ownerDocument->importNode($dom_feature, true);
          $dom_features->appendChild($dom_feature_copy);
          $key++;
        }
      }

      $xml_product_done_md5[trim($product_to_check->Reference)] = md5($product_to_check->asXML());

      $i++;
    }

    return $xml_product_done_md5;
  }



  public function unpdateProdutsFromXml($xml_products){
    $shops = Shop::getShops(true, null, true);
    $products_exsit_in_xml = array();
    $diff_only = false;
    if (isset($_GET['diff_only']) && $_GET['diff_only']==1) {
      $diff_only = 1;
    }
    if ($diff_only) {
      $xml_product_done_md5 = $this->getProductsMd5FromXmlFile(_PS_ROOT_DIR_ . '/download/product_files/done/*.xml');
      $xml_product_md5 = $this->getProductsMd5FromXmlFile(_PS_ROOT_DIR_ . '/download/product_files/*.xml');
    }


    if (isset($xml_products->Products) && count($xml_products->Products->children()) > 0)
    {
      $i = -1;
      $n = 0;
      $same_c = 0;
      $diff_c = 0;
      foreach ($xml_products->Products->Product as $key=> $product) {
        $products_exsit_in_xml[] = trim($product->Reference);
        $i++;
        if (isset($this->go_next_product) && $this->go_next_product) {
          continue;
        }
        if ((isset($_GET['from']) && $i<$_GET['from']) || (isset($_GET['to']) && $i>$_GET['to'])) {
          continue;
        }
        $product_names = $this->getProductNames($product);
        if (in_array(strtolower($product_names[1]),['sav',"colisage"])) {
          continue;
        }
        if ($product->Reference->__toString() !== '95142') {
          //continue;
        }

        if (isset($_GET['ref']) && $_GET['ref'] && $product->Reference->__toString() !== $_GET['ref']) {

          continue;

        }

        //var_dump(count($product->Features->Feature));
        if ($i>0) {
          //continue;
        }
        if ($diff_only) {
          if (isset($xml_product_done_md5[trim($product->Reference)])) {
            if ($xml_product_md5[trim($product->Reference)] == $xml_product_done_md5[trim($product->Reference)]) {
              $same_c++;
              continue;
            }
          }
        }
        $diff_c++;
        if (isset($_GET['diff_count'])) {
          echo trim($product->Reference).' * <br>';
          continue;
        }


        //continue;
        //var_dump($i);
        echo trim($product->Reference).' * <br>';
        $update_product = $this->getProductToUpdate($product);
        $newProduct = $this->getNewProductByUpdateProduct($update_product);
        if (isset($_GET['add_only']) && $_GET['add_only'] && $update_product) {
          //$this->setProductPrice($product,$newProduct,$shops);
          //var_dump($this->context->shop->id);
          //$this->setProductLinks($product,$newProduct,$shops);
          //$this->setProductDescriptions($product,$newProduct,$shops);
          //$newProduct->save();
          continue;
        }
        $this->setManufacturer($product,$newProduct,$shops);
        $this->setSupplier($product,$newProduct,$shops);
        $categories_to_add = $this->getProductCategories($product,$newProduct,$shops);
        $this->setProductNames($product,$newProduct,$shops);
        $this->setProductDescriptions($product,$newProduct,$shops);
        $this->setProductShortDescriptions($product,$newProduct,$shops);
        //if (!$update_product) {
          $this->setProductMunimalQuantity($product,$newProduct,$shops);
          // $this->setProductAvailableNow($product,$newProduct,$shops);
          // $this->setProductAvailableLater($product,$newProduct,$shops);
          $this->setProductAdditionnalShippingCost($product,$newProduct,$shops);
          $this->setProductWholesalePrice($product,$newProduct,$shops);
          $this->setProductPrice($product,$newProduct,$shops);
          $this->setProductOnlineOnly($product,$newProduct,$shops);
          $this->setProductUnity($product,$newProduct,$shops);
          $this->setProductEcoTax($product,$newProduct,$shops);
        // }

        $this->setProductReference($product,$newProduct,$shops);
        $this->setProductSupplierReference($product,$newProduct,$shops);
        $this->setProductShippingInfos($product,$newProduct,$shops);
        $this->setProductLinks($product,$newProduct,$shops);
        $this->setProductMeta($product,$newProduct,$shops);
        $this->SetProductActive($product,$newProduct,$shops);
        $this->SetProductOthers($product,$newProduct,$shops);
        $this->saveProduct($product,$newProduct,$update_product,$shops);

        $this->addProductToShops($product,$newProduct,$shops);
        $this->setProductCategories($newProduct,$categories_to_add);
        $this->setProductTags($product,$newProduct,$shops);
        $newProduct_img = array();
        $newProduct_img = $this->setProductImages2($product,$newProduct,$update_product,$shops);

        if (!$update_product) {

          //$this->setProductSpecialPrices($product,$newProduct,$shops);
        }

        $this->setProductFeatures($product,$newProduct,$update_product,$shops);
        $this->setProductCombinations($product,$newProduct,$shops,$newProduct_img);
        $n++;
      }
    }
    $this->disableProductNotInXml($products_exsit_in_xml);
    var_dump($same_c,$diff_c);
  }

  public function callDisableProductNoStockAllShops(){

  }

  public function disableProductNotInXml($products_exsit_in_xml){
    if (count($products_exsit_in_xml)==0) {
      return;
    }
    $not_in = '';
    foreach($products_exsit_in_xml as $ref){
      $not_in .= "'".$ref."'".",";
    }
    $not_in = trim($not_in,',');
    $sql = "UPDATE `ps_product_shop` SET `active`=0 where ps_product_shop.id_product IN (SELECT id_product FROM ps_product WHERE reference not in ($not_in))";
    //$sql = "select reference from ps_product where reference not in ($not_in)";
    $r = Db::getInstance()->execute($sql);
    $sql = "UPDATE `ps_product` SET `active`=0 where reference not in ($not_in)";
    $r = Db::getInstance()->execute($sql);
    // $sql = "UPDATE `ps_product_shop` SET `active`=1 where ps_product_shop.id_product IN (SELECT id_product FROM ps_product WHERE reference in ($not_in))";
    // //$sql = "select reference from ps_product where reference not in ($not_in)";
    // $r = Db::getInstance()->execute($sql);
    // $sql = "UPDATE `ps_product` SET `active`=1 where reference in ($not_in)";
    // $r = Db::getInstance()->execute($sql);

  }

  public function autoNext($xml_products){
    if (isset($_GET['auto']) && $_GET['auto']) {
      $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


      $new_from = 0;
      $new_to = 0;
      $tmps0 = explode('?',$actual_link);
      $new_link = '';
      $tmps = explode('&',$tmps0[1]);
      foreach($tmps as $key => $tmp){
        if (strpos($tmp,'from')=== 0 ) {
          $tmp2 =explode('=',$tmp);
          $new_from = $tmp2[1]+$_GET['auto'];
          $tmps[$key] = 'from='.$new_from;
        }
        if (strpos($tmp,'to')=== 0) {
          $tmp2 =explode('=',$tmp);
          $new_to = $tmp2[1]+$_GET['auto'];
          $tmps[$key] = 'to='.$new_to;
        }
      $new_link .= '&'.$tmps[$key];
      }
      $new_link = $tmps0[0].'?'.trim($new_link,'&');

      if ($new_from <= count($xml_products->Products->children())) {

        echo "<a href='$new_link'>$new_link</a>";
        echo "<script>setTimeout(function(){window.location.href = '$new_link'},3000);</script>";
        //header('Location: '.$new_link);
      }
    }
  }


  public function importProducts(){
    $this->checkAndSetProductImportFlag();

    $xml_products = $this->getProductsXmlFromFile();
    $this->unpdateProdutsFromXml($xml_products);
    Category::regenerateEntireNtree();
    $this->removeProductImportFlag();
    $this->autoNext($xml_products);
  }

  public function updateLayeredFilters(){
    $r = Db::getInstance()->executeS("SELECT cat_p.id_category, a.id_attribute_group FROM ps_category_product cat_p
    LEFT JOIN ps_product_attribute p_a
    ON p_a.id_product = cat_p.id_product
    LEFT JOIN ps_product_attribute_combination p_a_c
    ON p_a.id_product_attribute = p_a_c.id_product_attribute
    LEFT JOIN ps_attribute a
    ON a.id_attribute = p_a_c.id_attribute
    WHERE a.id_attribute_group IS NOT null
    GROUP BY cat_p.id_category, a.id_attribute_group");

    $cats_attrs_groups = array();
    foreach ($r as $key => $line) {
      if (isset($cats_attrs_groups[$line['id_category']])) {
        $cats_attrs_groups[$line['id_category']][] = $line['id_attribute_group'];
      }
      else {
        $cats_attrs_groups[$line['id_category']] = array($line['id_attribute_group']);
      }
    }

    // $tmp = 'a:7:{s:9:"shop_list";a:2:{i:0;i:1;i:1;i:3;}s:10:"categories";a:1:{i:0;i:367;}s:31:"layered_selection_subcategories";a:2:{s:11:"filter_type";i:0;s:17:"filter_show_limit";i:0;}s:23:"layered_selection_stock";a:2:{s:11:"filter_type";i:0;s:17:"filter_show_limit";i:0;}s:30:"layered_selection_manufacturer";a:2:{s:11:"filter_type";i:0;s:17:"filter_show_limit";i:0;}s:30:"layered_selection_price_slider";a:2:{s:11:"filter_type";i:1;s:17:"filter_show_limit";i:0;}s:23:"layered_selection_ag_91";a:2:{s:11:"filter_type";i:0;s:17:"filter_show_limit";i:0;}}';
    //
    // var_dump(unSerialize($tmp));
    $filters_tpl = array(
      "shop_list" => array(1,3),
      "categories" => array(),
      "layered_selection_subcategories" => array(
        "filter_type"=>0,
        "filter_show_limit" =>0
      ),
      "layered_selection_stock" => array(
        "filter_type"=>0,
        "filter_show_limit" =>0
      ),
      "layered_selection_manufacturer" => array(
        "filter_type"=>0,
        "filter_show_limit" =>0
      ),
      "layered_selection_price_slider" => array(
        "filter_type"=>0,
        "filter_show_limit" =>0
      )
    );

    $i = -1;
    foreach($cats_attrs_groups as $id_cat => $cat_attr_groups){
      $i ++;
      if ($id_cat != 367) {
        continue;
      }
      //var_dump($cat_attr_groups);
      $filters = $filters_tpl;
      $filters['categories'] = array($id_cat);

      foreach($cat_attr_groups as $key => $id_ag){
        $filters['layered_selection_ag_'.$id_ag] = array(
          "filter_type"=>0,
          "filter_show_limit" =>0
        );
      }


    }

  }

  public function autoSyncStock(){
    $site_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
    if (!isset($_GET['dev'])) {
      echo date('Y-m-d H:i:s').'<br>';
      $this->callPage("http://bo.habitatetjardin.com/dex/prestashop/maj_stock.asp?langue=fr");
      echo date('Y-m-d H:i:s').'<br>';
      $this->callPage("http://bo.habitatetjardin.com/dex/prestashop/maj_stock.asp?langue=es");
    }

    echo date('Y-m-d H:i:s').'<br>';
    $this->downloadStockFile();
    echo date('Y-m-d H:i:s').'<br>';
    $this->callPage($site_url."/modules/gecps/scripts/sync_stock.php?key=zddzdjjdhffkljgkjfhmedfklgegjht&diff_only=1");
    echo date('Y-m-d H:i:s').'<br>';

  }

  public function autoSyncProduct(){
    $site_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
    if (!isset($_GET['dev'])) {
      echo date('Y-m-d H:i:s').'<br>';
      $this->callPage("http://bo.habitatetjardin.com/dex/prestashop/export_produits.asp");
    }

    echo date('Y-m-d H:i:s').'<br>';
    $this->downloadProductFile();
    echo date('Y-m-d H:i:s').'<br>';
    $_GET['diff_only'] = 1;
    $this->importProducts();
    echo date('Y-m-d H:i:s').'<br>';
    $this->callPage("$site_url/scraper?action=disableProductNoStockAllShops");
    echo date('Y-m-d H:i:s').'<br>';



  }

  public function callPage($url){
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTPHEADER => array(
         'Content-Type: application/x-www-form-urlencoded',
      ),
      CURLOPT_RETURNTRANSFER => true
    ));
    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
  }

  public function autoSyncOrder(){
    $this->module::sendOrderToAsp();
  }


}
