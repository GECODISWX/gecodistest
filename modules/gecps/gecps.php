<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class Gecps extends Module
{
    protected $config_form = false;
    public $vars;

    public function __construct()
    {
        $this->name = 'gecps';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        $this->author = 'xiao';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Gecodis Prestashop module');
        $this->description = $this->l('Gecodis Prestashop module');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->inputs = array(
          "PS_REGISTRATION_SEND",
          "GECPS_FS_CAT",
          "GECPS_PS_CAT",
          "GECPS_PROMO_CAT",
          "GECPS_FREESHIPPING_CAT",
          "GECPS_CATS_SLIDERS",
          "GECPS_CATS_EXCL",
          "GECPS_HOME_TAB_CATS"
    		);
        foreach ($this->inputs as $key => $input) {
          $this->$input = Configuration::get($input);
        }
        $this->initVars();
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('GECPS_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayNav2') &&
            $this->registerHook('displayHeader')&&
            $this->registerHook('displayHome')&&
            $this->registerHook('actionCustomerAccountAdd')&&
            $this->registerHook('displayCustomerAccount')&&
            $this->registerHook('actionValidateOrder')&&
            $this->registerHook('displayProductTabTitle')&&
            $this->registerHook('displayProductTabSection')&&
            $this->registerHook('actionProductFlagsModifier')&&
            $this->registerHook('displayOrderTrackings')&&
            $this->registerHook('displayCombinationFirstImage')&&
            $this->registerHook('displayProductVariants')&&
            $this->registerHook('displayProductAvailability')&&
            $this->registerHook('displayProductPrettyPrice')&&
            $this->registerHook('displayProductFeatures')&&
            $this->registerHook('displaySubCategories')&&
            $this->registerHook('displayPrettyOrderId')&&
            $this->registerHook('displayOrderConfirmationAddresses');

    }

    public function uninstall()
    {
        Configuration::deleteByName('GECPS_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitGecpsModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGecpsModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
      $inputs = array();
      foreach ($this->inputs as $key => $input) {
        $array = array(
            'col' => 3,
            'type' => 'textarea',
            'desc' => $this->l($input),
            'name' => $input,
            'label' => $input,
        );
        $inputs[] = $array;
      }
      $fields_form = array(
        'form' => array(
          'legend' => array(
            'title' => $this->l('Settings') ,
            'icon' => 'icon-cogs'
          ) ,
          'input' => $inputs,
          'submit' => array(
            'title' => $this->l('Save') ,
          )
        )
      );
      return $fields_form;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
      $array = array();
      foreach ($this->inputs as $key => $input) {
        $array[$input] = Configuration::get($input);
        $this->$input = $array[$input];
      }
        return $array;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
      $form_values = $this->getConfigFormValues();

      foreach (array_keys($form_values) as $key) {
          Configuration::updateValue($key, Tools::getValue($key));
      }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {

    }

    public function hookDisplayFooter()
    {
        if ($this->context->customer->asp_sav_token) {
            $sav_token = $this->context->customer->asp_sav_token;
        }
        else {
          $sav_token = false;
        }
        $this->smarty->assign(
          array(
            'sav_token' => $sav_token
          )
        );
        return $this->display(__FILE__, 'footer.tpl');
    }

    public function hookDisplayProductTabTitle($params){
      return $this->display(__FILE__, 'product-tab-title.tpl');
    }

    public function hookDisplayProductTabSection($params){

      $asp_extermal_links = self::getAspExternalLinks($params['product']);
      $asp_recommended_options = $this->getRecommendedOptionsByRef($params['product']);
      // foreach ($asp_recommended_options as $key => $p) {
      //   var_dump($p->has_discount);
      // }
      //$asp_packs = $this->getAspPacks($params['product']);
      $asp_instructions = $this->getAspInstructions($params['product']);
      $this->smarty->assign(
        array(
          'asp_extermal_links' => $asp_extermal_links,
          'asp_recommended_options' =>$asp_recommended_options,
          'asp_packs' => false,
          'asp_instructions' => $asp_instructions,
        )
      );
      return $this->display(__FILE__, 'product-tab-section.tpl');
    }

    public function hookDisplayHeader()
    {
      $this->checkLogin();
      $this->context->controller->addJS($this->_path.'/views/js/front.js');
      $this->context->controller->addCSS($this->_path.'/views/css/front.css');
      $this->context->controller->addCSS($this->_path.'/views/css/fa5p.css');
      $this->context->controller->addCSS($this->_path.'/views/css/icomoon/style.css');
      return $this->display(__FILE__, 'header.tpl');
        /* Place your code here. */
    }

    public function hookDisplayHome(){


      return $this->display(__FILE__, 'home-footer.tpl');
    }

    public function hookDisplayNav2(){

      $this->smarty->assign($this->getNav2Variables());
      return $this->display(__FILE__, 'display-nav.tpl');
    }

    private function getNameSimple($name)
    {
        return preg_replace('/\s\(.*\)$/', '', $name);
    }

    public function getIsoByShopName($shop_name){
      $temp = explode('(',trim($shop_name,')'));
      return $temp[1];
    }

    public function getShopCountryNameByShopName($shop_name){
      $shop_country_iso= $this->getIsoByShopName($shop_name);
      return  Country::getNameById($this->context->language->id,Country::getByIso($shop_country_iso));
    }

    public function getCurrentEntityUrlByShop($id_shop,$iso,$default_url = ''){

      $entity_type = $this->context->controller->php_self;
      $id_lang = Language::getIdByIso(strtolower($iso));

      if ($entity_type == 'product') {
        $link = $this->context->link->getProductLink(Tools::getValue('id_product'),null,null,null,$id_lang,$id_shop);
      }
      elseif($entity_type == 'category') {

        $link = $this->context->link->getCategoryLink(new Category(Tools::getValue('id_category'), $id_lang),null,$id_lang,null,$id_shop);
      }
      elseif($entity_type == 'cms'){
        $link = $this->context->link->getCMSLink(new CMS(Tools::getValue('id_cms'), $id_lang),null,null,$id_lang,$id_shop);
      }
      else {
        $link = $default_url;
      }

      return $link;
    }

    public function getNav2Variables()
    {
        $id_shops = Shop::getShops(true, null, true);
        $shops =array();
        foreach ($id_shops as $key => $id_shop) {
          $shops[$id_shop] = Shop::getShop($id_shop);
          $shops[$id_shop]['iso'] = strtoupper($this->getIsoByShopName($shops[$id_shop]['name']));
          $shops[$id_shop]['country_name'] = $this->getShopCountryNameByShopName($shops[$id_shop]['name']);
          $shops[$id_shop]['entity_url'] = $this->getCurrentEntityUrlByShop($id_shop,$shops[$id_shop]['iso'],'https://'.$shops[$id_shop]['domain_ssl']);
        }



        return array(
            'shops' => $shops,
            'current_shop' => array(
                'id_shop' => $this->context->shop->id,
                'name' => $this->getShopCountryNameByShopName($this->context->shop->name),
                'iso' => strtoupper($this->getIsoByShopName($this->context->shop->name)),
                'id_lange' =>$this->context->language->id
            )
        );
    }




    public function hookActionCustomerAccountAdd($params){
      self::creatCustomerInAsp($params['newCustomer']);
    }

    public static function creatCustomerInAsp($c){
      $rand = date('Y-m-d h:i:s').rand();
      $cookie_file = _PS_ROOT_DIR_."/var/curl_cookies/$rand.txt";
      $gender = $c->id_gender == 2 ? 'Mme' : 'M.';
      $jour = 'jj';
      $mois = 'mm';
      $annee = 'aaaa';
      $password = Tools::getValue('password');
      if ($c->birthday) {
        $tmp = explode('-',$c->birthday);
        $jour = $tmp[2];
        $mois = $tmp[1];
        $annee = $tmp[0];
      }
      $data = array(
        'submit'    => 'VALIDER',
        'email'     =>  $c->email,
        'cemail'    =>  $c->email,
        'pass'      =>  $password,
        'cpass'     =>  $password,
        'civilite'  =>  $gender,
        'nom'       =>  $c->lastname,
        'prenom'    =>  $c->firstname,
        'jour'      =>  $jour,
        'mois'      =>  $mois,
        'annee'     =>  $annee,
        'adresse'   =>  'inconnu',
        'cp'        =>  '00000',
        'ville'     =>  'inconnu',
        'portable'  =>  '0600000000',
        'code_pays' =>  'FRA'



      );
      $post_data = http_build_query($data);
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.habitatetjardin.com/client/inscription.html',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $post_data,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => array(
           'Content-Type: application/x-www-form-urlencoded',
        ),
        CURLOPT_COOKIESESSION=> true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE     =>$cookie_file, //set cookie file
        CURLOPT_COOKIEJAR      =>$cookie_file, //set cookie jar
      ));

      $response = curl_exec($curl);
      curl_close($curl);
      $curl_cookie = Customer::copyCurlCookieToCookie($cookie_file);

      $datas = self::getCustomerInfoFromAsp($c->email,$password,$curl_cookie);
      if (isset($datas['customer']['asp_cookie']) && $datas['customer']['asp_cookie']) {
        $c->asp_cookie = $datas['customer']['asp_cookie'];
      }
      if (isset($datas['customer']['asp_id_client']) && $datas['customer']['asp_id_client']) {
        $c->asp_id_client = $datas['customer']['asp_id_client'];
      }
      $sav_token = self::getCustomerSavToken($curl_cookie);
      $c->asp_sav_token = $sav_token;
      $c->update();



    }

    public static function getCustomerSavToken($curl_cookie){
      if (isset($curl_cookie['0'])) {
        $curl = curl_init();
        $cookie = 'Cookie: '.trim($curl_cookie['0']).'='.trim($curl_cookie['1']).'; cptv=2';


        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://www.habitatetjardin.com/client/espace-client.html',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTPHEADER => array(
             'Content-Type: application/x-www-form-urlencoded',
             $cookie
          ),
          CURLOPT_RETURNTRANSFER => true
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $html = new simple_html_dom();
    		$html->load($result);

        $trs = $html->find('.news-in2.fondclient table tr');
        $count = count($trs)-3;
        $tr = $html->find('.news-in2.fondclient table tr',$count);
        return $tr->find('a',1)->href;
      }
      return false;
    }

    public static function getCustomerInfoFromAsp($email,$password,$curl_cookie){
      if (isset($curl_cookie['0'])) {
        $curl = curl_init();
        $cookie = 'Cookie: '.trim($curl_cookie['0']).'='.trim($curl_cookie['1']).'; cptv=2';


        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://www.habitatetjardin.com/client/modification.html',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTPHEADER => array(
             'Content-Type: application/x-www-form-urlencoded',
             $cookie
          ),
          CURLOPT_RETURNTRANSFER => true
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        $html = new simple_html_dom();
    		$html->load($result);

        $form = $html->find('.news-in2.fondclient.cmd form',0);
        $datas = Customer::parseAspCustomerAddressesForm($form);
        if (isset($datas['customer'])) {
          $datas['customer']['password'] = $password;
          $datas['customer']['email'] = $email;
          $datas['customer']['asp_cookie'] = $cookie;
        }
        return $datas;

      }
      return false;
    }

    public function hookDisplayCustomerAccount(){
      $this->context->smarty->assign('sav_url', 'http://sav.habitatetjardin.com');
      return $this->display(__FILE__, 'my-account.tpl');
    }

    public function hookActionValidateOrder($params){

    }

    public static function sendOrderToAsp(){
      $oids = self::getOrdersToSync();
      $orders_data = self::getOrdersDetails($oids);
      self::creatOrdersInAsp($orders_data);

    }

    public static function creatOrdersInAsp($orders_data){
      foreach($orders_data as $key => $data){
        $o = new Order($data['order_id']);
        $c = new Customer($o->id_customer);

        if ($c->asp_cookie) {
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.habitatetjardin.com/client/modification.html',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array(
               'Content-Type: application/x-www-form-urlencoded',
               $c->asp_cookie
            ),
            CURLOPT_RETURNTRANSFER => true
          ));

          $result = curl_exec($curl);
          curl_close($curl);
          $html = new simple_html_dom();
      		$html->load($result);
        }
      }

    }

    public static function getLangsId(){
      $langs =array(
        'id_fr' => Language::getIdByIso('fr')
      );

      return $langs;
    }

    public function makePrettyOrderId($order){
      $iod = "";
      $address_invoice = new Address($order->id_address_invoice);
      $country = new Country($address_invoice->id_country);
      $iod .= "1".str_replace("+","",$country->call_prefix);
      $iod .= date('y',strtotime($order->date_add));
      $tmp = 10000000000+$order->id;
      $iod .= substr($tmp,1);
      return $iod;
    }

    public function makePrettyOrderIdFromReference($ref_order){
      $orders = Order::getByReference($ref_order);
      $pretty_id = $this->makePrettyOrderId($orders[0]);
      return $pretty_id;
    }

    public static function getOrdersDetails($oids){


      $id_orders = $oids;
      $orders = array();
      $id_langs = self::getLangsId();
      $order_states = OrderState::getOrderStates($id_langs['id_fr']);
      foreach ($id_orders as $key => $id_order) {
        $order = new Order($id_order);
        $l =array();
        $order_data_add = date('Y-m-d\TH:i:s\Z',strtotime($order->date_add));
        $l['acceptance_decision_date'] = $order_data_add;
        $l['can_cancel'] = false;
        $l['can_shop_ship'] = false;
        $l['commercial_id'] = '123456789';//?
        $l['created_date'] = $order_data_add;
        $l['currency_iso_code'] = 'EUR';
        $customer = new Customer($order->id_customer);
        $gender = new Gender($customer->id_gender);
        $address_invoice = new Address($order->id_address_invoice);
        $address_delivery = new Address($order->id_address_delivery);
        $address_invoice_info = array(
          'city' => $address_invoice->city,
          'civility' => $gender->name[$id_langs['id_fr']],
          'company' => $address_invoice->company,
          'country' => Country::getIsoById($address_invoice->id_country),
          //'country' => Country::getNameById($id_langs['id_fr'],$address_invoice->id_country),
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
        );
        $address_delivery_info = array(
          'city' => $address_delivery->city,
          'civility' => $gender->name[$id_langs['id_fr']],
          'company' => $address_delivery->company,
          //'country' => Country::getIsoById($address_delivery->id_country),
          'country' => Country::getNameById($id_langs['id_fr'],$address_delivery->id_country),
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
        );

        $l['customer']=array(
          'billing_address'=>$address_invoice_info,
          'civility' => $gender->name[$id_langs['id_fr']],
          'customer_id' => $order->id_customer,
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
        $l['order_id'] = $id_order;
        $l['order_lines'] = array();
        $order_lines_info = array();
        $order_shipping = $order->getShipping();
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
            "order_line_state" => $order_shipping[0]['order_state_name'],
            "order_line_state_reason_code" => null,
            "order_line_state_reason_label" => null,
            "price" => $line['product_price'],
            "price_additional_info" => null,
            "price_unit" => $line['product_price'],
            "product_medias" => array(),
            "product_sku" => $product->reference,
            "product_title" => $product->name[$id_langs['id_fr']],
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

    public static function getOrdersToSync(){
      $sql = "SELECT id_order FROM ps_orders where asp_id_order = '' AND id_order >2";
      $r = Db::getInstance()->executeS($sql);
      $oids = false;
      foreach ($r as $key => $value) {
        $oids[] = $value['id_order'];
      }
      return $oids;
    }

    public static function getAllReferences(){
      $sql = "SELECT ps_product.reference FROM ps_product
              UNION
              SELECT ps_product_attribute.reference FROM ps_product_attribute";
      $r = Db::getInstance()->executeS($sql);
      $refs = array();
      foreach($r as $l){
        $refs[] = $l['reference'];
      }
      return array_unique($refs);
    }

    public static function getAspExternalLinks($p){
      $sql = "SELECT reference FROM ps_product_attribute WHERE id_product = ".$p['id_product'];
      $r = Db::getInstance()->executeS($sql);
      $refs = array($p['reference']);
      $ref_in = "";
      if ($r) {
        foreach($r as $ref){
          $ref_in .= "'".$ref['reference']."',";
        }
      }
      else {
        $ref_in .= "'".$p['reference']."',";
      }
      $ref_in = trim($ref_in,',');
      $sql = "SELECT * FROM ps_asp_external_links WHERE reference in (".$ref_in.")";
      $r = Db::getInstance()->executeS($sql);
      foreach ($r as $key => $l) {
        $r[$key]['link'] = urldecode($l['link']);
      }
      return $r;
    }

    public function isProductActive($id_p){
      $sql = "SELECT active from ps_product_shop where id_product=$id_p AND id_shop=".$this->context->shop->id;
      $r = Db::getInstance()->executeS($sql);
      if ($r) {
        return $r[0]['active'];
      }
      else {
        return 0;
      }
    }

    public function getRecommendedOptionsByRef($product){
      $refs = array($product['reference']);
      $comb_refs = Db::getInstance()->executeS("SELECT reference from ps_product_attribute where id_product = ".$product['id_product']);
      foreach ($comb_refs as $key => $comb_ref) {
        $refs[] = $comb_ref['reference'];
      }
      $ref_in = "'".$product['reference']."',";
      foreach ($refs as $key => $ref) {
        $ref_in .= "'".$ref."',";
      }
      $ref_in = trim($ref_in,',');



      $sql = "SELECT * FROM ps_asp_recommended_options where reference IN ($ref_in)";
      $r = Db::getInstance()->executeS($sql);
      $p = array();
      foreach ($r as $key => $l) {

        $id = Product::getIdByReference($l['option_reference']);
        if ($id && !$this->isProductActive($id)) {
          continue;
        }

        if ($id) {
          $p[] = $id;
        }
        else {
          $sql = "SELECT * FROM ps_product_attribute where reference = ".$l['option_reference'];
          $comb_r = Db::getInstance()->executeS($sql);
          if ($comb_r) {
            $p[] = $comb_r[0]['id_product'];

          }
        }
      }


      $p = array_unique($p);
      $productsForTemplate = $this->getProductsTemplate($p);
      return $productsForTemplate;
    }

    public function getProductsTemplate($p){
      if (!empty($p)) {
        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );
        $productsForTemplate = array();
        if (is_array($p)) {
          foreach ($p as $productId) {
              $productsForTemplate[] = $presenter->present(
                  $presentationSettings,
                  $assembler->assembleProduct(array('id_product' => $productId)),
                  $this->context->language
              );
          }
        }
        return $productsForTemplate;
      }
    }

    public function getAspPacks($product){
      $refs = array($product['reference']);
      $comb_refs = Db::getInstance()->executeS("SELECT reference from ps_product_attribute where id_product = ".$product['id_product']);
      foreach ($comb_refs as $key => $comb_ref) {
        $refs[] = $comb_ref['reference'];
      }
      $ref_in = "";
      $refs = array_unique($refs);
      foreach ($refs as $key => $ref) {
        $ref_in .= "'".$ref."',";
      }
      $ref_in = trim($ref_in,',');
      $sql = "SELECT * FROM ps_asp_pack WHERE reference in ($ref_in) order by ps_asp_pack.ordre";
      $packs_r = Db::getInstance()->executeS($sql);
      $packs = array();
      foreach ($packs_r as $key => $pack_r) {
        $productsForTemplate = $this->getAspPackProducts($pack_r['id_pack'],$product['id_product']);
        if ($productsForTemplate) {
            $packs[] = $productsForTemplate;
        }

      }
      return $packs;
    }

    public function getAspPackProducts($id_pack,$this_id_product){
      $sql = "SELECT * FROM ps_asp_pack_product WHERE id_pack = $id_pack";
      $r = Db::getInstance()->executeS($sql);
      if (!$r) {
        return false;
      }
      $p = array($this_id_product);
      foreach ($r as $key => $l) {
        $id = Product::getIdByReference($l['reference']);
        if ($id) {
          $p[] = $id;
        }
        else {
          $sql = "SELECT * FROM ps_product_attribute where reference = ".$l['reference'];
          $comb_r = Db::getInstance()->executeS($sql);
          if ($comb_r) {
            $p[] = $comb_r[0]['id_product'];

          }
        }
      }
      $p = array_unique($p);
      $productsForTemplate = $this->getProductsTemplate($p);
      return $productsForTemplate;

    }

    public function getAspInstructions($product){
      $refs = array($product['reference']);
      $comb_refs = Db::getInstance()->executeS("SELECT reference from ps_product_attribute where id_product = ".$product['id_product']);
      foreach ($comb_refs as $key => $comb_ref) {
        $refs[] = $comb_ref['reference'];
      }
      $ref_in = "";
      $refs = array_unique($refs);
      foreach ($refs as $key => $ref) {
        $ref_in .= "'".$ref."',";
      }
      $ref_in = trim($ref_in,',');
      $sql = "SELECT * FROM ps_asp_doc_product WHERE reference in ($ref_in)";
      $doc_products_r = Db::getInstance()->executeS($sql);
      $text = "";
      foreach ($doc_products_r as $key => $l) {
        $sql = "SELECT text FROM ps_asp_doc where id_doc=".$l['id_doc'];
        $r = Db::getInstance()->executeS($sql);
        if ($r) {
          $text .=$r[0]['text'];
        }
      }
      if ($text) {
        return $text;
      }
      return false;
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
      $products = Context::getContext()->cart->getProducts();
      $costs = 0;
      foreach ($products as $key => $p) {
        $price = $this->getProductCountryShippingPrice($p['id_product'],$p['id_product_attribute']);
        if ($price) {
          $costs += $price*$p['quantity'];
        }
      }
      return $costs;

    }


    public function getOrderShippingCostExternal($params)
    {
        //return 20;
    }

    public function getProductIdsByCat($id_cat){
      $r = Db::getInstance()->executeS('Select * from ps_category_product WHERE id_category = '.$id_cat);
      $p = array();
      foreach($r as $l){
        $p[] = $l['id_product'];
      }
      return $p;
    }

    public static function getFlashSaleProductIds(){
      $sql = "SELECT * FROM ps_specific_price where ps_specific_price.from <= '".date('Y-m-d h:i:s')."' and ps_specific_price.to >= '".date('Y-m-d h:i:s')."'";
      $r = Db::getInstance()->executeS($sql);
      $p = array();
      foreach ($r as $key => $l) {
        if (!isset($p[$l['id_product']])) {
          $p[$l['id_product']] = [];
        }
        if (isset($l['id_product_attribute'])) {
          $p[$l['id_product']][$l['id_product_attribute']] = $l['id_product_attribute'];
        }
      }
      return $p;
      //return $this->getProductIdsByCat(Configuration::get("GECPS_FS_CAT"));
    }

    public static function getPrivateSaleProductIds(){
      $sql = "SELECT * FROM ps_asp_private_sales where ps_asp_private_sales.start <= '".date('Y-m-d h:i:s')."' and ps_asp_private_sales.end >= '".date('Y-m-d h:i:s')."'";
      $r = Db::getInstance()->executeS($sql);
      if (!$r) {
        return [];
      }

      $ref_in = "";
      $p = [];
      foreach ($r as $key => $l) {
        if ($l['id_products']) {
          $ref_in .= $l['id_products'].',';
        }

      }
      $ref_in = trim($ref_in,',');
      $p_r = Db::getInstance()->executeS("
        SELECT id_product,0 id_product_attribute FROM ps_product where reference in ($ref_in)
        Union
        SELECT id_product,id_product_attribute FROM ps_product_attribute where reference in ($ref_in)
      ");
      foreach ($p_r as $key => $l) {
        if (!isset($p[$l['id_product']])) {
          $p[$l['id_product']] = [];
        }
        if (isset($l['id_product_attribute'])) {
          $p[$l['id_product']][$l['id_product_attribute']] = $l['id_product_attribute'];
        }
      }
      return $p;
      //return $this->getProductIdsByCat(Configuration::get("GECPS_PS_CAT"));
    }

    public function initVars(){
      $fs_p = self::getFlashSaleProductIds();
      $ps_p = self::getPrivateSaleProductIds();
      $this->vars = array(
        'fs_p' => $fs_p,
        'ps_p' => $ps_p
      );
    }

    public function checkIfProductInSelection($id_p,$id_pa,$s){
      $found=0;
      if (count($s)>0) {
        if (isset($s[$id_p])) {
          if ($id_pa) {
            if (isset($s[$id_p][$id_pa])) {
              $found=1;
            }
            else {
              $found=0;
            }
          }
          else {
            $found=1;
          }
        }
      }
      return $found;
    }

    public function hookActionProductFlagsModifier($params){
      $id_p = $params['product']['id_product'];
      $id_pa = $params['product']['id_product_attribute'];
      $fs_flag = $this->checkIfProductInSelection($id_p,$id_pa,$this->vars['fs_p']);
      if ($fs_flag) {
        $params['flags']['flash_sale'] = [
            'type' => 'flash_sale',
            'label' => $this->l('Flash sale'),
        ];
      }
      $ps_flag = $this->checkIfProductInSelection($id_p,$id_pa,$this->vars['ps_p']);
      if ($ps_flag) {
        $params['flags']['private_sale'] = [
            'type' => 'private_sale',
            'label' => $this->l('Private sale'),
        ];
      }
    }

    public function getOrderTrackingsAsp($order){

      $r = Db::getInstance()->executeS("
        SELECT * FROM ps_asp_order_trackings
        WHERE id_order = ".$order->details->id
      );

      return $r;


    }

    public function hookDisplayOrderTrackings($params){
      $order_trackings = $this->getOrderTrackingsAsp($params['order']);
      $merged_order_trackings=$this->mergeOrderTrackings($order_trackings);
      $this->smarty->assign(
        array(
          'order' => $params['order'],
          'order_tracking' => $merged_order_trackings
        )
      );
      return $this->display(__FILE__, 'order_tracking2.tpl');
    }

    public function mergeOrderTrackings($order_trackings){
      $r = array();
      foreach ($order_trackings as $key => $order_tracking) {
        $tracking_number = $order_tracking['tracking_number'];
        $date = DateTime::createFromFormat('Y-m-d', $order_tracking['date']);
        $order_tracking['date'] = $date->format('d/m/Y');
        if (!isset($r[$tracking_number])) {

          $r[$tracking_number] = $order_tracking;
          $r[$tracking_number]['products'] = [];
        }
        $r[$tracking_number]['products'][]=[
          'reference'=>$order_tracking['reference'],
          'id_product'=>$order_tracking['id_product'],
          'id_product_attribute'=>$order_tracking['id_product_attribute'],
          'quantity'=>$order_tracking['quantity'],
        ];


      }
        // var_dump($r);
      return $r;

    }

    public function checkLogin(){
      if ($this->context->controller->php_self == "category") {
        $id_category = (int) Tools::getValue('id_category');
        $r = Db::getInstance()->executeS("SELECT * FROM ps_category WHERE id_category=".$id_category);
        if ($r) {
          $id_parent = $r[0]['id_parent'];
          if ($id_parent == Configuration::get("GECPS_PS_CAT")) {
            if (!$this->context->customer->isLogged()) {
              $back = $this->context->link->getCategoryLink($id_category);
      				header('Location: '.$this->context->link->getPageLink('authentication').'?back='.$back);
      			}
          }
        }

      }

    }


    public function getProductCountryShippingPrice($id_p,$id_pa,$p=null,$variation_data=null,$with_tax=false){

      $shop_country_iso= $this->getIsoByShopName($this->context->shop->name);
      $id_country = Country::getByIso($shop_country_iso);
      $id_shop = $this->context->shop->id;
      if (!$p) {
        $p = new Product($id_p);
        if ($id_p) {
          $p->id_product_attribute = $id_pa;
        }
      }
      if (!$variation_data) {
        $variation_data = $this->getVariationData($p);
      }
      $sql = "SELECT * FROM ps_asp_shipping_prices WHERE id_product=$id_p and id_product_attribute = $id_pa and id_country=$id_country";
      $shipping_price = Db::getInstance()->executeS($sql);
      if ($shipping_price && $shipping_price[0]['id_country']==6) {
        $additional_shipping_cost = $shipping_price[0]['price'];
      }
      else {
        if (isset($p->id_product_attribute)&& $p->id_product_attribute) {
          $additional_shipping_cost = $variation_data['encombrement'];
        }
        else {
          $additional_shipping_cost = $p->additional_shipping_cost;
        }
      }
      if ($with_tax) {
        $c = Carrier::getCarriers($this->context->language->id)[0];
        $sql = "SELECT t.rate  FROM `ps_tax_rule` trg ,ps_tax t WHERE `id_tax_rules_group` in ( select id_tax_rules_group from ps_carrier_tax_rules_group_shop where id_shop=$id_shop and id_carrier=".$c['id_carrier']." ) AND `id_country` = $id_country AND trg.id_tax = t.id_tax";
        $r = Db::getInstance()->executeS($sql);
        $rate = $r[0]['rate']/100;

        return $additional_shipping_cost*(1+$rate);
      }
      else {
        return $additional_shipping_cost;
      }

    }

    public function getVariationData($p){
      $l_iso = strtolower($this->getIsoByShopName($this->context->shop->name));
      if (isset($p->id_product_attribute) && $p->id_product_attribute) {
        $r = Db::getInstance()->executeS("
          SELECT asp_v.*
          FROM ps_asp_variations asp_v,ps_product_attribute pa
          WHERE pa.reference = asp_v.reference
          AND pa.id_product_attribute = ".$p->id_product_attribute
        );
      }
      else {
        $r = Db::getInstance()->executeS("
          SELECT asp_v.*
          FROM ps_asp_variations asp_v
          WHERE asp_v.reference = ".$p->reference
        );
      }

      if ($r) {
        $variation_data = $r[0];
        $variation_data['guarantee'] =   $variation_data['guarantee_'.$l_iso];

      }
      else {
        $variation_data = false;
      }
      return $variation_data;
    }

    public function hookDisplayProductVariants($params){
      $currency = Currency::getCurrencyInstance((int) Context::getContext()->currency->id);
      $id_currency = $currency->id;
      $variation_data = $this->getVariationData($params['product']);
      $additional_shipping_cost = $this->getProductCountryShippingPrice($params['product']->id_product,$params['product']->id_product_attribute,$params['product'],$variation_data,true);
      $this->smarty->assign(
        array(
          'p' => $params['product'],
          'additional_shipping_cost' =>Context::getContext()->getCurrentLocale()->formatPrice($additional_shipping_cost,Currency::getIsoCodeById((int) $id_currency)),
          'variation_data' => $variation_data,
        )
      );
      return $this->display(__FILE__, 'comb_extra_info.tpl');
    }

    public function hookDisplayCombinationFirstImage($params){
      $p = new Product($params['p']->id_product);
      $currency = Currency::getCurrencyInstance((int) Context::getContext()->currency->id);
      $id_currency = $currency->id;
      $r = Db::getInstance()->executeS("
        SELECT pac.*,a.id_attribute_group
        FROM `ps_product_attribute_combination` pac, ps_attribute a
        WHERE pac.id_product_attribute = ".$params['p']->id_product_attribute."
        AND a.id_attribute = pac.id_attribute
        order by a.id_attribute_group
      ");
      if (count($r)==1) {
        $r2 = Db::getInstance()->executeS("
        SELECT * FROM ps_product_attribute_image pai
        WHERE pai.id_product_attribute
        IN (
          SELECT pac.id_product_attribute FROM ps_product_attribute_combination pac
          WHERE pac.id_attribute = ".$params['id_attribute']."
          AND pac.id_product_attribute
          IN (
              SELECT pa.id_product_attribute FROM ps_product_attribute pa
              WHERE pa.id_product = ".$params['p']->id."
          )
        )
        LIMIT 1"
        );
      }
      else {
        $sql = "
          SELECT * FROM (
              SELECT s1.*,pac.id_attribute FROM(
                  SELECT * FROM ps_product_attribute_image pai
                  WHERE
                      pai.id_product_attribute IN(
                      SELECT pac.id_product_attribute
                      FROM ps_product_attribute_combination pac
                      WHERE
                          pac.id_attribute = ".$params['id_attribute']."
                          AND pac.id_product_attribute IN(
                              SELECT
                                  pa.id_product_attribute
                              FROM
                                  ps_product_attribute pa
                              WHERE
                                  pa.id_product = ".$params['p']->id."
                      )
                  )
              ) s1
          LEFT JOIN ps_product_attribute_combination pac ON
              pac.id_product_attribute = s1.id_product_attribute AND id_attribute = ".$r[1]['id_attribute']."
          ) s2
          WHERE
              s2.id_attribute IS NOT NULL
          LIMIT 1 ";
        $r2 = Db::getInstance()->executeS($sql);
      }
      if ($r2) {
        $variation_price = $p->getPrice(1,$r2[0]['id_product_attribute']);
        $this->smarty->assign(
          array(
            "url_image" => $this->context->link->getImageLink($params['p']->link_rewrite,$r2[0]['id_image'],'small_default'),
            "link_only" => isset($params['link_only'])?$params['link_only']:0,
            "group_attribute" => $params['group_attribute'],
            "variation_price" =>Context::getContext()->getCurrentLocale()->formatPrice((float) $variation_price,Currency::getIsoCodeById((int) $id_currency)),
          )
        );
        return $this->display(__FILE__, 'comb_first_img.tpl');
      }

    }

    public function hookDisplayProductAvailability($params){
      $params['product']->stock_available = StockAvailable::getQuantityAvailableByProduct($params['product']->id_product,$params['product']->id_product_attribute,$this->context->shop->id);
      $this->smarty->assign(
        array(
          'product'=>$params['product']
        )
      );
      return $this->display(__FILE__, 'comb_availability.tpl');
    }

    public function hookDisplayProductPrettyPrice($params){
      $tmp = explode(',',$params['price']);
      $p1 = $tmp[0];
      $tmp2 = explode('n',$tmp[1]);
      $p2 = substr($tmp[1],0,2);
      $p3 = substr($tmp[1],strlen($tmp[1])-3,strlen($tmp[1])-1);


      $this->smarty->assign(
        array(
          'p1'=>$p1,
          'p2'=>$p2,
          'p3'=>$p3
        )
      );
      return $this->display(__FILE__, 'pretty_price.tpl');
    }

    public function hookDisplayProductFeatures($params){
      $p = $params['product'];
      $l_iso = strtolower($this->getIsoByShopName($this->context->shop->name));
      if ($p->id_product_attribute) {
        $r = Db::getInstance()->executeS("
          SELECT reference FROM ps_product_attribute
          WHERE id_product =".$p->id."
          AND id_product_attribute =".$p->id_product_attribute
        );
        $reference =$r[0]['reference'];

      }
      else {
        $reference = $p->reference;
      }
      $sql = "SELECT id_feature,name_$l_iso as name,value_$l_iso as value,type FROM ( SELECT aa.* FROM ps_asp_attributes aa WHERE aa.id_category IN( SELECT id_category FROM ps_category_product WHERE id_product = ".$p->id_product." AND id_category != 0 ) ) s1 LEFT JOIN( SELECT * FROM ps_asp_attribute_values aav WHERE aav.reference = '".$reference."' ) s2 ON s2.id_asp_attribute = s1.id ORDER BY `s1`.`position` ASC";

      $r = Db::getInstance()->executeS($sql);
      $f =[];

      foreach($r as $key => $l){
        if ($l['type']==1) {
          $f[] = $l;
        }
        else {
          if ($l['value']) {
            $f[] = $l;
          }
        }
      }

      $r = Db::getInstance()->executeS("SELECT * from ps_asp_variations where reference = '$reference'");
      if ($r) {
        $description = $r[0]["description_$l_iso"];
      }
      else{
        $description = false;
      }

      $this->smarty->assign(
          array(
            'product' => $p,
            'f'=>$f,
            'description'=>$description
          )
        );

      return $this->display(__FILE__, 'product_features.tpl');
    }

    protected function getImage($object, $id_image)
    {
        $retriever = new ImageRetriever(
            $this->context->link
        );

        return $retriever->getImage($object, $id_image);
    }

    public function hookDisplaySubCategories($params){
      $id_lang = $this->context->language->id;
      if (strpos(Configuration::get("GECPS_CATS_SLIDERS"),",".Tools::getValue('id_category').",")===false) {
        return false;
      }
      $current_cat = new Category(Tools::getValue('id_category'));
      $sub_cats = $current_cat->getSubCategories($id_lang,true);
      $sub_cats_array = [];
      foreach ($sub_cats as $key1 => $sub_cat) {
        $sub_cat_obj = new Category($sub_cat['id_category'],$this->context->language->id);
        $sub_cat['image'] = $this->getImage(
            $sub_cat_obj,
            $sub_cat_obj->id_image
        );
        $sub_cat['url'] = $this->context->link->getCategoryLink(
            $sub_cat['id_category'],
            $sub_cat['link_rewrite']
        );
        $sub_cats_array[$key1] = [$sub_cat];
        $sub_sub_cats = $sub_cat_obj->getSubCategories($id_lang,true);
        foreach ($sub_sub_cats as $key2 => $sub_sub_cat) {
          $sub_sub_cat['url'] = $this->context->link->getCategoryLink(
              $sub_sub_cat['id_category'],
              $sub_sub_cat['link_rewrite']
          );
          $sub_cats_array[$key1][] = $sub_sub_cat;
        }
      }

      $this->smarty->assign(
          array(
            "sub_cats" => $sub_cats_array
          )
        );

      return $this->display(__FILE__, 'sub_cats_slider.tpl');
    }

    public function hookDisplayPrettyOrderId($params){
      $pretty_id = $this->makePrettyOrderIdFromReference($params['ref_order']);
      $this->smarty->assign(
          array(
            "pretty_id" => $pretty_id
          )
        );
      return $this->display(__FILE__, 'pretty_order_id.tpl');
    }

    public function hookDisplayOrderConfirmationAddresses($params){
      $addresses = $params['order']->getAddresses();
      $this->smarty->assign(
          array(
            "addresses" => $addresses
          )
        );
      return $this->display(__FILE__, 'order_conf_addresses.tpl');
    }





}
