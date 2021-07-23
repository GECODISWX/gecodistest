<?php
/**
 * 2017 IQIT-COMMERCE.COM
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement
 *
 *  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
 *  @copyright 2017 IQIT-COMMERCE.COM
 *  @license   Commercial license (You can not resell or redistribute this software.)
 *
 */

class IqitProductReview extends ObjectModel
{
    public $id_iqitreviews_products;
    public $id_product;
    public $id_customer;
    public $id_guest;
    public $customer_name;
    public $title;
    public $comment;
    public $rating;
    /**
     * @var int 0=hidden 1=published
     */
    public $status;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */

    public static $definition = array(
        'table' => 'iqitreviews_products',
        'primary' => 'id_iqitreviews_products',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_guest' => array('type' => self::TYPE_INT),
            'customer_name' => array('type' => self::TYPE_STRING, 'size' => 255),
            'title' => array('type' => self::TYPE_STRING, 'size' => 255, 'required' => true),
            'comment' => array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'size' => 65535, 'required' => true),
            'rating' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE),
        ),
    );

    public static function clearProductReviews($idProduct)
    {
        if (!Validate::isUnsignedInt($idProduct)) {
            return;
        }
        Db::getInstance()->delete(self::$definition['table'], 'id_product = ' . (int) $idProduct);
    }



    public static function getByCustomer($idProduct, $idCustomer, $getLast = false, $idGuest = false)
    {
        $cacheId = 'IqitProductReview::getByCustomer_'.(int)$idProduct.'-'.(int)$idCustomer.'-'.(bool)$getLast.'-'.(int)$idGuest;
        if (!Cache::isStored($cacheId)) {
            $results = Db::getInstance()->executeS('
				SELECT *
				FROM `'._DB_PREFIX_.self::$definition['table'].'` pr
				WHERE pr.`id_product` = '.(int)$idProduct.'
				AND '.(!$idGuest ? 'pr.`id_customer` = '.(int)$idCustomer : 'pr.`id_guest` = '.(int) $idGuest).'
				ORDER BY pr.`date_add` DESC '
                .($getLast ? 'LIMIT 1' : ''));
            if ($getLast && count($results)) {
                $results = array_shift($results);
            }
            Cache::store($cacheId, $results);
        }
        return Cache::retrieve($cacheId);
    }

    public static function getReferencesInProduct($id_product){
      $cacheId = 'IqitProductReview::getReferencesInProduct_'.(int)$id_product;
      if (!Cache::isStored($cacheId)) {
        $r = Db::getInstance()->executeS('
        SELECT reference FROM `ps_product` WHERE id_product = '.$id_product.'
        UNION
        SELECT reference FROM `ps_product_attribute` WHERE id_product = '.$id_product);
          Cache::store($cacheId, $r);
      }
      return Cache::retrieve($cacheId);
    }

    public static function getByProductAndAsp($idProduct, $status = 1){

      $ps_reviews = self::getByProduct($idProduct, $status = 1);
      $r = Db::getInstance()->executeS('SELECT * FROM ps_asp_product_reviews WHERE id_product = ('.$idProduct.') order by date DESC');
      foreach ($r as $key => $l) {
        if (strlen($l['review'])>40) {
          $title = substr($l['review'],0,40).'...';
        }
        else {
          $title = $l['review'];
        }
        $asp_review = array(
          'customer_name' => $l['first_name'].' '.$l['last_name'],
          'date_add' => $l['date'],
          'rating' => $l['score'],
          'status' => 1,
          'title' => $title,
          'comment' => $l['review'],
          'id_product' => $idProduct
        );
        $ps_reviews[] = $asp_review;
      }


      //var_dump($refs);

      return $ps_reviews;
    }

    public static function getByProduct($idProduct, $status = 1)
    {
        if (!Validate::isUnsignedId($idProduct)) {
            return false;
        }
        if (!Validate::isUnsignedId($status)) {
            return false;
        }

        $cacheId = 'IqitProductReview::getByProduct_'.(int)$idProduct;
        if (!Cache::isStored($cacheId)) {
            $result = Db::getInstance()->executeS('
				SELECT *
				FROM `'._DB_PREFIX_.self::$definition['table'].'` pr
				WHERE pr.`id_product` = '.(int)$idProduct
                .($status  ? ' AND pr.`status` = 1' : '').'
				ORDER BY pr.`date_add` DESC ');

            Cache::store($cacheId, $result);
        }
        return Cache::retrieve($cacheId);
    }

    public static function getLatestReview($limit=20){
      if (!Validate::isUnsignedId($limit)) {
          return false;
      }

      $cacheId = 'IqitProductReview::homeLatestList';
      if (!Cache::isStored($cacheId)) {
        $result = Db::getInstance()->executeS('
          SELECT *
          FROM `'._DB_PREFIX_.self::$definition['table'].'` pr
          WHERE pr.`status` = 1
          ORDER BY pr.`date_add` DESC
          LIMIT '.$limit);

        Cache::store($cacheId, $result);
      }

      return Cache::retrieve($cacheId);
    }


    public static function getLatestReviewAndAsp($limit=20){
      if (!Validate::isUnsignedId($limit)) {
          return false;
      }

      $cacheId = 'IqitProductReview::getLatestReviewAndAsp';
      if (!Cache::isStored($cacheId)) {
        $result = Db::getInstance()->executeS("
          SELECT * FROM (
            SELECT pr.id_product,pr.customer_name, pr.comment, pr.title, pr.rating, pr.date_add FROM ps_iqitreviews_products pr WHERE pr.status =1
            UNION
            SELECT apr.id_product,concat(apr.first_name, ' ', apr.last_name), apr.review, apr.review ,apr.score, apr.date FROM ps_asp_product_reviews apr where id_product !=0) results
          ORDER BY date_add DESC
          LIMIT ".$limit);

        Cache::store($cacheId, $result);
      }

      return Cache::retrieve($cacheId);
    }

    public static function getSnippetDataFromAsp($idProduct){
      if (!Validate::isUnsignedId($idProduct)) {
          return false;
      }

      $cacheId = 'IqitProductReview::getSnippetDataFromAsp_'.(int)$idProduct;
      if (!Cache::isStored($cacheId)) {
          $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
      SELECT (SUM(pr.`score`) / COUNT(pr.`score`)) AS avarageRating, COUNT(pr.`score`) as reviewsNb
      FROM ps_asp_product_reviews pr
      WHERE pr.`id_product` = '.(int)$idProduct);

          Cache::store($cacheId, $result);
      }
      return Cache::retrieve($cacheId);
    }

    public static function getSnippetData($idProduct)
    {
        if (!Validate::isUnsignedId($idProduct)) {
            return false;
        }

        $cacheId = 'IqitProductReview::getSnippetData_'.(int)$idProduct;
        if (!Cache::isStored($cacheId)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT (SUM(pr.`rating`) / COUNT(pr.`rating`)) AS avarageRating, COUNT(pr.`rating`) as reviewsNb
				FROM `'._DB_PREFIX_.self::$definition['table'].'` pr
				WHERE pr.`status` = 1  AND pr.`id_product` = '.(int)$idProduct);

            Cache::store($cacheId, $result);
        }
        return Cache::retrieve($cacheId);
    }

    public static function mergeSnippetData($snippet_ps,$snippet_asp){
      if (!$snippet_ps['reviewsNb'] && !$snippet_asp['reviewsNb']) {
        return false;
      }
      $avarageRating = ($snippet_ps['avarageRating']*$snippet_ps['reviewsNb'] + $snippet_asp['avarageRating']*$snippet_asp['reviewsNb'])/($snippet_ps['reviewsNb']+$snippet_asp['reviewsNb']);
      $result = array(
        'avarageRating' => $avarageRating,
        'reviewsNb' => $snippet_ps['reviewsNb']+$snippet_asp['reviewsNb']
      );
      return $result;
    }

    public static function getSnippetDataAndAsp($id_product){
      if (!Validate::isUnsignedId($id_product)) {
          return false;
      }

      $cacheId = 'IqitProductReview::getSnippetDataAndAsp_'.(int)$id_product;
      if (!Cache::isStored($cacheId)){
        $snippet_ps = self::getSnippetData($id_product);
        $snippet_asp = self::getSnippetDataFromAsp($id_product);
        $result = self::mergeSnippetData($snippet_ps,$snippet_asp);
        Cache::store($cacheId, $result);
      }

      return Cache::retrieve($cacheId);
    }
}
