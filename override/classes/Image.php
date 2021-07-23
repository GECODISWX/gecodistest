<?php

class Image extends ImageCore
{
  public $asp_file;

  public static $definition = [
      'table' => 'image',
      'primary' => 'id_image',
      'multilang' => true,
      'fields' => [
          'id_product' => ['type' => self::TYPE_INT, 'shop' => 'both', 'validate' => 'isUnsignedId', 'required' => true],
          'asp_file' => ['type' => self::TYPE_STRING,'validate' => 'isString', 'size' => 256],
          'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
          'cover' => ['type' => self::TYPE_BOOL, 'allow_null' => true, 'validate' => 'isBool', 'shop' => true],
          'legend' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128],
      ],
  ];

}
