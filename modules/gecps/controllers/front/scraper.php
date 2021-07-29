<?php
if (!class_exists('simple_html_dom_node')) {
  include(_PS_MODULE_DIR_."gecps/classes/simple_html_dom.php");
}
include(_PS_MODULE_DIR_."iqitelementor/src/IqitElementorCategory.php");

class gecpsscraperModuleFrontController extends ModuleFrontController
{
  public $params;

  public function initContent()
  {
    //parent::initContent();
    $this->initParams();
	  $this->checkAction();
    $this->setTemplate('module:gecps/views/templates/front/scraper.tpl');


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

  public function callPage($url,$data=array()){
    $options = array(
		 	//CURLOPT_POST=>true,
			//CURLOPT_PUT=> true,
			CURLOPT_COOKIESESSION=> true,
            CURLOPT_CUSTOMREQUEST  =>"POST",        //set request type post or get
            CURLOPT_POST           =>true,        //set to GET
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2', //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
           // CURLOPT_HEADER         => true,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            //CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			CURLOPT_POSTFIELDS		=>	$data,
			CURLOPT_URL			=>	$url,
			CURLOPT_POSTREDIR=> 3
        );

	 curl_setopt_array( $this->curl, $options );
		$result = curl_exec ($this->curl);

		//var_dump($result);

		return $result;
  }

  public function updateCategoriesInfoFromFO(){
    $this->curl = curl_init();
    $result = $this->callPage($this->params['sitemap_url']);
    $html = new simple_html_dom();
		$html->load($result);
    $cat_links =array();
    $cat_infos = array();

    $cat_link_lines = $html->find('#header ul#onglets>li>a');
    foreach ($cat_link_lines as $key => $link) {
      $cat_links[]=array('name' => html_entity_decode(trim($link->plaintext)), 'url' => $this->params['fo_base'].$link->href);
    }

    $cat_link_lines = $html->find('.soussousmn a');
    foreach ($cat_link_lines as $key => $link) {
      $cat_links[]=array('name' => html_entity_decode(trim($link->plaintext)), 'url' => $this->params['fo_base'].$link->href);
    }


    foreach ($cat_links as $key => $cat_link) {
      if ($key>40) {
        //continue;
      }
      if (count($cat_infos)>10) {
        //continue;
      }
      if ($cat_link['url'] != 'https://www.habitatetjardin.com/piscine/abri-piscine,b1568.html') {
        continue;
      }

      $cat_info = array();
      $cat_info['url'] = $cat_link['url'];
      $cat_info['name'] = $cat_link['name'];
      $result = $this->callPage($cat_link['url']);
      $html = new simple_html_dom();
  		$html->load($result);
      $cat_desc_html =$html->find('.jscontent',0);
      if (!$cat_desc_html) {
        $cat_desc_html =$html->find('.pr_hj',0);
      }
      if (!$cat_desc_html) {
        $cat_desc_html =$html->find('.newslettertexte',0);
      }
      if ($cat_desc_html) {
        $cat_desc = trim($cat_desc_html->plaintext);
        if ($cat_desc) {
          $cat_info['desc'] = $cat_desc;
          $cat_infos[]=$cat_info;
        }
      }
      else {

      }
    }
    curl_close ($this->curl);
    var_dump($cat_infos);

    foreach ($cat_infos as $key => $cat_info) {

      $results = Category::searchByName(1,$cat_info['name']);var_dump($cat_info['name']);
      if ($results) {
        $cat = new Category($results[0]['id_category']);
        $need_updated = 0;
        if ($cat->description[1] != $cat_info['desc']) {
          $cat->description[1] = $cat_info['desc'];
          $need_updated = 1;
        }


        if ($need_updated) {
          $cat->update();
        }

      }

    }
  }

  public function updateCategoriesFilters(){
    $this->curl = curl_init();
    $result = $this->callPage($this->params['sitemap_url']);
    $html = new simple_html_dom();
		$html->load($result);
    $cat_links =array();
    $cat_infos = array();

    $cat_link_lines = $html->find('#header ul#onglets>li>a');
    foreach ($cat_link_lines as $key => $link) {
      $cat_links[]=array('name' => html_entity_decode(trim($link->plaintext)), 'url' => $this->params['fo_base'].$link->href);
    }

    $cat_link_lines = $html->find('.soussousmn a');
    foreach ($cat_link_lines as $key => $link) {
      $cat_links[]=array('name' => html_entity_decode(trim($link->plaintext)), 'url' => $this->params['fo_base'].$link->href);
    }


    foreach ($cat_links as $key => $cat_link) {
      if ($key>40) {
        //continue;
      }
      if (count($cat_infos)>10) {
        //continue;
      }
      if ($cat_link['url'] != 'https://www.habitatetjardin.com/piscine/pompe-a-chaleur,c641.html') {
        continue;
      }

      $cat_info = array();
      $cat_info['url'] = $cat_link['url'];
      $cat_info['name'] = $cat_link['name'];
      $result = $this->callPage($cat_link['url']);
      $html = new simple_html_dom();
  		$html->load($result);
      $cat_fitler_tiltes =$html->find('.filtertitle');
      if (count($cat_fitler_tiltes)) {
        foreach ($cat_fitler_tiltes as $key => $cat_fitler_tilte) {
          $filter_name = html_entity_decode($cat_fitler_tilte->plaintext);

          $filter_name = trim($filter_name);
          // $filter_name = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $filter_name);
          $filter_name = str_replace(array("<", ">", "=", "{", "}"), '', $filter_name);
          $filter_name = mb_substr(strip_tags($filter_name), 0, 128, "utf-8");
          $filter_name = str_replace('É','é',$filter_name);
          $filter_name = ucfirst(strtolower($filter_name));

          if (!in_array($filter_name,array('Marques','Prix'))) {
            $r = $this->getFeatureIdByName($filter_name);
            if ($r) {
              var_dump($r);
            }
          }

        }
      }

    }

  }

  public function getFeatureIdByName($name,$id_lang=1){
    $sql = 'SELECT id_feature FROM `ps_feature_lang` WHERE `id_lang` = '.$id_lang.' AND `name` LIKE "'.$name.'"';
    $r = Db::getInstance()->executeS($sql);
    return $r;
  }

  public function test(){



  }

  public function getFromAspPsapi($action,$to_array=true){
    $url = "http://sav.habitatetjardin.com/es/psapi.asp?action=".$action;
    $json = file_get_contents($url);
    if ($to_array) {
      $json = json_decode($json,true);
      return $json['r'];
    }
    else {
      return $json;
    }

  }



  public function syncProductExternalDocumentFromAsp(){
    $sql = "TRUNCATE `ps_asp_external_links`;";
    Db::getInstance()->execute($sql);
    $last = $this->getFromAspPsapi('count_article_lien');
    $count_article_lien = (int)$last[0]['count'];
    //$max_id = 2;
    $int = 500;
    $refs = Gecps::getAllReferences();
    for ($i=0; $i < ($count_article_lien); $i+=$int) {
      // echo "article_lien&from=$i&int=$int".'<br/>';
      $r = $this->getFromAspPsapi("article_lien&from=$i&int=$int");

      if ( count($r)>0) {

        $s = 'INSERT INTO `ps_asp_external_links` VALUE ';
        $n = 0;
        foreach ($r as $key => $l) {
          $l = $this->checkExternalLinksFormat($l);
          if (!in_array($l['fk_article'],$refs)) {
            continue;
          }
          //$s .= "('".$l['id_lien']."','".$l['fk_article']."','".$l['titre']."','".$l['lien']."'),";
          $s .= '("'.$l['id_lien'].'","'.$l['fk_article'].'","'.$l['titre'].'","'.$l['lien'].'"),';
          $n ++;

        }
        $sql = trim($s,',');
        if ($n) {
          $r = Db::getInstance()->execute($sql);
        }

        // if (!$r) {
        //   echo $sql."<br/>";
        // }
      }


    }
  }
  public function checkExternalLinksFormat($l){
    if (strpos($l['titre'],'http')!== false && strpos($l['lien'],'http')== false) {
      $lien = $l['titre'];
      $l['titre'] = $l['lien'];
      $l['lien'] = $lien;
    }
    //$l['titre'] = str_replace("'","\'",$l['titre']);
    return $l;
  }

  public function syncOptionRecommFromAsp(){
    $sql = "TRUNCATE `ps_asp_recommended_options`;";
    Db::getInstance()->execute($sql);
    $last = $this->getFromAspPsapi('count_article_option');
    $count_article_option = (int)$last[0]['count'];
    //$count_article_option = 100;
    //$max_id = 2;
    $int = 1000;
    $refs = Gecps::getAllReferences();
    for ($i=0; $i < ($count_article_option); $i+=$int) {
      $r = $this->getFromAspPsapi("article_option&from=$i&int=$int");

      if ( count($r)>0) {

        $s = 'INSERT INTO `ps_asp_recommended_options` VALUE ';
        $n = 0;
        foreach ($r as $key => $l) {

          if (!in_array($l['fk_article'],$refs)) {
            continue;
          }
          if (!in_array($l['fk_option'],$refs)) {
            continue;
          }
          //$s .= "('".$l['id_lien']."','".$l['fk_article']."','".$l['titre']."','".$l['lien']."'),";
          $s .= '("'.$l['id_option'].'","'.$l['fk_article'].'","'.$l['fk_option'].'","'.$l['type_option'].'"),';
          $n++;
        }
        $sql = trim($s,',');
        if ($n) {
          $r = Db::getInstance()->execute($sql);
        }

        // if (!$r) {
        //   echo $sql."<br/>";
        // }
      }
    }
  }

  public function synPackFromAsp(){
    $sql = "TRUNCATE `ps_asp_pack`;";
    Db::getInstance()->execute($sql);
    $last = $this->getFromAspPsapi('count_pack');
    $count_pack = (int)$last[0]['count'];
    //$max_id = 2;
    $int = 1000;
    $refs = Gecps::getAllReferences();
    for ($i=0; $i < ($count_pack); $i+=$int) {
      $r = $this->getFromAspPsapi("pack&from=$i&int=$int");

      if ( count($r)>0) {

        $s = 'INSERT INTO `ps_asp_pack` VALUE ';
        $n =0;
        foreach ($r as $key => $l) {

          if (!in_array($l['fk_article'],$refs)) {
            continue;
          }
          $s .= '("'.$l['id_pack'].'","'.$l['fk_article'].'","'.$l['encombrement'].'","'.$l['online'].'","'.$l['ordre'].'"),';
          $n++;
        }
        $sql = trim($s,',');
        if ($n) {
          $r = Db::getInstance()->execute($sql);
        }

        // if (!$r) {
        //   echo $sql."<br/>";
        // }
      }
    }
    $this->synPackProductFromAsp();
  }

  public function synPackProductFromAsp(){
    $sql = "TRUNCATE `ps_asp_pack_product`;";
    Db::getInstance()->execute($sql);
    $last = $this->getFromAspPsapi('count_article_pack');
    $count_pack_product = (int)$last[0]['count'];
    //$max_id = 2;
    $int = 1000;
    $refs = Gecps::getAllReferences();
    for ($i=0; $i < ($count_pack_product); $i+=$int) {
      $r = $this->getFromAspPsapi("article_pack&from=$i&int=$int");

      if ( count($r)>0) {

        $s = 'INSERT INTO `ps_asp_pack_product` VALUE ';
        $n =0;
        foreach ($r as $key => $l) {

          if (!in_array($l['fk_article'],$refs)) {
            continue;
          }
          $s .= '("'.$l['id_article_pack'].'","'.$l['fk_article'].'","'.$l['fk_pack'].'","'.$l['pvttc'].'"),';
          $n++;

        }
        $sql = trim($s,',');
        if ($n) {
          $r = Db::getInstance()->execute($sql);
        }

        // if (!$r) {
        //   echo $sql."<br/>";
        // }
      }
    }
  }

  public function synDocProductFromAsp(){
    $sql = "TRUNCATE `ps_asp_doc_product`;";
    Db::getInstance()->execute($sql);
    $last = $this->getFromAspPsapi('count_fiche_complementaire_attrib');
    $count_fiche_complementaire_attrib = (int)$last[0]['count'];
    //$max_id = 2;
    $int = 1000;
    $refs = Gecps::getAllReferences();
    for ($i=0; $i < ($count_fiche_complementaire_attrib); $i+=$int) {
      $r = $this->getFromAspPsapi("fiche_complementaire_attrib&from=$i&int=$int");

      if ( count($r)>0) {

        $s = 'INSERT INTO `ps_asp_doc_product` VALUE ';
        $n =0;
        foreach ($r as $key => $l) {

          $s .= '("'.$l['id_attrib'].'","'.$l['fk_fiche'].'","'.$l['fk_article'].'"),';
          $n ++;
        }
        $sql = trim($s,',');
        if ($n) {
          $r = Db::getInstance()->execute($sql);
        }
        // if (!$r) {
        //   echo $sql."<br/>";
        // }
      }
    }
  }

  public function synDocFromAsp(){
    $sql = "TRUNCATE `ps_asp_doc`;";
    Db::getInstance()->execute($sql);
    $last = $this->getFromAspPsapi('count_fiche_complementaire');
    $count_fiche_complementaire = (int)$last[0]['count'];
    //$max_id = 2;
    $int = 1000;
    $refs = Gecps::getAllReferences();
    for ($i=0; $i < ($count_fiche_complementaire); $i+=$int) {
      $r = $this->getFromAspPsapi("fiche_complementaire&from=$i&int=$int");

      if ( count($r)>0) {

        $s = 'INSERT INTO `ps_asp_doc` VALUE ';
        $n = 0;
        foreach ($r as $key => $l) {

          $s .= '("'.$l['id_fiche'].'","","'.$l['titre'].'","'.$l['langue'].'"),';
          $n++;
        }
        $sql = trim($s,',');
        if ($n) {
          $r = Db::getInstance()->execute($sql);
        }

        if (!$r) {
          echo $sql."<br/>";
        }
      }
    }

  }

  public function synDocTextFromAsp(){
    $sql = "SELECT id_doc FROM ps_asp_doc";
    $r = Db::getInstance()->executeS($sql);
    foreach ($r as $key => $l) {
      if ($key > 10) {
        //continue;
      }
      $r = $this->getFromAspPsapi("fiche_complementaire_texte&id=".$l['id_doc'],false);
      //var_dump("fiche_complementaire_texte&id=".$l['id_doc']);
      $text = str_replace('"',"'",$r);
      $sql = 'UPDATE `ps_asp_doc` SET `text`="'.$text.'"  WHERE `id_doc` = '.$l['id_doc'];
      Db::getInstance()->execute($sql);
    }
  }

  // public static function makeProductFlagsStr($flags_str,$action,$new_str){
  //   $flags = explode(';',$flags_str);
  //   if ($action == 'add') {
  //     if (!in_array($new_str,$flags)) {
  //       $flags[] = $new_str;
  //     }
  //   }
  //   elseif($action == 'rm') {
  //     if (($key = array_search($new_str, $flags)) !== false) {
  //         unset($flags[$key]);
  //     }
  //   }
  //   return trim(implode(';',$flags),';');
  // }

  // public function removeProductsFlags($flag){
  //   $r = Db::getInstance()->executeS('SELECT * FROM ps_product where flags like "%'.$flag.'%"');
  //   foreach($r as $p){
  //     $new_flags = self::makeProductFlagsStr($p['theme_flags'],'rm',$flag);
  //     Db::getInstance()->update('product',['theme_flags'=>$new_flags],"id_product =".$p['id_product']);
  //     Db::getInstance()->update('product_shop',['theme_flags'=>$new_flags],"id_product =".$p['id_product']);
  //   }
  //
  // }

  public function syncFlashSales(){
    $vs_cat = new Category(Configuration::get("GECPS_FS_CAT"));
    $vs_cat->cleanAssoProducts();
    $last = $this->getFromAspPsapi('count_ventes_flash');
    $count = (int)$last[0]['count'];
    //$max_id = 2;
    $int = 1000;
    for ($i=0; $i < ($count); $i+=$int) {
      $r = $this->getFromAspPsapi("ventes_flash&from=$i&int=$int");

      if ( count($r)>0) {
        foreach ($r as $key => $l) {
          if ($this->checkIfFlashSaleEnded($l)) {
            continue;
          }
          // $end_date
          $p = self::getProductIdFromAspRef($l['fk_article']);

          if ($p['id_product']) {
            // if ($p['id_product'] != 2863) {
            //   continue;
            // }
            $product = new Product($p['id_product']);
            $product->addToCategories($vs_cat->id_category);
            if ($p['id_product_attribute']) {
              Db::getInstance()->execute("UPDATE `ps_product_attribute` SET `default_on` = NULL WHERE id_product = ".$p['id_product']);
              Db::getInstance()->execute("UPDATE `ps_product_attribute_shop` SET `default_on` = NULL WHERE id_product = ".$p['id_product']);
              Db::getInstance()->update('product_attribute',['default_on' => 1],"id_product = ".$p['id_product']." AND id_product_attribute = ".$p['id_product_attribute']);
              Db::getInstance()->update('product_attribute_shop',['default_on' => 1],"id_product = ".$p['id_product']." AND id_product_attribute = ".$p['id_product_attribute']);
              $product->cache_default_attribute = $p['id_product_attribute'];
            }


          }
        }
      }
    }

  }

  public function syncPrivateSales(){
    $pr_cat = new Category(Configuration::get("GECPS_PS_CAT"));
    $pr_cat->cleanAssoProducts();
    $last = $this->getFromAspPsapi('count_ventes_privees');
    $count = (int)$last[0]['count'];
    //$max_id = 2;
    $int = 1000;
    $pr_cats_r = Db::getInstance()->executeS("SELECT * FROM ps_asp_private_sales");
    $pr_cats_exist = array();
    foreach ($pr_cats_r as $key => $l) {
      $pr_cats_exist[$l['id_private_sale']] = $l;
    }
    for ($i=0; $i < ($count); $i+=$int) {
      $r = $this->getFromAspPsapi("ventes_privees&from=$i&int=$int");
      if ( count($r)>0) {
        foreach ($r as $key => $l) {
          if ($key>0) {
            //continue;
          }
          if (isset($pr_cats_exist[$l['id_vente']])) {
            Db::getInstance()->update('asp_private_sales', [
                'name' => str_replace("'","\'",$l['nom']),
                'id_products' => $l['liste'],
                'start' =>   self::parseAspTimeString($l['debut']),
                'end' => self::parseAspTimeString($l['fin']),
                'online' => $l['online'],
                'date_add' => self::parseAspTimeString($l['created'])
              ],
              'id_private_sale = '.$l['id_vente']
            );
          }
          else {

            Db::getInstance()->insert('asp_private_sales', [
              'id_private_sale' => $l['id_vente'],
              'name' => str_replace("'","\'",$l['nom']),
              'id_products' => $l['liste'],
              'start' =>   self::parseAspTimeString($l['debut']),
              'end' => self::parseAspTimeString($l['fin']),
              'online' => $l['online'],
              'date_add' => self::parseAspTimeString($l['created'])
            ]);
          }
        }
      }
    }
    $this->assoCatetoryWithPrivateSale();
  }

  public function assoCatetoryWithPrivateSale(){
    $pr_cats_r = Db::getInstance()->executeS("SELECT * FROM ps_asp_private_sales ORDER BY id_private_sale DESC");
    $id_fr = Language::getIdByIso('fr');
    $id_private_sale_parent = Configuration::get("GECPS_PS_CAT");
    $shops = Shop::getShops(true, null, true);
    foreach ($pr_cats_r as $key => $l) {
      if ($key != 0) {
        //continue;
      }
      if (!$this->checkIfPrivateSaleEnded($l)) {
        if ($l['id_category']) {
          $cat = new Category($l['id_category']);
        }
        else {
          $cat = new Category();

        }
        $cat->name = array($id_fr=>$l['name']);
        $rewrite = Tools::str2url(str_replace(array("<", ">", ";", "=", "#", "{", "}"), '', mb_substr(trim($l['name']), 0, 128, "utf-8")));
        $cat->id_parent = $id_private_sale_parent;
        $cat->link_rewrite = array($id_fr=>$rewrite);
        $cat->save();
        $cat->associateTo($shops);
        Db::getInstance()->update('asp_private_sales',['id_category'=>$cat->id],'id_private_sale = '.$l['id_private_sale']);

        $cat->cleanAssoProducts();
        //$cat->updateGroup([3]);
        if ($l['id_products']) {
          $refs = explode(',',$l['id_products']);
          foreach ($refs as $key => $ref) {
            $p = self::getProductIdFromAspRef($ref);
            if ($p['id_product']) {
              $product = new Product($p['id_product']);
              $product->addToCategories([$cat->id_category,Configuration::get("GECPS_PS_CAT")]);
            }
          }
        }
      }
      else {
        if ($l['id_category']) {
          $cat = new Category($l['id_category']);
          $cat->active = 0;
          $cat->save();
        }
      }
    }
  }

  public function checkIfFlashSaleEnded($l){
    $end = DateTime::createFromFormat('d/m/Y H:i:s', $l['datefin']." 00:00:00");
    $fs_end = strtotime($end->format("Y-m-d H:i:s"));
    $now = strtotime(date("Y-m-d H:i:s"));

    return $fs_end<$now;
  }

  public function checkIfPrivateSaleEnded($l){
    $pr_end = strtotime($l['end']);
    $now = strtotime(date("Y-m-d H:i:s"));

    return $pr_end<$now;
  }



  public static function parseAspTimeString($str,$return_obj = false){
    $date = DateTime::createFromFormat('d/m/Y H:i:s', $str);
    if ($return_obj) {
      return $date;
    }
    else {
      return $date->format('Y-m-d H:i:s');
    }

  }

  public static function getCountryIdFromAspCode($code3){

  }

  public static function getProductIdFromAspRef($ref){
    $id = Product::getIdByReference($ref);
    $p = array('id_product'=>0,'id_product_attribute'=>0);
    if ($id) {
      $p['id_product'] = $id;
    }
    else {
      $sql = "SELECT * FROM ps_product_attribute where reference = ".$ref;
      $comb_r = Db::getInstance()->executeS($sql);
      if ($comb_r) {
        $p['id_product'] = $comb_r[0]['id_product'];
        $p['id_product_attribute'] = $comb_r[0]['id_product_attribute'];

      }
    }

    return $p;
  }

  public function syncShippingCodesfromAsp(){
    $sql = "TRUNCATE `ps_asp_shipping_codes`;";
    Db::getInstance()->execute($sql);
    $last = $this->getFromAspPsapi('count_encombrement');
    $count = (int)$last[0]['count'];
    //$max_id = 2;
    $int = 1000;
    for ($i=0; $i < ($count); $i+=$int) {
      $r = $this->getFromAspPsapi("encombrement&from=$i&int=$int");

      if ( count($r)>0) {

        $s = 'INSERT INTO `ps_asp_shipping_codes` VALUE ';
        $n = 0;
        foreach ($r as $key => $l) {
          $price = (float)str_replace(',','.',$l['prix']);
          $price_wt = $price*1.196;
          $s .= '("'.$l['id_encombrement'].'","'.$l['seuil_mini'].'","'.$l['seuil_maxi'].'","'.$price.'","'.$price_wt.'","'.$l['poids'].'"),';
          $n++;
        }
        $sql = trim($s,',');
        if ($n) {
          $r = Db::getInstance()->execute($sql);
        }

        if (!$r) {
          echo $sql."<br/>";
        }
      }
    }

  }

  public function getShippingCostByShippingCode($codes_r,$code){
    foreach ($codes_r as $key => $code_r) {
      if ($code_r['min']<=$code && $code_r['max']>=$code) {
        return $code_r['price'];
      }
    }
    return 0;
  }

  public function calcProductShippingCost(){
    $codes_r = Db::getInstance()->executeS('SELECT * FROM `ps_asp_shipping_codes`');
    $r = Db::getInstance()->executeS('SELECT * FROM `ps_product`');
    foreach ($r as $key => $l) {
      $price_wt = $this->getShippingCostByShippingCode($codes_r,$l['shipping_code']);
      Db::getInstance()->update('product2',['additional_shipping_cost'=>$price_wt],'id_product = '.$l['id_product']);
    }

  }

  public function removeProductDeliveryInformation(){
    Db::getInstance()->update(
      'product',
      [
        "additional_delivery_times" => '1'
      ]
    );
    Db::getInstance()->update(
      'product_lang',
      [
        "delivery_in_stock" => '',
        "delivery_out_stock" => ''
      ]
    );
  }

  public function getAllReferences($with_comb = false){
    $r = Db::getInstance()->executeS('SELECT reference FROM ps_product');
    $refs = array();
    foreach ($r as $key => $l) {
      $refs[] = $l['reference'];
    }
    return $refs;
  }

  public function syncPorductDeliveryInformation(){
    $this->removeProductDeliveryInformation();
    $last = $this->getFromAspPsapi('count_article_livraison');
    $count = (int)$last[0]['count'];
    $int = 1000;
    $ps_refs = $this->getAllReferences();
    $data = array();
    for ($i=0; $i < ($count); $i+=$int) {
      $r = $this->getFromAspPsapi("article_livraison&from=$i&int=$int");

      if ( count($r)>0) {
        foreach ($r as $key => $l) {
          if (!in_array($l['id_article'],$ps_refs)) {
            continue;
          }
          if (!isset($data[$l['livraison']])) {
              $data[$l['livraison']] = '';
          }

          if ($l['livraison']) {
            $data[$l['livraison']] .= '"'.$l['id_article'].'",';
          }
        }

      }
    }
    foreach ($data as $key => $l) {
      $sql = "UPDATE ps_product_lang SET `delivery_in_stock` = '".$key."',`delivery_out_stock` = '".$key."'
              WHERE ps_product_lang.id_product IN (
                  SELECT id_product FROM ps_product WHERE reference IN (".trim($l,',').")
              )";
      Db::getInstance()->execute($sql);
    }

  }

  public function disableProductNoStockAllShops(){
    $shops = Shop::getShops(true, null, true);
    foreach ($shops as $key => $shop) {
        $this->disableProductNoStock($shop);
    }

  }

  public function disableProductNoStock($shop){

    $r = Db::getInstance()->executeS('SELECT * FROM ps_product ');
    foreach ($r as $key => $l) {
      if ($l['id_product'] != '2960') {
        //continue;
      }
     $stock_available = StockAvailable::getQuantityAvailableByProduct($l['id_product'],null,$shop);
     if ($stock_available <=0 ) {
       Db::getInstance()->update('product',['active'=>0],"id_product = ".$l['id_product']);
       Db::getInstance()->update('product_shop',['active'=>0],"id_product = ".$l['id_product']." AND id_shop = ".$shop);

     }

    }
  }

  public function getProductsXmlFromFile(){
    $file_string = file_get_contents(_PS_DOWNLOAD_DIR_GECPS_."product_files/export_produits.xml");
    //$file_string = file_get_contents(_PS_DOWNLOAD_DIR_GECPS_."product_files/done/20210511175336-export_produits.xml");
    $invalid_characters = '/[^\x9\xa\x20-\xD7FF\xE000-\xFFFD]/';
    $file_string = preg_replace($invalid_characters, '', $file_string);
    $xml_products = simplexml_load_string($file_string);
    return $xml_products;
  }

  public function getStocksXmlFromFile($c,$string_only=false){
    $file_string = file_get_contents(_PS_DOWNLOAD_DIR_GECPS_."stock_files/export_stock_$c.xml");
    //$file_string = file_get_contents(_PS_DOWNLOAD_DIR_GECPS_."product_files/done/20210511175336-export_produits.xml");
    $invalid_characters = '/[^\x9\xa\x20-\xD7FF\xE000-\xFFFD]/';
    $file_string = preg_replace($invalid_characters, '', $file_string);
    if ($string_only) {
      return $file_string;
    }
    $xml_products = simplexml_load_string($file_string);
    return $xml_products;
  }

  public function disableOfflineCombinationAllShop(){
    $c_shops=$this->params['c_shops'];
    foreach ($c_shops as $c => $id_shop) {
      // if ($c != 'fr') {
      //   //continue;
      // }
      $xml_string = $this->getStocksXmlFromFile($c,true);
      $r = Db::getInstance()->executeS("SELECT * from ps_product_attribute");
      foreach($r as $l){
        // if ($l['reference'] != '69044') {
        //   //continue;
        // }
        $f = strpos($xml_string,"<Reference><![CDATA[".$l['reference']."]]></Reference>");
        if (!$f) {
          //var_dump($l['reference']);
          StockAvailable::setQuantity($l['id_product'],$l['id_product_attribute'],0,$id_shop);
        }
      }
    }
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


  public function unpdateProductsStatuFromXml(){
    $xml_products = $this->getProductsXmlFromFile();
    if (isset($xml_products->Products) && count($xml_products->Products->children()) > 0)
    {
      // $fr_c = 0;
      // $es_c = 0;
      foreach ($xml_products->Products->Product as $key=> $product) {
        $product_names = $this->getProductNames($product);
        if (in_array(strtolower($product_names[1]),['sav',"colisage"])) {
          continue;
        }
        $c = $this->updateProductStatu($product);
        // if ($c['fr']) {
        //   $fr_c++;
        // }
        // if ($c['es']) {
        //   $es_c++;
        // }
      }
      // var_dump($fr_c,$es_c);
    }
  }

  public function updateProductStatu($product){
    $c_shops=$this->params['c_shops'];
    $ref = $product->Reference->__toString();
    $id_product = Product::getIdByReference($ref);
    $r = array();
    foreach ($product->Active->Children() as $key => $c) {
      if ($ref != '931') {
        //continue;
      }

      if (isset($c_shops[$key])) {
        $r[$key] = (int)$c;
        Db::getInstance()->update('product_shop',['active'=>$c],'id_product = '.$id_product." AND id_shop = ".$c_shops[$key]);
      }
    }
    return $r;
  }

  public function syncProductReviewFromAsp(){
    Db::getInstance()->execute("TRUNCATE `ps_asp_product_reviews`;");
    $last = $this->getFromAspPsapi('count_article_avis');
    $count = (int)$last[0]['count'];
    $int = 200;
    for ($i=0; $i < ($count); $i+=$int) {
      //var_dump("article_avis&from=$i&int=$int"); continue;
      $r = $this->getFromAspPsapi("article_avis&from=$i&int=$int");

      if ( count($r)>0) {
        $s = 'INSERT INTO `ps_asp_product_reviews` VALUE ';
        $n = 0;
        foreach ($r as $key => $l) {
          $ref = $l['fk_article'];
          if ($l['fk_generique'] != '') {
            $ref = $l['fk_generique'];
          }
          $p = self::getProductIdFromAspRef($ref);
          $id_product = $p['id_product'];
          $date  = DateTime::createFromFormat('d/m/Y', $l['date_avis']);
          $s .= '("'.$l['id_avis'].'","'.$id_product.'","'.$l['fk_article'].'","'.($l['note']/2).'","'.$date->format('Y-m-d').'","'.$l['commentaires'].'","'.$l['prenom'].'","'.$l['nom'].'","'.$l['email'].'"),';
          $n++;
        }
        $sql = trim($s,',');
        if ($n) {
          $r = Db::getInstance()->execute($sql);
          //echo $r;
          //var_dump($sql);
        }

        if (!$r) {
          echo $sql."<br/>";
        }
      }
    }

  }

  public function syncCategoryTexts(){
    $last = $this->getFromAspPsapi('count_categorie_textes');
    $count = (int)$last[0]['count'];
    $int = 1000;
    for ($i=0; $i < ($count); $i+=$int) {
      //var_dump("article_avis&from=$i&int=$int"); continue;
      $r = $this->getFromAspPsapi("categorie_textes&from=$i&int=$int");

      if ( count($r)>0) {

        $n = 0;
        foreach ($r as $key => $l) {
          // if ($l['fk_categorie'] != 45) {
          //   //continue;
          // }
          $n++;
          $cat = Db::getInstance()->executeS('SELECT id_category from ps_category_lang WHERE name like "'.$l['nom'].'" AND id_lang = 1 AND id_shop =1');
          if ($cat) {
            Db::getInstance()->execute(
              'UPDATE `ps_category_lang`
              SET `description` ="'.$l['haut'].'" , `description2` ="'.$l['bas'].'"
              WHERE id_category ='.$cat[0]['id_category']
              .' id_lang in (1)'

            );
          }
        }
        if ($n) {
          //echo $r;
          //var_dump($sql);
        }

      }
    }
  }

  public function syncCategoryBoxes(){
    return false;
    $last = $this->getFromAspPsapi('count_box');
    $count = (int)$last[0]['count'];
    $int = 1000;

    for ($i=0; $i < ($count); $i+=$int) {

      $r = $this->getFromAspPsapi("box&from=$i&int=$int");
      $categories_boxes = array();
      if ( count($r)>0) {

        $n = 0;
        foreach ($r as $key => $l) {
          if ($l['fk_categorie'] != 1550) {
            //continue;
          }
          $n++;
          $cat = Db::getInstance()->executeS('SELECT id_category from ps_category_lang WHERE name like "'.$l['nom'].'" AND id_lang = 1 AND id_shop =1');
          if ($cat) {
            $categories_boxes[$cat[0]['id_category']][] = $l;

          }
        }

      }
    }
    $this->makeCategoriesBoxes($categories_boxes);
  }

  public function makeCategoriesBoxes($categories_boxes){
    $r = Db::getInstance()->executeS('SELECT * FROM `ps_iqit_elementor_content_lang` WHERE id_elementor = 2 AND id_lang =1 AND id_shop=1 ');
    if ($r) {
      $tpl_json = $r[0]['data'];
    }
    else {
      return;
    }
    //echo $tpl_json;
    foreach ($categories_boxes as $id_cat => $categorie_boxes) {
      $this->saveCategoryBoxes($id_cat,$tpl_json,$categorie_boxes);
    }
  }

  public function saveCategoryBoxes($id_cat,$tpl_json,$boxes_datas){

    $shops = Shop::getShops(true, null, true);
    $tpl = json_decode($tpl_json,true);

    foreach ($shops as $key => $shop) {
      Shop::setContext(Shop::CONTEXT_SHOP, (int)$shop);
      $id_langs = Language::getLanguages(true,$shop,true);
      $id_lang = $id_langs[0];
      $lang = new Language($id_lang);
      $id_elementor = IqitElementorCategory::getIdByCategory($id_cat);
      if ($id_elementor) {
        $elementor = new IqitElementorCategory($id_elementor);

      }
      else {
        $elementor = new IqitElementorCategory();
        $elementor->id_category = $id_cat;
        $elementor->just_elementor = 0;

      }

      $category = new Category($id_cat);
      $cat_boxes_elm = json_decode($tpl_json,true);

      $box_tpl_title = $tpl[0]['elements'][0]['elements'][0];
      $box_tpl_text = $tpl[0]['elements'][0]['elements'][1];
      $box_tpl_img = $tpl[0]['elements'][0]['elements'][2];
      $box_tpl_col = $tpl[0]['elements'][0];
      $box_tpl_section = $tpl[0];

      $cat_boxes_elm[0]['elements'] = array();
      $box_tpl_col['elements'] = array();
      $box_tpl_section['elements'] =array();

      $n=-1;
      foreach ($boxes_datas as $key => $box_datas) {
        $n++;
        if ($box_datas['id_box'] != 111) {
          //continue;
        }
        if ($n>5) {
          //continue;
        }

        // $tpl[0]->elements[0]->elements[0]->elements[0]->elements[0]->settings->title = $category->name[1];
        // var_dump($tpl[0]->elements[0]->elements[0]->elements[0]->elements[0]->settings->title);
        $box_title = $box_tpl_title;
        $box_text = $box_tpl_text;
        $box_img = $box_tpl_img;
        $box_col = $box_tpl_col;
        //var_dump($n%2);
        if ($n%2 == 0) {
          $box_section = $box_tpl_section;
          self::newElmId($box_section);
        }


        self::newElmId($box_title);
        self::newElmId($box_text);
        self::newElmId($box_img);
        self::newElmId($box_col);


        $subcat = Db::getInstance()->executeS('SELECT * from ps_category_lang WHERE name like "'.$box_datas['titre'].'"');
        if (!$subcat) {
            $tmp = explode(",",$box_datas['lien']);
            if (isset($tmp[1])) {
              $tmp2 = $tmp[1];
            }
            else {
              $tmp2 = $tmp[0];
            }

            $filteredNumbers = preg_split("/\D+/", $tmp2);
            $id_category_asp =$filteredNumbers[1];
            $tmp = explode(">",$this->getCategoryNameFromAsp($filteredNumbers[1]));
            $category_name_asp = trim($tmp[0]);
            if (!$category_name_asp) {

            }  //var_dump($id_category_asp,$box_datas['lien'],$category_name_asp);

            $subcat = Db::getInstance()->executeS('SELECT * from ps_category_lang WHERE name like "'.$category_name_asp.'"');

        }
        else {
        //  $id_subcat= $subcat[0]['id_category'];
        }

        if ($subcat) {
          $subcat_obj = new Category($subcat[0]['id_category']);
          $box_link = $this->context->link->getCategoryLink($subcat[0]['id_category'],null,$id_lang,null,$shop);
          $box_cat_name =$subcat_obj->name[$id_lang];
        }
        else {
          $box_link = "";
          $box_cat_name = $box_datas['titre'];
        }




        $box_title['settings']['title'] = $box_cat_name;
        $box_title['settings']['link']['url'] = $box_link;
        if ($shop == 1) {
          $box_text['settings']['editor'] = $box_datas['texte'];
        }
        elseif ($shop == 3) {
          $box_text['settings']['editor'] = $box_datas['texte_es'];
        }

        if ($box_link!="") {
          $see_all_link = "<br><a class='see_all_link' href='$box_link'>".$this->module->l('See all products in category','scraper',$lang->getLocale())." $box_cat_name</a>";
          $box_text['settings']['editor'] .= $see_all_link;
        }


        $box_img['settings']['image']['url'] = 'https://www.habitatetjardin.com/files/box/'.str_replace('_mini','',$box_datas['image']);
        $box_img['settings']['link']['url'] =$box_link;
        $box_img['settings']['link_to']="custom";
        $box_col['elements']=[$box_title,$box_text,$box_img];
        $box_section['elements'][]=$box_col;
        if ($n%2 == 1) {
          $cat_boxes_elm[]=$box_section;
        }

        //$cat_boxes_elm


      }

      $data_lang= array();

      $data_lang[$id_lang] = json_encode($cat_boxes_elm);

      $elementor->data = $data_lang;
      $elementor->save();
    }


  }

  public static function newElmId(&$e){
    $e['id'] = $e['id'].rand(1111111111,9999999999);

  }

  public function setDefaultCombinationAllProducts(){
    $r = Db::getInstance()->executeS("SELECT * FROM ps_product_attribute");
    $products_no_default = array();
    $products_has_default = array();
    foreach($r as $l){
      if ($l['default_on']) {
        $products_has_default[$l['id_product']] = $l['id_product'];
        unset($products_no_default[$l['id_product']]);
      }
      else {
        if (!isset($products_has_default[$l['id_product']])) {
          $products_no_default[$l['id_product']] = $l['id_product'];
        }

      }
    }

    foreach($products_no_default as $product_no_default){
      if ($product_no_default != 1484) {
        //continue;
      }
      $r = Db::getInstance()->executeS("SELECT * FROM ps_product_attribute where id_product=".$product_no_default." ORDER BY price");
      $id_product_attribute = $r[0]['id_product_attribute'];
      $id_product = $r[0]['id_product'];

      Db::getInstance()->update('product_attribute',['default_on'=>1],"id_product_attribute=".$id_product_attribute);
      Db::getInstance()->execute("UPDATE ps_product_attribute_shop SET default_on = NULL WHERE id_product=".$id_product);
      Db::getInstance()->update('product_attribute_shop',['default_on'=>1],"id_product_attribute=".$r[0]['id_product_attribute']);
    }

  }

  public function setDefaultImageAllProducts(){
    $r = Db::getInstance()->executeS("SELECT * FROM ps_product_attribute_shop WHERE id_shop=1 AND default_on =1");
    foreach ($r as $key => $l) {
      if ($l['id_product_attribute']!=235) {
        //continue;
      }
      $product_image_r = Db::getInstance()->executeS("SELECT * FROM ps_image WHERE cover =1 AND id_product = ".$l['id_product']);
      $product_attribute_image_r = Db::getInstance()->executeS("SELECT * FROM ps_product_attribute_image WHERE id_product_attribute = ".$l['id_product_attribute']);

      if (!$product_attribute_image_r) {
        continue;
      }

      if ($product_attribute_image_r[0]['id_image'] == 0) {
        continue;
      }

      if ($product_image_r) {
        $id_product_image_cover = $product_image_r[0]['id_image'];
      }
      else{
        $id_product_image_cover = 0;
      }

      if ($id_product_image_cover != $product_attribute_image_r[0]['id_image']) {

          //var_dump($product_image_r[0]['id_image'],$product_attribute_image_r[0]['id_image']);
        Db::getInstance()->execute("UPDATE `ps_image` SET `cover` = NULL WHERE id_product=".$l['id_product']);
        Db::getInstance()->execute("UPDATE `ps_image_shop` SET `cover` = NULL WHERE id_product=".$l['id_product']);
        Db::getInstance()->update('image',['cover'=>1],"id_image=".$product_attribute_image_r[0]['id_image']);
        Db::getInstance()->update('image_shop',['cover'=>1],"id_image=".$product_attribute_image_r[0]['id_image']);

      }
    }

  }

  public function getCategoryNameFromAsp($id_category_asp){
    $r = $this->getFromAspPsapi("category_name&id=".$id_category_asp,false);
    return $r;
  }

  public function setProductOrderByReference(){
    $cats_r = Db::getInstance()->executeS("SELECT * FROM ps_category");
    foreach ($cats_r as $key => $cat_r) {
      // if ($cat_r['id_category'] != 367) {
      //   continue;
      // }
      $cp_r = Db::getInstance()->executeS("
        SELECT cp.*, p.reference FROM ps_category_product cp, ps_product p
        where cp.id_product = p.id_product
        and id_category =".$cat_r['id_category']
      );

      $cps = array();
      foreach ($cp_r as $key => $l) {
        $cps[$l['reference']] = $l;
      }
      ksort($cps);
      $i =0;
      foreach ($cps as $key => $cp) {
        $i++;
        Db::getInstance()->update(
          "category_product",
          ["position"=>$i],
          "id_category = ".$cp['id_category']." AND id_product =".$cp['id_product']);
      }
    }
  }

  public function syncProductSalesCountFromAsp(){
    $sql = "TRUNCATE `ps_product_sale`;";
    Db::getInstance()->execute($sql);
    $i = 0;
    $int = 1000;
    $continue =1;
    $today = date('Y-m-d');
    $asp_product_sales = array();
    while ($continue) {
      $r = $this->getFromAspPsapi("count_product_sale&from=$i&int=$int");
      if (count($r)==0) {
        $continue = 0;
      }
      else {
        foreach ($r as $key => $l) {
          $l['sum'] = (float)str_replace(',','.',$l['sum']);
          $p = self::getProductIdFromAspRef($l['fk_article']);
          if ($p['id_product']) {
            if (isset($asp_product_sales[$p['id_product']])) {
              $asp_product_sales[$p['id_product']]['count'] += $l['count'];
              $asp_product_sales[$p['id_product']]['sale_nbr'] += $l['sale_nbr'];
              $asp_product_sales[$p['id_product']]['sum'] += $l['sum'];
            }
            else {
              $asp_product_sales[$p['id_product']] = $l;
            }
          }

        }
      }
      $i += $int;
    }

    $s = 'INSERT INTO `ps_product_sale` VALUE ';
    $n = 0;
    foreach ($asp_product_sales as $key => $l) {
      $s .= '("'.$key.'","'.$l['count'].'","'.$l['sale_nbr'].'","'.$l['sum'].'","'.$today.'"),';

      $n++;
    }

    $sql = trim($s,',');
    if ($n) {
      $r = Db::getInstance()->execute($sql);
    }



  }

  public function tranlsateGuarantee(){
    $guarantee_l = [];
    if (($handle = fopen(_PS_TRANSLATIONS_DIR_."gecodis/garantie_es.csv", "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($data[1]=="") {
          $data[1] = $data[0];
        }
        $guarantee_l[md5(trim(strtolower($data[0])))] = ["fr"=>$data[0],"es"=>$data[1]];
      }
    }
    if ($guarantee_l) {
      $r = Db::getInstance()->executeS('SELECT DISTINCT guarantee_fr,guarantee_es FROM `ps_asp_variations` WHERE `guarantee_fr` = `guarantee_es` AND `guarantee_fr`!=""');

      foreach ($r as $key => $l) {
        if (isset($guarantee_l[md5(trim(strtolower($l['guarantee_fr'])))])) {
          //var_dump($l);
        }
      }
    }
  }

  public function syncVariationDataFromAsp(){
    //$sql = "TRUNCATE `ps_asp_variations`;";
    //Db::getInstance()->execute($sql);
    $i = 0;
    $int = 500;
    $continue =1;
    $codes_r = Db::getInstance()->executeS('SELECT * FROM `ps_asp_shipping_codes`');
    $guarantee_l = [];
    if (($handle = fopen(_PS_TRANSLATIONS_DIR_."gecodis/garantie_es.csv", "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($data[1]=="") {
          $data[1] = $data[0];
        }
        $guarantee_l[md5(trim(strtolower($data[0])))] = ["fr"=>trim($data[0]),"es"=>trim($data[1])];
      }
    }
    fclose($handle);
    while ($continue) {
      $r = $this->getFromAspPsapi("article_data&from=$i&int=$int");
      if ($r) {
        if (count($r)==0) {
          $continue = 0;
        }
        // if ($i>=10) {
        //   $continue =0;
        // }
        else {
          $s = 'INSERT INTO `ps_asp_variations` VALUE ';
          $n = 0;
          foreach ($r as $key => $l) {
            if ($l) {
              // if ($l['id_article']!="106728") {
              //   continue;
              // }
              $p = self::getProductIdFromAspRef($l['id_article']);

              $shipping_cost = $this->getShippingCostByShippingCode($codes_r,$l['encombrement']);
              if ($l['garantie']) {
                $fr_md5 = md5(trim(strtolower($l['garantie'])));
                $guarantee_es = isset($guarantee_l[$fr_md5])?$guarantee_l[$fr_md5]['es']:$l['garantie'];
              }
              else {
                $guarantee_es = "";
              }
              $r2 = Db::getInstance()->execute("SELECT reference FROM ps_asp_variations WHERE reference = '".$l['id_article']."'");
              if ($r2) {
                $sql = 'UPDATE `ps_asp_variations` SET
                        `id_product`="'.$p['id_product'].'",
                        `id_product_attribute`="'.$p['id_product_attribute'].'",
                        `shipping_delay`="'.$l['livraison'].'",
                        `encombrement`="'.$shipping_cost.'",
                        `guarantee_fr`="'.trim($l['garantie']).'",
                        `guarantee_es`="'.$guarantee_es.'",
                        `name_fr`="'.$l['nom'].'",
                        `name_es`="'.$l['nom_es'].'",
                        `description_fr`="'.$l['descriptif'].'",
                        `description_es`="'.$l['descriptif_es'].'",
                        `description_short_fr`="'.$l['descSuc'].'",
                        `description_short_es`="'.$l['descSuc_es'].'"
                        WHERE reference = '.$l['id_article'];
                Db::getInstance()->execute($sql);
              }
              else {
                $s .= '("","'.$l['id_article'].'","'.$l['livraison'].'","'.$shipping_cost.'","'.$l['garantie'].'","'.$guarantee_es.'","'.$l['nom'].'","'.$l['nom_es'].'","'.$l['descriptif'].'","'.$l['descriptif_es'].'","'.$l['descSuc'].'","'.$l['descSuc_es'].'"),';
                $n++;
              }

            }

          }
          $sql = trim($s,',');
          if ($n) {
            $r2 = Db::getInstance()->execute($sql);
          }
        }
      }
      else {
        $continue = 0;
      }
      if (!is_array($r)) {
        $continue = 0;
      }
      $i += $int;
    }
  }

  public function syncAttributeFromAsp(){
    $sql = "TRUNCATE `ps_asp_attributes`;";
    Db::getInstance()->execute($sql);
    $i = 0;
    $int = 500;
    $continue =1;
    while ($continue) {
      $r = $this->getFromAspPsapi("categorie_critere&from=$i&int=$int");
      if ($r) {
        if (count($r)==0) {
          $continue = 0;
        }
        // if ($i>=1) {
        //   $continue =0;
        // }
        else {
          $s = 'INSERT INTO `ps_asp_attributes` VALUE ';
          $n = 0;
          foreach ($r as $key => $l) {
            if ($l) {
              $cat = Db::getInstance()->executeS('SELECT id_category from ps_category_lang WHERE name like "%'.$l['nom'].'%" AND id_lang = 1 AND id_shop =1');
              $feature = Db::getInstance()->executeS('SELECT id_feature from ps_feature_lang WHERE name like "'.trim($l['nom_critere']).'" AND id_lang = 1');

              $id_cat = $cat?$cat[0]['id_category']:0;
              $id_feature = $feature?$feature[0]['id_feature']:0;
              $s .= '("'.$l['id_critere'].'","'.trim($l['nom_critere']).'","'.trim($l['nom_critere_es']).'","'.$id_feature.'","'.$id_cat.'","'.$l['type_critere'].'","'.$l['ordre'].'"),';
              $n++;
            }

          }
          $sql = trim($s,',');
          if ($n) {
            $r2 = Db::getInstance()->execute($sql);
          }
        }
      }
      else {
        $continue = 0;
      }
      if (!is_array($r)) {
        $continue = 0;
      }
      $i += $int;
    }
  }

  public function syncAttributeValueFromAsp(){
    $sql = "TRUNCATE `ps_asp_attribute_values`;";
    Db::getInstance()->execute($sql);
    $i = 0;
    $int = 1000;
    $continue =1;
    while ($continue) {
      $r = $this->getFromAspPsapi("article_critere&from=$i&int=$int");
      if ($r) {
        if (count($r)==0) {
          $continue = 0;
        }
        // if ($i>=10) {
        //   $continue =0;
        // }
        else {
          $s = 'INSERT INTO `ps_asp_attribute_values` VALUE ';
          $n = 0;
          foreach ($r as $key => $l) {
            if ($l) {
              $s .= '("'.$l['id_articlecritere'].'","'.$l['fk_article'].'","'.$l['fk_critere'].'","'.$l['valeur'].'","'.$l['valeur_es'].'"),';
              $n++;
            }

          }
          $sql = trim($s,',');
          if ($n) {
            $r2 = Db::getInstance()->execute($sql);
          }
        }
      }
      else {
        $continue = 0;
      }
      if (!is_array($r)) {
        $continue = 0;
      }
      $i += $int;
    }
  }

  public function makeFreeShippingCategory(){
    $shops = Shop::getShops(true, null, true);
    foreach ($shops as $key => $id_shop) {
      // if ($id_shop != 3) {
      //   continue;
      // }
      $shop = new Shop($id_shop);
      $country_name = $this->module->getShopCountryNameByShopName($shop->name);
      $freeshipping_cat = new Category(Configuration::get("GECPS_FREESHIPPING_CAT",null,null,$id_shop));
      $freeshipping_cat->cleanAssoProducts();
      if ($id_shop==1) {
        $sql = "SELECT ps.* FROM ps_product_shop ps
                WHERE ps.id_shop = $id_shop
                AND ps.active = 1
                AND ps.id_product IN (
	                 SELECT av.id_product FROM ps_asp_variations av
                   WHERE av.encombrement = 0)";
        $r = Db::getInstance()->executeS($sql);
      }
      elseif ($id_shop==3) {
        $sql = "SELECT ps.* FROM ps_product_shop ps
                WHERE ps.id_shop = $id_shop
                AND ps.active = 1
                AND ps.id_product NOT IN (
	                 SELECT asp.id_product FROM ps_asp_shipping_prices asp
                   WHERE id_country = 6)";
        $r = Db::getInstance()->executeS($sql);
      }

      foreach ($r as $key => $l) {
        $product = new Product($l['id_product']);
        $product->addToCategories($freeshipping_cat->id);
      }
    }


  }


  public function syncAdditionalShippingFromAsp(){
    $sql = "TRUNCATE `ps_asp_shipping_prices`;";
    Db::getInstance()->execute($sql);
    $i = 0;
    $int = 1000;
    $continue =1;
    while ($continue) {
      $r = $this->getFromAspPsapi("port_pays&from=$i&int=$int");
      if ($r) {
        if (count($r)==0) {
          $continue = 0;
        }
        // if ($i>=10) {
        //   $continue =0;
        // }
        else {
          $s = 'INSERT INTO `ps_asp_shipping_prices` VALUE ';
          $n = 0;
          foreach ($r as $key => $l) {
            if ($l) {
              $p = self::getProductIdFromAspRef($l['fk_article']);

              $id_product = $p['id_product'];
              $id_product_attribute = $p['id_product_attribute'];
              $id_country = $this->params['iso3'][$l['code_pays']];
              $price = str_replace(",",".",$l['portHT']);
              $s .= '("","'.$id_product.'","'.$id_product_attribute.'","'.$l['fk_article'].'","'.$id_country.'","'.$price.'"),';
              $n++;
            }

          }
          $sql = trim($s,',');
          if ($n) {
            $r2 = Db::getInstance()->execute($sql);
          }
        }
      }
      else {
        $continue = 0;
      }
      if (!is_array($r)) {
        $continue = 0;
      }
      $i += $int;
    }
  }

  public static function disableProductsAndCategoriesForES(){
    $cats_exlc_es = Configuration::get("GECPS_CATS_EXCL_ES");
  }

  public function disableEmptyCategoriesES(){
    $sql = "SELECT
        cs.id_category
    FROM
        `ps_category_shop` cs
    WHERE
        cs.id_shop = 3 AND cs.id_category NOT IN(0, 1, 2) AND cs.id_category NOT IN(
        SELECT
            id_category
        FROM
            (
            SELECT
                cp.id_category,
                COUNT(cp.id_product) COUNT
            FROM
                ps_category_product cp,
                ps_product_shop ps
            WHERE
                cp.id_product = ps.id_product AND ps.id_shop = 3 AND ps.active = 1
            GROUP BY
                cp.id_category
            ORDER BY
                `cp`.`id_category` ASC
        ) s1
    )";

    $r = Db::getInstance()->executeS($sql);
    $in = "";
    if ($r) {
      foreach ($r as $key => $l) {
        if ($l['id_category']!=426) {
          continue;
        }
        $in .= ",".$l['id_category'];
      }
      $in = trim($in,",");var_dump($in);
      $sql = "DELETE FROM ps_category_shop where id_shop=3 and id_category in ($in)";
      Db::getInstance()->execute($sql);
    }


  }

  public function enableCategoriesES(){

  }





  public function autoSyncProductExtra(){
    echo date('H:i:s').'<br>';
    $this->syncOptionRecommFromAsp();
    echo date('H:i:s').'<br>';
    $this->syncProductExternalDocumentFromAsp();
    echo date('H:i:s').'<br>';
    $this->synDocFromAsp();
    echo date('H:i:s').'<br>';
    $this->synDocProductFromAsp();
    echo date('H:i:s').'<br>';
    $this->synDocTextFromAsp();
    echo date('H:i:s').'<br>';
    $this->synPackFromAsp();
    echo date('H:i:s').'<br>';
    $this->synPackProductFromAsp();
    echo date('H:i:s').'<br>';
    $this->syncPrivateSales();
    echo date('H:i:s').'<br>';
    $this->syncFlashSales();
    echo date('H:i:s').'<br>';
    $this->syncPorductDeliveryInformation();
    echo date('H:i:s').'<br>';
    $this->unpdateProductsStatuFromXml();
    echo date('H:i:s').'<br>';
    $this->disableOfflineCombinationAllShop();
    echo date('H:i:s').'<br>';
    $this->setDefaultCombinationAllProducts();
    echo date('H:i:s').'<br>';
    $this->setDefaultImageAllProducts();
    echo date('H:i:s').'<br>';
    $this->syncAttributeFromAsp();
    echo date('H:i:s').'<br>';
    $this->syncAttributeValueFromAsp();
    echo date('H:i:s').'<br>';
    $this->syncVariationDataFromAsp();
    echo date('H:i:s').'<br>';
    $this->disableProductNoStockAllShops();
    echo date('H:i:s').'<br>';
  }

}
