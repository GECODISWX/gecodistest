<?php
class gecpsorderModuleFrontController extends ModuleFrontController
{
  public $params;

  public function initContent()
  {
    //parent::initContent();
    $this->initParams();
	  $this->checkAction();
    $this->setTemplate('module:gecps/views/templates/front/order.tpl');


  }

  public function initParams(){
    $this->params =array(
      'id_fr' => Language::getIdByIso('fr'),
      'type_payment' => array(
        'Chèque' => 'cheque',
        'Transfert bancaire' => 'virement',
        'Pagos por transferencia bancaria' => 'virement'
      )
    );
  }

  public function checkAction(){
    if (isset($_GET['action'])) {
      if (method_exists($this,$_GET['action'])) {
        $this->{$_GET['action']}();
      }
    }
  }

  public function getOrdersDetails(){

    $date_from = date('Y-m-d H:i:s');
    $date_to = date('Y-m-d H:i:s',strtotime('+1 day'));
    if (isset($_GET['from']) && $_GET['from']) {
      $date_from = date('Y-m-d H:i:s',strtotime($_GET['from']));
    }
    if (isset($_GET['to']) && $_GET['to']) {
      $date_to = date('Y-m-d H:i:s',strtotime($_GET['to']));
    }

    $id_orders = Order::getOrdersIdByDateAllShops($date_from,$date_to);
    $orders = array();
    // $order_states = OrderState::getOrderStates($this->params['id_fr']);
    foreach ($id_orders as $key => $id_order) {
      $order = new Order($id_order);
      $l =array();
      $order_data_add = date('Y-m-d\TH:i:s\Z',strtotime($order->date_add));
      $l['acceptance_decision_date'] = $order_data_add;
      $l['shipping_price'] = $order->total_shipping_tax_incl;
      $l['can_cancel'] = false;
      $l['can_shop_ship'] = false;
      $l['commercial_id'] = '123456789';//?
      $l['created_date'] = $order_data_add;
      $l['currency_iso_code'] = 'EUR';
      $customer = new Customer($order->id_customer);
      $gender = new Gender($customer->id_gender);
      $address_invoice = new Address($order->id_address_invoice);
      $address_delivery = new Address($order->id_address_delivery);
      $country_iso_code = Country::getIsoById($address_delivery->id_country);
      $payment_state_code = $this->getOrderPaymentState($order);
      // if (isset($_GET['country_iso_code'])) {
      //   if ($country_iso_code != $_GET['country_iso_code']) {
      //     continue;
      //   }
      // }
      if (isset($_GET['country_iso_code'])) {
        $tmp = explode(',',$_GET['country_iso_code']);
        if (!in_array($country_iso_code,$tmp)) {
          continue;
        }
      }
      if (isset($_GET['payment_states'])) {
        $tmp = explode(',',$_GET['payment_states']);
        if (!in_array($payment_state_code,$tmp)) {
          continue;
        }
      }
      if (isset($_GET['id_order_prestashop'])) {
        $tmp = explode(',',$_GET['id_order_prestashop']);
        if (!in_array($order->id,$tmp)) {
          continue;
        }
      }

      $address_invoice_info = array(
        'city' => $address_invoice->city,
        'civility' => $gender->name[$this->params['id_fr']],
        'company' => $address_invoice->company,
        'country' => Country::getIsoById($address_invoice->id_country),
        //'country' => Country::getNameById($this->params['id_fr'],$address_invoice->id_country),
        'country_iso_code' => Country::getIsoById($address_invoice->id_country),
        'firstname' => $address_invoice->firstname,
        'lastname' => $address_invoice->lastname,
        'phone' => $address_invoice->phone_mobile,
        'phone_secondary' => $address_invoice->phone,
        "state" => null,
        "street_1" => $address_invoice->address1,
        "street_2" => $address_invoice->address2,
        "zip_code" =>$address_invoice->postcode,
        'other' => $address_invoice->other,
        'nif' => $address_invoice->dni
      );

      $address_delivery_info = array(
        'city' => $address_delivery->city,
        'civility' => $gender->name[$this->params['id_fr']],
        'company' => $address_delivery->company,
        //'country' => Country::getIsoById($address_delivery->id_country),
        'country' => Country::getNameById($this->params['id_fr'],$address_delivery->id_country),
        'country_iso_code' => Country::getIsoById($address_delivery->id_country),
        'firstname' => $address_delivery->firstname,
        'lastname' => $address_delivery->lastname,
        'phone' => $address_delivery->phone_mobile,
        'phone_secondary' => $address_delivery->phone,
        "state" => null,
        "street_1" => $address_delivery->address1,
        "street_2" => $address_delivery->address2,
        "zip_code" =>$address_delivery->postcode,
        'other' => $address_delivery->other,
        'nif' => $address_invoice->dni
      );

      $l['customer']=array(
        'billing_address'=>$address_invoice_info,
        'civility' => $gender->name[$this->params['id_fr']],
        // 'customer_id' => $order->id_customer,
         'customer_id' => $customer->email,
        'asp_id_client' => $customer->asp_id_client,
        'firstname' => $customer->firstname,
        'lastname' => $customer->lastname,
        "locale" => null,
        'shipping_address' => $address_delivery_info,
      );

      $l['customer_debited_date'] = "";//?
      $l['customer_notification_email'] = "";//??
      $l['delivery_date'] = null;
      $l['fulfillment'] = array(
        'center' => array(
          'code' => 'DEFAULT'
        )
      );
      $order_message = $order->getFirstMessage();
      $l['has_customer_message'] = ($order_message?true:false);
      $l['has_incident'] = false;
      $l['has_invoice'] = false;
      $l['last_updated_date'] = $order_data_add;
      $l['leadtime_to_ship'] = null;//?
      $l['order_additional_fields'] = array(
        'customer_message' => $order_message,
        'type_payment' => 'CB',//??
      );
      $l['order_id'] = $this->module->makePrettyOrderId($order);
      $l['order_lines'] = array();
      $order_lines_info = array();
      $currentOrderState = $order->getCurrentOrderState();
      $order_shipping = $order->getShipping();
      $l['order_state'] =$currentOrderState->name[$this->params['id_fr']];
      $l['payment_state'] =$payment_state_code;
      $l['order_tax_mode'] = "TAX_INCLUDED";//??
      $l['payment_workflow'] = "";
      $l['price'] = $order->total_products_wt;
      $l['promotions'] = array(
        'applied_promotions'=>'',
        'total_deduced_amount' => 0
      );
      $l['shipping_carrier_code'] = 'prestashop';
      $l['shipping_company'] = 'prestashop';
      $l['shipping_deadline'] = date('Y-m-d\TH:i:s\Z',strtotime($order->date_add."+30day"));
      $l['shipping_type_code'] = 'prestashop';
      $l['shipping_type_label'] = 'prestashop';

      $l['shipping_zone_code'] = 'prestashop';
      $l['shipping_zone_label'] = 'prestashop';
      $l['total_commission'] = 0;
      $l['total_price'] = $order->total_paid;
      if (isset($this->params['type_payment'][$order->payment])) {
        $l['payment_type'] = $this->params['type_payment'][$order->payment];
      }
      else{
        $l['payment_type'] = 'hipay';
      }


      $order_lines = $order->getOrderDetailList();
      $total_product_shipping = 0;
      $total_product_quantity = 0;
      foreach ($order_lines as $key => $line){
        $total_product_shipping += ($line['total_shipping_price_tax_incl']*$line['product_quantity']);
        $total_product_quantity += $line['product_quantity'];
      }
      $total_other_shipping = $order->total_shipping_tax_incl - $total_product_shipping;
      $other_shipping_unit = $total_other_shipping/$total_product_quantity;
      foreach ($order_lines as $key => $line) {
        $product = new Product($line['product_id']);
        $line_info = array(
          'can_refund' => false,
          'cancelations' => array(),
          'category_code' => '',
          'category_label' => '',
          "commission_fee" => 0,
          "commission_rate_vat" => 0,
          "commission_taxes" => array(),
          "created_date" => null,
          "debited_date" => null,
          "description" => null,
          "last_updated_date" => null,
          "offer_id" => $line['product_id'],
          "offer_sku" => $product->reference,
          "offer_state_code" => null,
          "order_line_additional_fields" => array(),
          'order_line_id' => $line['id_order_detail'],
          'order_line_index' => $key+1,
          "order_line_state" => $currentOrderState->name[$this->params['id_fr']],
          "order_line_state_reason_code" => null,
          "order_line_state_reason_label" => null,
          "price" => $line['total_price_tax_incl'],
          "price_additional_info" => null,
          "price_unit" => $line['unit_price_tax_incl'],
          "product_medias" => array(),
          "product_sku" => $product->reference,
          "product_title" => $product->name[$this->params['id_fr']],
          "promotions" => array(),//??
          "quantity" => $line['product_quantity'],
          "received_date" => null,
          "refunds" => array(),
          "shipped_date" => null,
          "shipping_price" => $line['total_shipping_price_tax_incl'],
          "shipping_price_additional_unit" => $other_shipping_unit,
          "shipping_price_unit" => null,//??
          "shipping_taxes" => array(),//??
          "taxes" => array(),//??
          "total_commission" => null,
          "total_price" => $line['total_price_tax_incl']//??shipping included?
        );
        $l['order_lines'][]= $line_info;
      }





      $orders[] = $l;
    }
    return $orders;
  }

