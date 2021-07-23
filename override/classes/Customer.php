<?php

use PrestaShop\PrestaShop\Adapter\CoreException;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
include(_PS_MODULE_DIR_."gecps/classes/simple_html_dom.php");

class Customer extends CustomerCore
{
  public $pwtoken;
  public $asp_id_client;
  public $asp_cookie;
  public $asp_sav_token;


  /**
   * @see ObjectModel::$definition
   */
  public static $definition = array(
      'table' => 'customer',
      'primary' => 'id_customer',
      'fields' => array(
          'secure_key' => array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),
          'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isCustomerName', 'required' => true, 'size' => 255),
          'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isCustomerName', 'required' => true, 'size' => 255),
          'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255),
          'passwd' => array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 255),
          'pwtoken' => array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => false, 'size' => 255),
          'last_passwd_gen' => array('type' => self::TYPE_STRING, 'copy_post' => false),
          'id_gender' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
          'birthday' => array('type' => self::TYPE_DATE, 'validate' => 'isBirthDate'),
          'newsletter' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
          'newsletter_date_add' => array('type' => self::TYPE_DATE, 'copy_post' => false),
          'ip_registration_newsletter' => array('type' => self::TYPE_STRING, 'copy_post' => false),
          'optin' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
          'website' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
          'company' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
          'siret' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
          'ape' => array('type' => self::TYPE_STRING, 'validate' => 'isApe'),
          'outstanding_allow_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
          'show_public_prices' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
          'id_risk' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
          'max_payment_days' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
          'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
          'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
          'note' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000, 'copy_post' => false),
          'is_guest' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
          'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
          'id_shop_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
          'id_default_group' => array('type' => self::TYPE_INT, 'copy_post' => false),
          'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
          'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
          'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
          'reset_password_token' => array('type' => self::TYPE_STRING, 'validate' => 'isSha1', 'size' => 40, 'copy_post' => false),
          'reset_password_validity' => array('type' => self::TYPE_DATE, 'validate' => 'isDateOrNull', 'copy_post' => false),
          'asp_id_client' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),
          'asp_cookie' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),
          'asp_sav_token' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),
      ),
  );

  public function getByEmail($email, $plaintextPassword = null, $ignoreGuest = true)
  {
      if (!Validate::isEmail($email) || ($plaintextPassword && !Validate::isPasswd($plaintextPassword))) {
          die(Tools::displayError());
      }

      $shopGroup = Shop::getGroupFromShop(Shop::getContextShopID(), false);

      $sql = new DbQuery();
      $sql->select('c.`passwd`');
      $sql->from('customer', 'c');
      $sql->where('c.`email` = \'' . pSQL($email) . '\'');
      if (Shop::getContext() == Shop::CONTEXT_SHOP && $shopGroup['share_customer']) {
          $sql->where('c.`id_shop_group` = ' . (int) Shop::getContextShopGroupID());
      } else {
          $sql->where('c.`id_shop` IN (' . implode(', ', Shop::getContextListShopID(Shop::SHARE_CUSTOMER)) . ')');
      }

      if ($ignoreGuest) {
          $sql->where('c.`is_guest` = 0');
      }
      $sql->where('c.`deleted` = 0');

      $passwordHash = Db::getInstance()->getValue($sql);

      try {
          /** @var \PrestaShop\PrestaShop\Core\Crypto\Hashing $crypto */
          $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');
      } catch (CoreException $e) {
          return false;
      }

      $shouldCheckPassword = null !== $plaintextPassword;
      if ($shouldCheckPassword && !$crypto->checkHash($plaintextPassword, $passwordHash)) {
          $customer_exits = Customer::customerExists($email);
          $r = false;
          if ($customer_exits) {
            $r = $this->getFromAsp($email,$plaintextPassword,1);
          }
          else {
            $r = $this->getFromAsp($email,$plaintextPassword,0);
          }
          if (!$r) {
            return false;
          }

      }

      $sql = new DbQuery();
      $sql->select('c.*');
      $sql->from('customer', 'c');
      $sql->where('c.`email` = \'' . pSQL($email) . '\'');
      if (Shop::getContext() == Shop::CONTEXT_SHOP && $shopGroup['share_customer']) {
          $sql->where('c.`id_shop_group` = ' . (int) Shop::getContextShopGroupID());
      } else {
          $sql->where('c.`id_shop` IN (' . implode(', ', Shop::getContextListShopID(Shop::SHARE_CUSTOMER)) . ')');
      }
      if ($ignoreGuest) {
          $sql->where('c.`is_guest` = 0');
      }
      $sql->where('c.`deleted` = 0');

      $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

      if (!$result) {
          return false;
      }

      $this->id = $result['id_customer'];
      foreach ($result as $key => $value) {
          if (property_exists($this, $key)) {
              $this->{$key} = $value;
          }
      }

      if ($shouldCheckPassword && !$crypto->isFirstHash($plaintextPassword, $passwordHash)) {
          $this->passwd = $crypto->hash($plaintextPassword);
          $this->update();
      }

      return $this;
  }

  public function getFromAsp($username,$password,$login_only=1){
    $login_from_asp = self::loginFromAsp($username,$password,$login_only);
    return $login_from_asp;

  }

  static function loginFromAsp($username,$password,$login_only=1){
    $rand = date('Y-m-d h:i:s').rand();
    $cookie_file = _PS_ROOT_DIR_."/var/curl_cookies/$rand.txt";
    $customer_exists = Customer::customerExists($username);
    $data = array('ident_login'=>$username,'ident_pass'=>$password);
    $post_data = http_build_query($data);
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://www.habitatetjardin.com/client/identification.html',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $post_data,
      CURLOPT_FOLLOWLOCATION => false,
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
    $login_r_asp = strpos($response,'<title>Objet')!==false?true:false;
    $curl_cookie = Customer::copyCurlCookieToCookie($cookie_file);

    if ($login_r_asp) {
      if (isset($curl_cookie['0'])) {
        if ($login_only) {
          return $curl_cookie;
        }
        if (!$customer_exists) {
          return self::addCustomerFromAsp($username,$password,$curl_cookie);
        }
      }
    }

    unlink($cookie_file);
    return false;
  }

  static function addCustomerFromAsp($username,$password,$curl_cookie){
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
      $datas = self::parseAspCustomerAddressesForm($form);
      $sav_token = self::getCustomerSavToken($curl_cookie);
      if (isset($datas['customer'])) {
        $datas['customer']['password'] = $password;
        $datas['customer']['email'] = $username;
        $datas['customer']['asp_cookie'] = $cookie;
        $datas['customer']['asp_sav_token'] = $sav_token;

        return self::addCustomerFromAspDatas($datas);
      }
      return false;




    }
  }

  static function addCustomerFromAspDatas($datas){
    $c = new Customer();
    $language = Context::getContext()->language;
    /** @var \PrestaShop\PrestaShop\Core\Crypto\Hashing $crypto */
    $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');
    $c->passwd = $crypto->hash($datas['customer']['password']);
    foreach ($datas['customer'] as $key => $value) {
      $c->$key = $value;
    }
    $c->addGroups([Configuration::get('PS_CUSTOMER_GROUP')]);
    $c->id_default_group = Configuration::get('PS_CUSTOMER_GROUP');
    if ($c->save()) {
      self::addAddressToCustomerAsp($datas,$c);
      return $c;
    }
    else {
      return false;
    }

  }

  static function addAddressToCustomerAsp($datas,$c){
    $types = array('address_invoice','address_delivery');
    foreach ($types as $key_type => $type) {
      if (isset($datas[$type]['id_country'])) {
        $a = new Address();
        foreach ($datas[$type] as $key => $value) {
          $a->$key = $value;
        }
        $a->id_customer = $c->id;
        $alias = $a->city.' '.($key_type+1);
        if (strlen($alias)>30) {
          $alias = substr($alias,0,29);
        }
        $a->alias = $alias;
        $a->save();
      }
    }



  }

  static function parseAspCustomerAddressesForm($form){
    $datas['customer']['asp_id_client'] = $form->find('input[name=idclient]',0)->value;
    $addr_invoice_table = $form->find('table',0);
    $gender = $addr_invoice_table->find('input[checked]',0)->value;
    $datas['customer']['id_gender'] = ($gender=='M.'?1:2);
    $datas['customer']['lastname'] = $addr_invoice_table->find('input[name=nom]',0)->value;
    $datas['customer']['firstname'] = $addr_invoice_table->find('input[name=prenom]',0)->value;

    $datas['address_invoice']['lastname'] = $datas['customer']['lastname'];
    $datas['address_invoice']['firstname'] = $datas['customer']['firstname'];
    $datas['address_invoice']['company'] = $addr_invoice_table->find('input[name=societe]',0)->value;
    $datas['address_invoice']['country_code3'] = $addr_invoice_table->find('option[selected]',0)->value;
    $datas['address_invoice']['country_code2'] = Country::getCountryCode2By3($datas['address_invoice']['country_code3']);
    if ($datas['address_invoice']['country_code2']) {
      $datas['address_invoice']['id_country'] = Country::getByIso($datas['address_invoice']['country_code2']);
    }
    $datas['address_invoice']['address1'] = $addr_invoice_table->find('input[name=adresse]',0)->value;
    $datas['address_invoice']['postcode'] = $addr_invoice_table->find('input[name=cp]',0)->value;
    $datas['address_invoice']['city'] = $addr_invoice_table->find('input[name=ville]',0)->value;
    $datas['address_invoice']['phone_mobile'] = $addr_invoice_table->find('input[name=portable]',0)->value;
    $datas['address_invoice']['phone'] = $addr_invoice_table->find('input[name=tel]',0)->value;

    $has_address_delivery = $form->find('input[name=livraison][checked]',0)->value =='oui'?false:true;

    if ($has_address_delivery) {
      $addr_delivery_table = $form->find('table',1);
      $datas['address_delivery']['lastname'] = $addr_delivery_table->find('input[name=liv_nom]',0)->value;
      $datas['address_delivery']['firstname'] = $addr_delivery_table->find('input[name=liv_prenom]',0)->value;
      $datas['address_delivery']['company'] = $addr_delivery_table->find('input[name=liv_societe]',0)->value;
      $datas['address_delivery']['country_code3'] = $addr_delivery_table->find('option[selected]',0)->value;
      $datas['address_delivery']['country_code2'] = Country::getCountryCode2By3($datas['address_delivery']['country_code3']);
      if ($datas['address_delivery']['country_code2']) {
        $datas['address_delivery']['id_country'] = Country::getByIso($datas['address_delivery']['country_code2']);
      }
      $datas['address_delivery']['address1'] = $addr_delivery_table->find('input[name=liv_adresse]',0)->value;
      $datas['address_delivery']['postcode'] = $addr_delivery_table->find('input[name=liv_cp]',0)->value;
      $datas['address_delivery']['city'] = $addr_delivery_table->find('input[name=liv_ville]',0)->value;
      $datas['address_delivery']['phone_mobile'] = $addr_delivery_table->find('input[name=liv_portable]',0)->value;
      $datas['address_delivery']['phone'] = $addr_delivery_table->find('input[name=liv_tel]',0)->value;
    }


    return $datas;


  }

  static function copyCurlCookieToCookie($file){
    $cookies = Customer::getCurlCookie($file);
    return $cookies;


  }

  static function getCurlCookie($file){
    $str = file_get_contents($file);
    $tmp = explode('	',$str);
    $r = array(trim($tmp[count($tmp)-2]),trim($tmp[count($tmp)-1]));
    return $r;
  }


  public static function customerExistsAsp($email){
    $curl = curl_init();
    $data = array('email'=>$email);
    $post_data = http_build_query($data);

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://www.habitatetjardin.com/hetj/client/oubli-motdepasse.asp',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $post_data,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: ASPSESSIONIDASDQABBA=IIKLJKEBIPLNCNHDHGJJDLGC; cptv=2'
      ),
    ));

    $result = curl_exec($curl);
    curl_close($curl);
    $html = new simple_html_dom();
    $html->load($result);

    $alert = $html->find('.alerte',0)->plaintext;
    if (strpos($alert,'Vos informations de connexion') !== false) {
      return true;
    }
    return false;
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
      $count = count($trs)-3;var_dump($count);
      $tr = $html->find('.news-in2.fondclient table tr',$count);
      return $tr->find('a',1)->href;
    }
    return false;
  }



}
