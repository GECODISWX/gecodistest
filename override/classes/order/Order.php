<?php

use PrestaShop\PrestaShop\Adapter\ServiceLocator;

class Order extends OrderCore
{
  public $asp_id_order;

  public static $definition = [
      'table' => 'orders',
      'primary' => 'id_order',
      'fields' => [
          'id_address_delivery' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
          'id_address_invoice' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
          'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
          'id_currency' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
          'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
          'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
          'id_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
          'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
          'id_carrier' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
          'current_state' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
          'secure_key' => ['type' => self::TYPE_STRING, 'validate' => 'isMd5'],
          'payment' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
          'module' => ['type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true],
          'recyclable' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'gift' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'gift_message' => ['type' => self::TYPE_STRING, 'validate' => 'isMessage'],
          'mobile_theme' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'total_discounts' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'total_discounts_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'total_discounts_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'total_paid' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
          'total_paid_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'total_paid_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'total_paid_real' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
          'total_products' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
          'total_products_wt' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
          'total_shipping' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'total_shipping_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'total_shipping_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'carrier_tax_rate' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
          'total_wrapping' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'total_wrapping_tax_incl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'total_wrapping_tax_excl' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice'],
          'round_mode' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
          'round_type' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
          'shipping_number' => ['type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'],
          'conversion_rate' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true],
          'invoice_number' => ['type' => self::TYPE_INT],
          'delivery_number' => ['type' => self::TYPE_INT],
          'invoice_date' => ['type' => self::TYPE_DATE],
          'delivery_date' => ['type' => self::TYPE_DATE],
          'valid' => ['type' => self::TYPE_BOOL],
          'reference' => ['type' => self::TYPE_STRING],
          'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
          'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
          'asp_id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
      ],
  ];

  public static function getOrdersIdByDateAllShops($date_from, $date_to, $id_customer = null, $type = null)
  {
      $sql = 'SELECT `id_order`
              FROM `' . _DB_PREFIX_ . 'orders`
              WHERE DATE_ADD(date_upd, INTERVAL -1 DAY) <= \'' . pSQL($date_to) . '\' AND date_upd >= \'' . pSQL($date_from) . '\'
                  ' .($type ? ' AND `' . bqSQL($type) . '_number` != 0' : '')
                  . ($id_customer ? ' AND id_customer = ' . (int) $id_customer : '');
      $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

      $orders = [];
      foreach ($result as $order) {
          $orders[] = (int) $order['id_order'];
      }

      return $orders;
  }
}
