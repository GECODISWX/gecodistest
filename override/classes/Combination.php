<?php

class Combination extends CombinationCore
{

  public static $definition = [
      'table' => 'product_attribute',
      'primary' => 'id_product_attribute',
      'fields' => [
          'id_product' => ['type' => self::TYPE_INT, 'shop' => 'both', 'validate' => 'isUnsignedId', 'required' => true],
          'location' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64],
          'ean13' => ['type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13],
          'isbn' => ['type' => self::TYPE_STRING, 'validate' => 'isIsbn', 'size' => 32],
          'upc' => ['type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12],
          'mpn' => ['type' => self::TYPE_STRING, 'validate' => 'isMpn', 'size' => 40],
          'quantity' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 10],
          'reference' => ['type' => self::TYPE_STRING, 'size' => 64],
          'supplier_reference' => ['type' => self::TYPE_STRING, 'size' => 64],

          /* Shop fields */
          'wholesale_price' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'size' => 27],
          'price' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20],
          'ecotax' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'size' => 20],
          'weight' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isFloat'],
          'unit_price_impact' => ['type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20],
          'minimal_quantity' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId', 'required' => true],
          'low_stock_threshold' => ['type' => self::TYPE_INT, 'shop' => true, 'allow_null' => true, 'validate' => 'isInt'],
          'low_stock_alert' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
          'default_on' => ['type' => self::TYPE_BOOL, 'allow_null' => true, 'shop' => true, 'validate' => 'isBool'],
          'available_date' => ['type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'],
      ],
  ];
}
