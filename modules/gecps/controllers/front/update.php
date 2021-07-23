<?php
class gecpsupdateModuleFrontController extends ModuleFrontController
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
    if (!defined('_PS_GECPS_LOG_DIR_')) {
        define('_PS_GECPS_LOG_DIR_', _PS_ROOT_DIR_.'/var/logs/');
    }
    $this->params = array(

    );
  }

  public function updateOrders(){

    // if ((int)$_GET['id'] == 8) {
    //   $this->logGetPost();
    //   $this->logPut();
    // }
    $this->logGetPost();
    $this->logPut();

    $data = $this->parseGetPutData();


    $this->doUpdateOrder($data);

  }

  public function parseOrderIdFromPrettyId($pretty_id){
    $id = (int)substr($pretty_id,5);
    return $id;
  }

  public function addOrderData($data){
    if (isset($_GET['id'])) {
      $data['ref_order'] = (int)$_GET['id'];
    }
    elseif(isset($data['id'])) {
      $data['ref_order'] = $data['id'] ;
    }
    elseif(isset($data['id_order'])) {
      $data['ref_order'] = $data['id_order'] ;
    }

    //$orders = Order::getByReference($data['ref_order']);
    $id_order = $this->parseOrderIdFromPrettyId($data['ref_order']);
    $data['id_order'] = $id_order;


    if (isset($_GET['date_depart'])) {
      $data['date_depart'] = $_GET['date_depart'];
    }


    return $data;
  }

  public function parseGetPutData(){
    $data =array();
    $data = $this->parsePutData();
    $data = $this->addOrderData($data);

    return $data;
  }

  public function updateOrderCarrier(){
    $order = new Order($data['id_order']);
    if (isset($data['tracking_number'])) {
      $id_order_carriers = $order->getIdOrderCarrier();
      if ($id_order_carriers) {
        if (is_array($id_order_carriers)) {
          $id_order_carrier = $id_order_carriers[count($id_order_carriers)-1];
        }else {
            $id_order_carrier = $id_order_carriers;
        }

        if ($id_order_carriers) {
          $order_carrier = new OrderCarrier($id_order_carrier);
          $order_carrier->tracking_number = $data['carrier_url'];
          $order_carrier->save();
        }
      }

    }
  }

  public function doUpdateOrder($data){

    if (!isset($data['id_order']) || !$data['id_order']) {
      return false;
    }

    $order = new Order($data['id_order']);

    if (isset($data['accepted'])) {
      if ($data['accepted']) {
        $this->changeOrderState($order->id,33);
      }
      else {
          $this->changeOrderState($order->id,34);
      }
    }
    if (isset($data['facture']) && $data['id_order']) {
      $r = Db::getInstance()->executeS('SELECT * FROM ps_asp_order_extras WHERE id_order = '.$data['id_order']);
      if (count($r)) {
        Db::getInstance()->update('asp_order_extras',['invoice'=>$data['facture']],"id_order =".$data['id_order']);
      }
      else {
        Db::getInstance()->insert('asp_order_extras',['id_order' => $data['id_order'] , 'invoice'=>$data['facture']]);
      }

    }
    if (isset($data['id_tracking'])) {
      $this->updateAspOrderTracking($data);
    }
  }

  public static function getProductIdFromAspRef($ref){
    $id = Product::getIdByReference($ref);
    $p = array();
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

  public function updateAspOrderTracking($data){
    $date = DateTime::createFromFormat('d/m/Y', $data['date']);
    $p = self::getProductIdFromAspRef($data['id_article']);
    $tracking = array(
      'id_tracking' => $data['id_tracking'],
      'id_order' => $data['id_order'],
      'reference' => $data['id_article'],
      'id_product' => $p['id_product'],
      'id_product_attribute' => isset($p['id_product_attribute'])?$p['id_product_attribute']:0,
      'quantity' => $data['quantité'],
      'carrier_name' => $data['carrier_name'],
      'date' => $date->format('Y-m-d'),
      'url_tracking' => $data['url_tracking'],
      'tracking_number' => $data['tracking_number']
    );
    $r = Db::getInstance()->executeS("SELECT * FROM ps_asp_order_trackings WHERE id_tracking = ".$tracking['id_tracking']);
    if ($r) {
      Db::getInstance()->update('asp_order_trackings',$tracking,'id_tracking ='.$tracking['id_tracking']);
    }
    else {
      Db::getInstance()->insert('asp_order_trackings',$tracking);
    }
  }

  public function changeOrderState($oid,$current_state)
  {
	  $sql="INSERT INTO `ps_order_history`(`id_order_history`, `id_employee`, `id_order`, `id_order_state`,`date_add`) VALUES ('',0,".$oid.",$current_state,'".date("Y-m-d H:i:s")."')";
	  $order = new Order($oid);
	  $order->current_state = $current_state;
	  $order->update();
    $r = Db::getInstance()->execute($sql);
    if ($r) {

      Hook::exec('actionOrderStatusUpdate', array('newOrderStatus' => $order->getCurrentOrderState(), 'id_order' => (int) $oid), null, false, true, false, $this->context->cart->id_shop);
    }
    return $r;
  }

  public function parsePutData(){
    $str = $this->getPutData();
    $put_data = json_decode($str,true);
    return $put_data;
  }

  public function auth() {
  	$AUTH_USER = 'admin';
  	$AUTH_PASS = 'admin';
  	header('Cache-Control: no-cache, must-revalidate, max-age=0');
  	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
  	$is_not_authenticated = (
  		!$has_supplied_credentials ||
  		$_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
  		$_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
  	);
  	if ($is_not_authenticated) {
  		header('HTTP/1.1 401 Authorization Required');
  		header('WWW-Authenticate: Basic realm="Access denied"');
  		exit;
  	}
  }

  public function getPutData(){
     return file_get_contents("php://input");

  }

  public function logPut(){

    $file = _PS_GECPS_LOG_DIR_.'put.txt';
    $fp = fopen($file, "a");
    $str = $this->getPutData();
    file_put_contents($file,$str.PHP_EOL,FILE_APPEND);
    fclose($fp);
  }

  public function logGetPost(){
    if (count($_GET)) {
      $file = _PS_GECPS_LOG_DIR_.'get.txt';
  		$_GET['get_time'] = date("Y-m-d H:i:s");
  		$content = serialize($_GET).PHP_EOL.'';
  		file_put_contents($file,$content,FILE_APPEND);
    }
    if (count($_POST)) {
      $file = _PS_GECPS_LOG_DIR_.'post.txt';
  		$_GET['post_time'] = date("Y-m-d H:i:s");
  		$content = serialize($_POST).PHP_EOL.'';
  		file_put_contents($file,$content,FILE_APPEND);
    }
  }
}
