<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class CategoryCore.
 */
class Category extends CategoryCore
{
  public $description2;

  public static $definition = [
      'table' => 'category',
      'primary' => 'id_category',
      'multilang' => true,
      'multilang_shop' => true,
      'fields' => [
          'nleft' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
          'nright' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
          'level_depth' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
          'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
          'id_parent' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
          'id_shop_default' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
          'is_root_category' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
          'position' => ['type' => self::TYPE_INT],
          'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
          'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
          /* Lang fields */
          'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128],
          'link_rewrite' => [
              'type' => self::TYPE_STRING,
              'lang' => true,
              'validate' => 'isLinkRewrite',
              'required' => true,
              'size' => 128,
              'ws_modifier' => [
                  'http_method' => WebserviceRequest::HTTP_POST,
                  'modifier' => 'modifierWsLinkRewrite',
              ],
          ],
          'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
          'description2' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
          'meta_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
          'meta_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 512],
          'meta_keywords' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
      ],
  ];
}
