<?php
class gecpscrawlerModuleFrontController extends ModuleFrontController
{
  public $params = array();

  public function initContent()
  {
    //parent::initContent();
    $this->initParams();
	   $this->checkAction();
    $this->setTemplate('module:gecps/views/templates/front/basic.tpl');


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

    );

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

  public function crawlAllPages(){
    $this->curl = curl_init();
    $total = 0;
    // $total += $this-> runAllHomePages();
    $total += $this->runAllCategoryPages();

    echo "total all : $total<br>";

    curl_close ($this->curl);
  }

  public function flushCaches(){
    if (extension_loaded('memcached')) {
      $m = new Memcached();
      $m->flush();
    }
  }

  public function runAllHomePages(){
    $shops = Shop::getShops(true, null, true);
    $t0 = new DateTime(date("Y-m-d H:i:s"));
    $total =0;
    foreach ($shops as $key => $id_shop) {
      $shop = new Shop($id_shop);
      $url = "https://".$shop->domain_ssl;
      $result = $this->callPage($url);
      $t1 = new DateTime(date("Y-m-d H:i:s"));
      $diff = $t1->getTimestamp() - $t0->getTimestamp();
      $total+=$diff;
      echo "(".$diff.") <a target='_blanc' href='$url'>".$url.'</a><br>';

    }
    echo "total : $total<br>";
    return $total;
  }

  public function runAllCategoryPages(){
    $r = Db::getInstance()->executeS("SELECT * FROM ps_category_shop WHERE id_category>2");
    $t0 = new DateTime(date("Y-m-d H:i:s"));
    $total =0;
    foreach ($r as $key => $l) {
      if ($key>30) {
        continue;
      }
      $t1 = new DateTime(date("Y-m-d H:i:s"));
      $id_langs = Language::getLanguages(true,$l['id_shop'],true);
      $id_lang = $id_langs[0];

      $cat_url = $this->context->link->getCategoryLink($l['id_category'],null,$id_lang,null,$l['id_shop']);
      $result = $this->callPage($cat_url);

      $diff = $t1->getTimestamp() - $t0->getTimestamp();
      echo "(".$diff.") <a target='_blanc' href='$cat_url'>".$cat_url.'</a><br>';
      $total+=$diff;
      $t0=$t1;
    }
    echo "total : $total<br>";
    return $total;

  }

  public function checkHipayConfig(){
    $shops = Shop::getShops(true, null, true);
    foreach ($shops as $key => $id_shop) {
      $conf = json_decode(Configuration::get("HIPAY_CONFIG",null,null,$id_shop),true);
      $test = $conf['account']['sandbox']['api_username_sandbox'];
      if ($test=='') {
        $file =  _PS_ROOT_DIR_.'/var/logs/'.'HIPAY_CONFIG.txt';
        $fp = fopen($file, "a");
        $str = "shop$id_shop:".date("Y-m-d H:i:s").PHP_EOL.'';
        file_put_contents($file,$str.PHP_EOL,FILE_APPEND);
        fclose($fp);
      }
    }
  }


}
