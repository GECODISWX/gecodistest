<?php
/**
 * HiPay Enterprise SDK Prestashop
 *
 * 2019 HiPay
 *
 * NOTICE OF LICENSE
 *
 * @author    HiPay <support.tpp@hipay.com>
 * @copyright 2019 HiPay
 * @license   https://github.com/hipay/hipay-enterprise-sdk-prestashop/blob/master/LICENSE.md
 */

require_once(dirname(__FILE__) . '/HipayDBQueryAbstract.php');
/**
 *
 * @author      HiPay <support.tpp@hipay.com>
 * @copyright   Copyright (c) 2019 - HiPay
 * @license     https://github.com/hipay/hipay-enterprise-sdk-prestashop/blob/master/LICENSE.md
 * @link    https://github.com/hipay/hipay-enterprise-sdk-prestashop
 */
class HipayDBThreeDSQuery extends HipayDBQueryAbstract
{
    public function cartAlreadyOrdered($customerId, $products)
    {
        $sql = 'SELECT id_order FROM `' . _DB_PREFIX_ . 'orders` o' .
            ' WHERE id_customer = ' . $customerId .
            ' AND (SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'order_detail` od WHERE od.id_order = o.id_order) = ' . count($products);

        $result = Db::getInstance()->executeS($sql);

        // If account has at least one order with item number matching current
        if(count($result) > 0){
            // We now go through every matching order to check on the products themselves
            foreach($result as $line){
                $detailsSQL = 'SELECT product_id, product_quantity, id_shop, product_attribute_id FROM `' . _DB_PREFIX_ . 'order_detail` od '.
                ' WHERE od.id_order = ' . $line["id_order"];

                $orderDetails = Db::getInstance()->executeS($detailsSQL);

                $found = true;
                // Going through ordered products
                foreach ($orderDetails as $aDetail){
                    // If an ordered product doesn't match current order, we skip to the next
                    if(!in_array($aDetail, $products)){
                        $found = false;
                        break;
                    }
                }

                if($found){
                    return true;
                }
            }
        }

        return false;
    }

    public function getNbPaymentAttempt($customerId, $paymentStart, $paymentMethods)
    {

        $sql = 'SELECT customer_id, COUNT(DISTINCT order_id) AS count 
            FROM `' . _DB_PREFIX_ . HipayDBQueryAbstract::HIPAY_TRANSACTION_TABLE .
            '` WHERE customer_id = ' . pSQL((int)$customerId) .
            ' AND payment_start >= "' . $paymentStart . '"' .
            ' AND payment_product IN (\'' . implode("','", $paymentMethods) . '\')' .
            ' GROUP BY customer_id;';

        $result = Db::getInstance()->getRow($sql);

        if (isset($result['count'])) {
            return $result['count'];
        }

        return 0;
    }

    public function getDateAddressFirstUsed($addressId)
    {

        $sql = 'SELECT date_add 
            FROM `' . _DB_PREFIX_ . 'orders`' .
            ' WHERE id_address_delivery = ' . pSQL((int)$addressId) .
            ' OR id_address_invoice = ' . $addressId .
            ' ORDER BY date_add ASC;';

        $result = Db::getInstance()->getRow($sql);

        if (isset($result['date_add'])) {
            return $result['date_add'];
        }

        return false;
    }

    public function getLastTransactionReference($customerId)
    {
        $sql = 'SELECT transaction_id FROM `' . _DB_PREFIX_ . 'order_payment`' .
            ' JOIN `' . _DB_PREFIX_ . 'orders` o ON order_reference = o.id_order' .
            ' WHERE id_customer = ' . pSQL((int)$customerId) .
            ' AND transaction_id IS NOT NULL' .
            ' ORDER BY o.date_add DESC;';

        $result = Db::getInstance()->getRow($sql);

        if (isset($result['transaction_id'])) {
            if(strpos($result['transaction_id'], "BO_TPP") !== FALSE){
                $transactionId = substr($result['transaction_id'], 0, strpos($result['transaction_id'], '-'));
            } else {
                $transactionId = $result['transaction_id'];
            }
            return $transactionId;
        } else {
            $sql = 'SELECT transaction_ref FROM `' . _DB_PREFIX_ . 'hipay_transaction` ht' .
                ' JOIN `' . _DB_PREFIX_ . 'orders` o ON ht.order_id = o.id_order' .
                ' WHERE id_customer = ' . pSQL((int)$customerId) .
                ' AND transaction_ref IS NOT NULL' .
                ' ORDER BY hp_id ASC;';

            $result = Db::getInstance()->getRow($sql);

            if (isset($result['transaction_ref'])) {
                return $result['transaction_ref'];
            }
        }


        return null;
    }
}