<?php

use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter;

class HistoryController extends HistoryControllerCore
{
  public static function getUrlToInvoice($order, $context)
  {
      $r = Db::getInstance()->executeS('SELECT * FROM ps_asp_order_extras WHERE id_order = '.$order->id);
      if (count($r)) {
        return $r[0]['invoice'];
      }
      else {
        return false;
      }
  }
}
