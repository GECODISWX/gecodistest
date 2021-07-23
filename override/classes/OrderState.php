<?php

class OrderState extends OrderStateCore
{
  public $payment_state_code;

  public static $definition = [
      'table' => 'order_state',
      'primary' => 'id_order_state',
      'multilang' => true,
      'fields' => [
          'send_email' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'module_name' => ['type' => self::TYPE_STRING, 'validate' => 'isModuleName'],
          'invoice' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'color' => ['type' => self::TYPE_STRING, 'validate' => 'isColor'],
          'logable' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'shipped' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'unremovable' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'delivery' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'hidden' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'paid' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'payment_state_code' => ['type' => self::TYPE_STRING,'validate' => 'isUnsignedInt'],
          'pdf_delivery' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'pdf_invoice' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'deleted' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

          /* Lang fields */
          'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64],
          'template' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isTplName', 'size' => 64],
      ],
  ];


}
