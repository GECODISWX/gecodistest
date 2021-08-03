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
          'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true, 'shop' => true],
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


  /**
   * Return current category childs.
   *
   * @param int $idLang Language ID
   * @param bool $active return only active categories
   *
   * @return array Categories
   */
  public function getSubCategories($idLang, $active = true)
  {
      $sqlGroupsWhere = '';
      $sqlGroupsJoin = '';
      if (Group::isFeatureActive()) {
          $sqlGroupsJoin = 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cg.`id_category` = c.`id_category`)';
          $groups = FrontController::getCurrentCustomerGroups();
          $sqlGroupsWhere = 'AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP'));
      }

      $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
  SELECT c.*, cl.`id_lang`, cl.`name`, cl.`description`, cl.`link_rewrite`, cl.`meta_title`, cl.`meta_keywords`, cl.`meta_description`
  FROM `' . _DB_PREFIX_ . 'category` c
  ' . Shop::addSqlAssociation('category', 'c') . '
  LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = ' . (int) $idLang . ' ' . Shop::addSqlRestrictionOnLang('cl') . ')
  ' . $sqlGroupsJoin . '
  WHERE `id_parent` = ' . (int) $this->id . '
  ' . ($active ? 'AND category_shop.`active` = 1' : '') . '
  ' . $sqlGroupsWhere . '
  GROUP BY c.`id_category`
  ORDER BY `level_depth` ASC, category_shop.`position` ASC');


      foreach ($result as &$row) {
          $row['id_image'] = Tools::file_exists_cache($this->image_dir . $row['id_category'] . '.jpg') ? (int) $row['id_category'] : Language::getIsoById($idLang) . '-default';
          $row['legend'] = 'no picture';
      }

      return $result;
  }


  /**
   * Get children of the given Category.
   *
   * @param int $idParent Parent Category ID
   * @param int $idLang Language ID
   * @param bool $active Active children only
   * @param bool $idShop Shop ID
   *
   * @return array Children of given Category
   */
  public static function getChildren($idParent, $idLang, $active = true, $idShop = false)
  {
      if (!Validate::isBool($active)) {
          die(Tools::displayError());
      }

      $cacheId = 'Category::getChildren_' . (int) $idParent . '-' . (int) $idLang . '-' . (bool) $active . '-' . (int) $idShop;
      if (!Cache::isStored($cacheId)) {
          $query = 'SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, category_shop.`id_shop`
    FROM `' . _DB_PREFIX_ . 'category` c
    LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
    ' . Shop::addSqlAssociation('category', 'c') . '
    WHERE `id_lang` = ' . (int) $idLang . '
    AND c.`id_parent` = ' . (int) $idParent . '
    ' . ($active ? 'AND category_shop.`active` = 1' : '') . '
    GROUP BY c.`id_category`
    ORDER BY category_shop.`position` ASC';
          $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
          Cache::store($cacheId, $result);

          return $result;
      }

      return Cache::retrieve($cacheId);
  }

  /**
   * Check if the given Category has child categories.
   *
   * @param int $idParent Parent Category ID
   * @param int $idLang Language ID
   * @param bool $active Active children only
   * @param bool $idShop Shop ID
   *
   * @return bool Indicates whether the given Category has children
   */
  public static function hasChildren($idParent, $idLang, $active = true, $idShop = false)
  {
      if (!Validate::isBool($active)) {
          die(Tools::displayError());
      }

      $cacheId = 'Category::hasChildren_' . (int) $idParent . '-' . (int) $idLang . '-' . (bool) $active . '-' . (int) $idShop;
      if (!Cache::isStored($cacheId)) {
          $query = 'SELECT c.id_category, "" as name
    FROM `' . _DB_PREFIX_ . 'category` c
    LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . ')
    ' . Shop::addSqlAssociation('category', 'c') . '
    WHERE `id_lang` = ' . (int) $idLang . '
    AND c.`id_parent` = ' . (int) $idParent . '
    ' . ($active ? 'AND category_shop.`active` = 1' : '') . ' LIMIT 1';
          $result = (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
          Cache::store($cacheId, $result);

          return $result;
      }

      return Cache::retrieve($cacheId);
  }




}
