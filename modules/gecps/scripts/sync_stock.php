<?php

include('../../../config/config.inc.php');
include(_PS_ROOT_DIR_ .'/init.php');
// @ini_set('display_errors','on');
// @ini_set('max_execution_time', 240);

if(Tools::getValue('key') != 'zddzdjjdhffkljgkjfhmedfklgegjht' ) {
    die('Accès refusé');
}

$files = glob(_PS_ROOT_DIR_ . '/download/stock_files/*.xml');
$diff_only = false;
if (isset($_GET['diff_only']) && $_GET['diff_only']==1) {
  $diff_only = 1;
}



foreach ($files as $file_path) {
  $file = basename($file_path);
	if (strpos($file, 'stock') !== false) {
    if (strpos($file, 'fr') !== false) {
      $id_shop = 1;
      $xml_stock = simplexml_load_file(_PS_ROOT_DIR_ . "/download/stock_files/export_stock_fr.xml");
      $xml_content = file_get_contents(_PS_ROOT_DIR_ . "/download/stock_files/export_stock_fr.xml");
    } elseif (strpos($file, 'es') !== false) {
      $id_shop = 3;
      $xml_stock = simplexml_load_file(_PS_ROOT_DIR_ . "/download/stock_files/export_stock_es.xml");
      $xml_content = file_get_contents(_PS_ROOT_DIR_ . "/download/stock_files/export_stock_fr.xml");
    } else {
      $xml_stock = null;
      $xml_content = false;
    }
	} else {
    $xml_stock = null;
  }

  if ($diff_only) {
    $files_done = glob(_PS_ROOT_DIR_ . '/download/stock_files/done/*'.$file);
    $last_file_done = $files_done[count($files_done)-1];
    $xml_stock_done = simplexml_load_file($last_file_done);
    $xml_stock_done_md5 = array();
    $i = 0;
    foreach ($xml_stock_done->Products->Product as $product) {
      $i++;
      if ($i>1) {
        //continue;
      }
      $xml_stock_done_md5[trim($product->Reference)] = md5($product->asXML());

      //echo md5($product->asXML()).'<br>';

    }
  }


  $products_exsit_in_xml =array();
  $combs_exsit_in_xml = array();
  if (isset($xml_stock)) {

    foreach ($xml_stock->Products->Product as $product) {
      if ($product->Reference->__toString()) {
        $products_exsit_in_xml[] = trim($product->Reference->__toString());
      }
      if (count($product->Combinaisons->children()) > 0) {
        foreach ($product->Combinaisons->children() as $combinaison) {
          if ($combinaison->Reference->__toString()) {
            $combs_exsit_in_xml[] = trim($combinaison->Reference->__toString());
          }
        }
      }

    }
  }

  // $not_in = '';
  // foreach($combs_exsit_in_xml as $ref){
  //   $not_in .= "'".$ref."'".",";
  // }
  // $not_in = trim($not_in,',');
  // //$sql = "UPDATE `ps_product_shop` SET `active`=0 where ps_product_shop.id_product IN (SELECT id_product FROM ps_product WHERE reference not in ($not_in))";
  // $sql = "select reference from ps_product where reference not in ($not_in)";
  // $r = Db::getInstance()->executeS($sql);var_dump(count($combs_exsit_in_xml));
  // //$sql = "UPDATE `ps_product` SET `active`=0 where reference not in ($not_in)";
  // //$r = Db::getInstance()->execute($sql);





  $i = 0;
  if (isset($xml_stock)) {
    $refs_in_xml = [];
    $cats_excl = Configuration::get("GECPS_CATS_EXCL");
    foreach ($xml_stock->Products->Product as $product) {

      $i++;
      if ($i>1) {
        //continue;
      }
      if (trim($product->Reference) !== '103094') {

        //continue;
      }
      // if (!in_array(trim($product->Reference),$refs)) {
      //   //continue;
      // }
      if ($id_shop==3&&$cats_excl) {
        $sql = "SELECT p.id_product FROM ps_product p, ps_category_product cp where p.id_product = cp.id_product and cp.id_category in ($cats_excl) and p.reference = '".trim($product->Reference)."' ";
        $r = Db::getInstance()->executeS($sql);
        if ($r) {
          continue;
        }
      }
      if ($diff_only) {
        if (isset($xml_stock_done_md5[trim($product->Reference)])) {
          if (md5($product->asXML()) == $xml_stock_done_md5[trim($product->Reference)]) {
            continue;
          }
        }

      }

      //echo md5($product->asXML()).'<br>';
      echo($product->Reference.'<br>');
      //$refs_in_xml[]=$product->Reference;
      //continue;

      //ob_flush();
      $id_product = false;
      if ($product->Reference->__toString()) {
        $id_product = Db::getInstance()->getValue('
          SELECT id_product
          FROM '._DB_PREFIX_.'product
          WHERE reference = \''.pSql(trim($product->Reference)).'\'
        ');
      } elseif (count($product->Combinaisons->children()) > 0) {
        foreach ($product->Combinaisons->children() as $combinaison) {
          if ($combinaison->Reference->__toString()) {
            $id_product = Db::getInstance()->getValue('
              SELECT id_product
              FROM '._DB_PREFIX_.'product_attribute
              WHERE reference = \''.pSql(trim($combinaison->Reference)).'\'
            ');
            break;
          }
        }
      }

      if (isset($id_product) && $id_product) {

        Shop::setContext(Shop::CONTEXT_SHOP, (int)$id_shop);

        $objProduct = new Product((int)$id_product, false, null, (int)$id_shop);

        $objProduct->id_shop_default = (int)$id_shop;

        if (count($product->Combinaisons->children()) > 0 || !$product->Price->__toString()) {
          $objProduct->price = number_format(0, 6);
        } else {
          $product_price = str_replace(',', '.', trim($product->Price));
          $product_price = number_format(floatval($product_price), 6);
          $objProduct->price = str_replace(',', '', $product_price);
          // if (isset($product->Ecotax)) {
          //   $product_ecotax = str_replace(',', '.', trim($product->Ecotax));
          //   $product_ecotax = number_format(floatval($product_ecotax), 6);
          //   $objProduct->price -= $product_ecotax;
          // }
        }
        if (isset($product->TaxRulesId)) {
          if ($id_shop == 1) {
            $objProduct->id_tax_rules_group = 1;
          }
          elseif($id_shop == 3) {
            $objProduct->id_tax_rules_group = 4;
          }

        }

        if (isset($product->WholesalePrice)) {
          $product_wholesale_price = str_replace(',', '.', trim($product->WholesalePrice));
          $product_wholesale_price = number_format(floatval($product_wholesale_price), 6);
          $objProduct->wholesale_price = str_replace(',', '', $product_wholesale_price);
        }

        if (isset($product->OnSale)) {
          $objProduct->on_sale = (int)$product->OnSale;
        }

        if (isset($product->SupplierReference)) {
          $objProduct->supplier_reference = trim($product->SupplierReference);
        }

        if (isset($product->EAN13)) {
          $objProduct->ean13 = trim($product->EAN13);
        }

        if (isset($product->UPC)) {
          $objProduct->upc = trim($product->UPC);
        }

        if (isset($product->MinimalQuantity)) {
          $objProduct->minimal_quantity = (int)$product->MinimalQuantity;
        }

        if (isset($product->Visibility)) {
          if (trim(strtolower($product->Visibility)) != 'both' && trim(strtolower($product->Visibility)) != 'none' && trim(strtolower($product->Visibility)) != 'catalog' && trim(strtolower($product->Visibility)) != 'search') {
            $objProduct->visibility = 'both';
          } else {
            $objProduct->visibility = trim(strtolower($product->Visibility));
          }
        }

        if (isset($product->AdditionnalShippingCost)) {
          $additional_shipping_cost = str_replace(',', '.', trim($product->AdditionnalShippingCost));
          $additional_shipping_cost = number_format(floatval($additional_shipping_cost), 2);
          $objProduct->additional_shipping_cost = (int)str_replace(',', '', $additional_shipping_cost);
        }

        if (isset($product->Unity)) {
          $objProduct->unity = trim($product->Unity);
        }

        if (isset($product->UnitPrice)) {
          $objProduct->unit_price = trim($product->UnitPrice);
        }

        $product_available_now = array();
        if (count($product->TextInStock->children()) > 0) {
          foreach ($product->TextInStock->children() as $iso => $textinstock) {
            $id_lang = Language::getIdByIso($iso);
            $textinstock = strip_tags($textinstock);
            $product_available_now[(int)$id_lang] = $textinstock;
          }

          $objProduct->available_now = $product_available_now;
        }

        $product_available_later = array();
        if (count($product->TextOutOfStock->children()) > 0) {
          foreach ($product->TextOutOfStock->children() as $iso => $textoutofstock) {
            $id_lang = Language::getIdByIso($iso);
            $textoutofstock = strip_tags($textoutofstock);
            $product_available_later[(int)$id_lang] = $textoutofstock;
          }

          $objProduct->available_later = $product_available_later;
        }

        if (isset($product->AvailableForOrder)) {
          $objProduct->available_for_order = (int)$product->AvailableForOrder;
        }
        if (isset($product->AvailableDate)) {
          if ($product->AvailableDate->__toString()) {
            $datetime_available = new DateTime();
            $date = $datetime_available->createFromFormat('d/m/Y', trim($product->AvailableDate));
            $availabledate = $date->format('Y-m-d');
            $objProduct->available_date = $availabledate;
          }
          else {
            $objProduct->available_date = "";
          }

        }

        if (isset($product->ShowPrice)) {
          $objProduct->show_price = (int)$product->ShowPrice;
        }

        if (isset($product->Active)) {
          if (isset($product->Stock) && (int)$product->Stock == 0 && count($product->Combinaisons->children())==0) {
            $objProduct->active = 0;
          }
          else {
            $objProduct->active = (int)$product->Active;
          }

        }

        if (isset($product->onlineOnly)) {
          if (trim(ucfirst($product->OnlineOnly)) == 'Vrai') {
            $objProduct->online_only = 1;
          } elseif (trim(ucfirst($product->OnlineOnly)) == 'Faux') {
            $objProduct->online_only = 0;
          } else {
            $objProduct->online_only = (int)$product->OnlineOnly;
          }
        }

        // VARIABLE A UTILISER POUR DES DEVELOPPEMENTS FUTURS
        // if ($product->Thematic->__toString()) {
        //   $objProduct->thematic = (int)$product->Thematic;
        // }

        $objProduct->update();

        if (isset($product->Stock)) {
          if ($id_shop == 3) {
            $quantity = (int)$product->Stock==9999?0:(int)$product->Stock;
          }
          else {
            $quantity = (int)$product->Stock;
          }
          StockAvailable::setQuantity((int)$objProduct->id, 0, $quantity, (int)$id_shop);
        }

        if (isset($product->SupplierReference)) {
          $objProduct->addSupplierReference((int)$objProduct->id_supplier, 0, trim($product->SupplierReference), (float)$objProduct->wholesale_price);
        }

        Db::getInstance()->delete('specific_price','id_product='.(int)$objProduct->id.' and id_shop='.$id_shop);
        if (count($product->Discount->children()) > 0 && (($product->Discount->Amount->__toString() && $product->Discount->Amount > 0) || ($product->Discount->Percent->__toString() && $product->Discount->Percent > 0))) {
          if ($product->Discount->DateFrom->__toString()) {
            $datetime_from = new DateTime();
            $date_from = $datetime_from->createFromFormat('d/m/Y H:i:s', trim($product->Discount->DateFrom.' 00:00:00'));
            $from = $date_from->format('Y-m-d H:i:s');
          } else {
            $from = '0000-00-00 00:00:00';
          }
          if ($product->Discount->DateTo->__toString()) {
            $datetime_to = new DateTime();
            $date_to = $datetime_from->createFromFormat('d/m/Y H:i:s', trim($product->Discount->DateTo.' 00:00:00'));
            $to = $date_to->format('Y-m-d H:i:s');
          } else {
            $to = '0000-00-00 00:00:00';
          }

          $exists_specific = SpecificPrice::exists((int)$objProduct->id, 0, (int)$id_shop, 0, 0, 0, 0, 1, $from, $to);
          if (count($product->Combinaisons->children()) == 0) {
            if (!$exists_specific) {
              $objSpecificPrice = new SpecificPrice();
              $objSpecificPrice->id_product_attribute = 0;
              $objSpecificPrice->id_currency = 0;
              $objSpecificPrice->id_specific_price_rule = 0;
              $objSpecificPrice->id_country = 0;
              $objSpecificPrice->id_group = 0;
              $objSpecificPrice->id_customer = 0;
              $objSpecificPrice->price = number_format(-1, 6);
              $objSpecificPrice->from_quantity = 1;
              $objSpecificPrice->reduction_tax = 0;
              $objSpecificPrice->id_shop = $id_shop;
            } else {
              $objSpecificPrice = new SpecificPrice($exists_specific);
            }

            $objSpecificPrice->id_product = (int)$objProduct->id;
            $objSpecificPrice->id_shop = (int)$id_shop;

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
          else {
              $exists_specific = SpecificPrice::exists((int)$objProduct->id, 0, (int)$id_shop, 0, 0, 0, 0, 1, $from, $to);
              $objSpecificPrice = new SpecificPrice($exists_specific);
              $objSpecificPrice->delete();
              $exists_specific = SpecificPrice::exists((int)$objProduct->id, 0, 0, 0, 0, 0, 0, 1, $from, $to);
              $objSpecificPrice = new SpecificPrice($exists_specific);
              $objSpecificPrice->delete();

          }

        }


        if (count($product->Combinaisons->children()) > 0) {

          $no_stock = true;

          foreach ($product->Combinaisons->children() as $combinaison) {
            if ($combinaison->Reference->__toString()) {
              $refs_in_xml[]=$combinaison->Reference->__toString();
              $id_product_attribute = Db::getInstance()->getValue('
                SELECT id_product_attribute
                FROM '._DB_PREFIX_.'product_attribute
                WHERE reference = \''.pSql(trim($combinaison->Reference)).'\'
                AND id_product = '.(int)$objProduct->id
              );
            }

            if (isset($id_product_attribute) && $id_product_attribute) {
              $objProductAttribute = new Combination((int)$id_product_attribute, null, (int)$id_shop);

              if ($combinaison->SupplierReference->__toString()) {
                $objProductAttribute->supplier_reference = trim($combinaison->SupplierReference);
                $objProduct->addSupplierReference((int)$objProduct->id_supplier, $objProductAttribute->id, trim($combinaison->SupplierReference), (float)$objProductAttribute->wholesale_price);
              }

              if ($combinaison->Reference->__toString()) {
                $objProductAttribute->reference = trim($combinaison->Reference);
              }

              if ($combinaison->EAN13->__toString()) {
                $objProductAttribute->ean13 = trim($combinaison->EAN13);
              }

              if ($combinaison->UPC->__toString()) {
                $objProductAttribute->upc = trim($combinaison->UPC);
              }

              if (isset($combinaison->WholesalePrice)) {
                $product_attribute_wholesale_price = str_replace(',', '.', trim($combinaison->WholesalePrice));
                $product_attribute_wholesale_price = number_format(floatval($product_attribute_wholesale_price), 6);
                $objProductAttribute->wholesale_price = str_replace(',', '', $product_attribute_wholesale_price);
              }

              if (isset($combinaison->Price)) {
                $product_attribute_price = str_replace(',', '.', trim($combinaison->Price));
                $product_attribute_price = number_format(floatval($product_attribute_price), 6);
                $objProductAttribute->price = str_replace(',', '', $product_attribute_price);
              }

              if (isset($combinaison->ImpactOnWeight)) {
                $product_attribute_weight = str_replace(',', '.', trim($combinaison->ImpactOnWeight));
                $product_attribute_weight = number_format(floatval($product_attribute_weight), 6);
                $objProductAttribute->weight = str_replace(',', '', $product_attribute_weight);
              }

              if (isset($combinaison->Stock)) {
                if ($id_shop == 3) {
                  $quantity = (int)$combinaison->Stock==9999?0:(int)$combinaison->Stock;
                }
                else {
                  $quantity = (int)$combinaison->Stock;
                }
                $objProductAttribute->stock = $quantity;
                StockAvailable::setQuantity((int)$objProduct->id, (int)$objProductAttribute->id, $quantity, (int)$id_shop);
                if ((int)$combinaison->Stock > 0) {
                  $no_stock = false;
                }
              }

              if (isset($combinaison->MinimalQuantity)) {
                $objProductAttribute->minimal_quantity = (int)$combinaison->MinimalQuantity;
              }

              if (isset($combinaison->Ecotax)) {
                $product_attribute_ecotax = str_replace(',', '.', trim($combinaison->Ecotax));
                $product_attribute_ecotax = number_format(floatval($product_attribute_ecotax), 6);
                //$objProductAttribute->ecotax = str_replace(',', '', $product_attribute_ecotax);
                $objProductAttribute->ecotax = 0;
              }

              if (isset($combinaison->AvailableDate)) {
                if ($combinaison->AvailableDate->__toString()) {
                  $datetime_available = new DateTime();
                  $date = $datetime_available->createFromFormat('d/m/Y', trim($combinaison->AvailableDate));
                  $availabledate = $date->format('Y-m-d');
                  $objProductAttribute->available_date = $availabledate;
                }
                else {
                  $objProductAttribute->available_date = "";
                }

              }

              $objProductAttribute->update();

              if (count($combinaison->Discount->children()) > 0 && ((isset($combinaison->Discount->Amount) && $combinaison->Discount->Amount > 0) || (isset($combinaison->Discount->Percent) && $combinaison->Discount->Percent > 0))) {
                if (isset($combinaison->Discount->DateFrom)) {
                  if ($combinaison->Discount->DateFrom->__toString()) {
                    $datetime_from = new DateTime();
                    $date_from = $datetime_from->createFromFormat('d/m/Y H:i:s', trim($combinaison->Discount->DateFrom.' 00:00:00'));
                    $from = $date_from->format('Y-m-d H:i:s');
                  }
                  else {
                    $from = '0000-00-00 00:00:00';
                  }

                }
                if (isset($combinaison->Discount->DateTo)) {
                  if ($combinaison->Discount->DateTo->__toString()) {
                    $datetime_to = new DateTime();
                    $date_to = $datetime_to->createFromFormat('d/m/Y H:i:s', trim($combinaison->Discount->DateTo.' 00:00:00'));
                    $to = $date_to->format('Y-m-d H:i:s');
                  }
                  else {
                    $to = '0000-00-00 00:00:00';
                  }
                }


                $exists_specific = SpecificPrice::exists((int)$objProduct->id, (int)$objProductAttribute->id, (int)$id_shop, 0, 0, 0, 0, 1, $from, $to);

                if (!$exists_specific) {
                  $objSpecificPrice2 = new SpecificPrice();
                  $objSpecificPrice2->id_currency = 0;
                  $objSpecificPrice2->id_specific_price_rule = 0;
                  $objSpecificPrice2->id_country = 0;
                  $objSpecificPrice2->id_group = 0;
                  $objSpecificPrice2->id_customer = 0;
                  $objSpecificPrice2->price = number_format(-1, 6);
                  $objSpecificPrice2->from_quantity = 1;
                  $objSpecificPrice2->reduction_tax = 0;
                  $objSpecificPrice2->id_shop = $id_shop;
                } else {
                  $objSpecificPrice2 = new SpecificPrice($exists_specific);
                }

                $objSpecificPrice2->id_product = (int)$objProduct->id;
                $objSpecificPrice2->id_product_attribute = (count($product->Combinaisons->children())>0?(int)$objProductAttribute->id:0);
                $objSpecificPrice2->id_shop = (int)$id_shop;

                if ($combinaison->Discount->Amount->__toString() && $combinaison->Discount->Amount > 0) {
                  $price_reduction = str_replace(',', '.', trim($combinaison->Discount->Amount));
                  $price_reduction = number_format(floatval($price_reduction), 6);
                  $objSpecificPrice2->reduction = str_replace(',', '', $price_reduction);
                  $objSpecificPrice2->reduction_type = 'amount';
                } else {
                  $objSpecificPrice2->reduction = number_format(floatval($combinaison->Discount->Percent) / 100, 6);
                  $objSpecificPrice2->reduction_type = 'percentage';
                }

                $objSpecificPrice2->from = $from;
                $objSpecificPrice2->to = $to;

                if (!$exists_specific) {
                  $objSpecificPrice2->add();
                } else {
                  $objSpecificPrice2->update();
                }
              }
            }
          }
          if ($no_stock) {
            $objProduct->active = 0;
            $objProduct->save();
          }

        }
      }
    }
    // $refs_in = "";
    // foreach($refs_in_xml as $ref){
    //   $refs_in .= '"'.$ref.'",';
    // }
    // $refs_in = "(".trim($refs_in,",").")";
    // $sql = "";
    if ($xml_content) {
      $r = Db::getInstance()->executeS("
        SELECT pa.reference,sa.id_stock_available FROM `ps_stock_available` sa, ps_product_attribute pa WHERE pa.id_product_attribute = sa.id_product_attribute AND sa.id_product_attribute!=0 AND id_shop=$id_shop and sa.quantity !=0
        UNION
        SELECT p.reference,sa.id_stock_available FROM `ps_stock_available` sa, ps_product p WHERE p.id_product = sa.id_product AND sa.id_product_attribute=0 AND id_shop=$id_shop and sa.quantity !=0");
      foreach ($r as $key => $l) {
        // if ($l['reference']!="58286") {
        //   continue;
        // }
        $exists_in_xml = strpos($xml_content,"<Reference><![CDATA[".$l['reference']."]]></Reference>");
        if ($exists_in_xml === false) {
          Db::getInstance()->update("stock_available",['quantity'=>0],"id_stock_available=".$l['id_stock_available']);
        }
      }

    }

  }
}
//var_dump($i);
die('Terminé.');