  public function getOrderPaymentState($order){
    $s = array(
      1 => 0,//En attente du paiement par chèque <-> en attend
      10 => 0,
      12 => 0,
      2 =>3,
      24=>3,
      26=>3,
      23=>8,
      28=>7,
      31=>6,
      6=>4//annuler
    );
    if (isset($s[$order->current_state])) {
      return $s[$order->current_state];
    }
    else {
      return 0;
    }
  }

  public function makeOrdersJson($orders){
    return json_encode(array('orders'=>$orders));
  }



  public function exportOrders(){
    $orders = $this->getOrdersDetails();
    $orders_json = $this->makeOrdersJson($orders);
    echo $orders_json;
  }

  public function exportOrdersAsXml(){
    $orders = $this->getOrdersDetails();
    $xml = $this->parseCmdArrayToXml($orders);
    header('Content-Type: application/xml; charset=utf-8');
    echo $xml->asXml();
  }

  public function parseCmdArrayToXml($orders){
    $xml = new SimpleXMLElement('<body/>');
    $xml->addChild('total_count',count($orders));
    $xml->addChild('orders');
    $simple_array_key = array('fulfillment','promotions','customer');
    $child_array_key = array(
      'order_lines' => 'order_line'
    );
    foreach ($orders as $order_key => $order) {
      $xml->orders->addChild('order');
      $order_xml = $xml->orders->order[$order_key];
      foreach ($order as $l_key => $l) {
        if (!is_array($l)) {
          $order_xml -> addChild($l_key,$l);
        }
        else {
          if (in_array($l_key,$simple_array_key)) {
            self::array2xml(array($l_key=>$l),$order_xml);
          }
          elseif ($l_key == 'order_lines'){
            $order_xml -> addChild('order_lines');
            foreach ($order['order_lines'] as $order_line_key => $order_line) {
              self::array2xml(array('order_line'=>$order_line),$order_xml->order_lines);
            }
          }
        }
      }
    }
    return $xml;
  }

  public static function array2xml( $array, $xml) {

    // Loop through array
    foreach( $array as $key => $value ) {
        // Another array? Iterate
        if ( is_array( $value ) ) {
          self::array2xml( $value, $xml->addChild( $key ) );
        } else {
          $xml->addChild( $key, $value );
        }
    }

    // Return XML
    return $xml;
}

}
