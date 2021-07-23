<?php
/**
 * HiPay Enterprise SDK Prestashop
 *
 * 2017 HiPay
 *
 * NOTICE OF LICENSE
 *
 * @author    HiPay <support.tpp@hipay.com>
 * @copyright 2017 HiPay
 * @license   https://github.com/hipay/hipay-enterprise-sdk-prestashop/blob/master/LICENSE.md
 */

require_once(dirname(__FILE__) . '/../../lib/vendor/autoload.php');
require_once(dirname(__FILE__) . '/../apiCaller/ApiCaller.php');
require_once(dirname(__FILE__) . '/../apiFormatter/PaymentMethod/CardTokenFormatter.php');
require_once(dirname(__FILE__) . '/../apiFormatter/PaymentMethod/GenericPaymentMethodFormatter.php');
require_once(dirname(__FILE__) . '/../apiFormatter/Info/DeliveryShippingInfoFormatter.php');
require_once(dirname(__FILE__) . '/../apiFormatter/Cart/CartFormatter.php');
require_once(dirname(__FILE__) . '/../helper/dbquery/HipayDBUtils.php');
require_once(dirname(__FILE__) . '/../helper/HipayHelper.php');
require_once(dirname(__FILE__) . '/../../classes/helper/enums/ApiMode.php');

use HiPay\Fullservice\Enum\Transaction\TransactionState;
use HiPay\Fullservice\Enum\Transaction\Operation;
use HiPay\Fullservice\Enum\Transaction\TransactionStatus;

/**
 * Handle Hipay Api call
 *
 * @author      HiPay <support.tpp@hipay.com>
 * @copyright   Copyright (c) 2017 - HiPay
 * @license     https://github.com/hipay/hipay-enterprise-sdk-prestashop/blob/master/LICENSE.md
 * @link    https://github.com/hipay/hipay-enterprise-sdk-prestashop
 */
class Apihandler
{
    private $module;
    private $context;

    public function __construct($moduleInstance, $contextInstance)
    {
        $this->module = $moduleInstance;
        $this->context = $contextInstance;
        $this->configHipay = $this->module->hipayConfigTool->getConfigHipay();
        $this->dbUtils = new HipayDBUtils($this->module);
    }

    /**
     * Handle moto payment request
     *
     * @param type $cart
     */
    public function handleMoto($cart)
    {
        $delivery = new Address((int)$cart->id_address_delivery);
        $deliveryCountry = new Country((int)$delivery->id_country);
        $currency = new Currency((int)$cart->id_currency);
        $params = array();

        $params["method"] = "credit_card";
        $params["moto"] = true;
        $params["iframe"] = false;
        $params["authentication_indicator"] = 0;
        $params["productlist"] = HipayHelper::getCreditCardProductList(
            $this->module,
            $this->configHipay,
            $deliveryCountry,
            $currency
        );

        $this->baseParamsInit($params, true, $cart);

        $this->handleHostedPayment($params, $cart, true);
    }

    /**
     * handle credit card api call
     *
     * @param string $mode
     * @param array $params
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function handleCreditCard($mode = ApiMode::HOSTED_PAGE, $params = array())
    {
        $this->baseParamsInit($params);
        $cart = $this->context->cart;
        $delivery = new Address((int)$cart->id_address_delivery);
        $deliveryCountry = new Country((int)$delivery->id_country);
        $currency = new Currency((int)$cart->id_currency);
        $customer = new Customer((int)$cart->id_customer);

        $params["multi_use"] = !$customer->is_guest && Tools::isSubmit('saveTokenHipay');

        switch ($mode) {
            case ApiMode::DIRECT_POST:
                $params ["paymentmethod"] = $this->getPaymentMethod($params);
                $this->handleDirectOrder($params);
                break;
            case ApiMode::HOSTED_PAGE_IFRAME:
                $params["productlist"] = HipayHelper::getCreditCardProductList(
                    $this->module,
                    $this->configHipay,
                    $deliveryCountry,
                    $currency
                );
                return $this->handleIframe($params);
            case ApiMode::HOSTED_PAGE:
                $params["productlist"] = HipayHelper::getCreditCardProductList(
                    $this->module,
                    $this->configHipay,
                    $deliveryCountry,
                    $currency
                );

                $this->handleHostedPayment($params);
                break;
            default:
                $this->module->getLogs()->logInfos("# Unknown payment mode $mode");
        }
    }

    /**
     * handle all local payment api call
     * @param string $mode
     * @param array $params
     * @return string
     */
    public function handleLocalPayment($mode = ApiMode::HOSTED_PAGE, $params = array())
    {
        $this->baseParamsInit($params, false);

        $params ["paymentmethod"] = $this->getPaymentMethod($params, false);

        switch ($mode) {
            case ApiMode::DIRECT_POST:
                return $this->handleDirectOrder($params);
                break;
            case ApiMode::HOSTED_PAGE:
                return $this->handleHostedPayment($params);
                break;
            case ApiMode::HOSTED_PAGE_IFRAME:
                return $this->handleIframe($params);
                break;
        }
    }

    /**
     * Handle capture request
     *
     * @param $params
     * @return bool
     */
    public function handleCapture($params)
    {
        return $this->handleMaintenance(Operation::CAPTURE, $params);
    }

    /**
     * Handle refund request
     *
     * @param $params
     * @return bool
     */
    public function handleRefund($params)
    {
        return $this->handleMaintenance(Operation::REFUND, $params);
    }

    /**
     * Accept any challenge
     *
     * @param $params
     * @return bool
     */
    public function handleAcceptChallenge($params)
    {
        return $this->handleMaintenance(Operation::ACCEPT_CHALLENGE, $params);
    }

    /**
     * Accept any challenge
     *
     * @param $params
     * @return bool
     */
    public function handleDenyChallenge($params)
    {
        return $this->handleMaintenance(Operation::DENY_CHALLENGE, $params);
    }


    public function handleCancel($params)
    {
        return $this->handleMaintenance(Operation::CANCEL, $params);
    }

    /**
     * handle maintenance request
     *
     * @param $mode
     * @param array $params
     * @return bool
     */
    private function handleMaintenance($mode, $params = array())
    {
        try {
            switch ($mode) {
                case Operation::CAPTURE:
                    $params["operation"] = Operation::CAPTURE;
                    ApiCaller::requestMaintenance($this->module, $params);
                    break;
                case Operation::REFUND:
                    $params["operation"] = Operation::REFUND;
                    ApiCaller::requestMaintenance($this->module, $params);
                    break;
                case Operation::ACCEPT_CHALLENGE:
                    $params["operation"] = Operation::ACCEPT_CHALLENGE;
                    ApiCaller::requestMaintenance($this->module, $params);
                    break;
                case Operation::DENY_CHALLENGE:
                    $params["operation"] = Operation::DENY_CHALLENGE;
                    ApiCaller::requestMaintenance($this->module, $params);
                    break;
                case Operation::CANCEL:
                    $params["operation"] = Operation::CANCEL;
                    $displayMsg = null;
                    $order = new Order($params['order']);

                    if ($order->getCurrentState() == Configuration::get('HIPAY_OS_AUTHORIZED') ||
                        $order->getCurrentState() == Configuration::get('HIPAY_OS_PENDING')) {
                        if ($params['transaction_reference'] !== false) {
                            $hipayDbMaintenance = new HipayDBMaintenance($this->module);

                            // If current transaction status is cancelled, it means we are currently handling the 115 notification from HiPay,
                            // and the transaction is already cancelled
                            if (!$hipayDbMaintenance->isTransactionCancelled($order->id)) {
                                try {
                                    $result = ApiCaller::requestMaintenance($this->module, $params);

                                    if (!in_array($result->getStatus(), array(TransactionStatus::AUTHORIZATION_CANCELLATION_REQUESTED, TransactionStatus::CANCELLED))) {
                                        $displayMsg = $this->module->l("There was an error on the cancellation of the HiPay transaction. You can see and cancel the transaction directly from HiPay's BackOffice");
                                        $displayMsg .= " (https://merchant.hipay-tpp.com/default/auth/login)";
                                        $status = $result->getStatus();
                                        $transactionRef = $result->getTransactionReference();
                                    } else {
                                        HipayOrderMessage::orderMessage($this->module, $order->id, $order->id_customer,
                                            HipayOrderMessage::formatOrderData($this->module, $result));
                                    }
                                } catch (GatewayException $e) {
                                    $errorMsg = array();
                                    $transaction = $hipayDbMaintenance->getTransactionById($order->id);

                                    preg_match("/\\[(.*)\\]/s", $e->getMessage(), $errorMsg);
                                    $displayMsg = $this->module->l("There was an error on the cancellation of the HiPay transaction. You can see and cancel the transaction directly from HiPay's BackOffice");
                                    $displayMsg .= " (https://merchant.hipay-tpp.com/default/auth/login)\n";
                                    $displayMsg .= $this->module->l("Message was : ") . preg_replace("/\r|\n/", "", $errorMsg[0]);

                                    $transactionRef = $transaction['transaction_ref'];
                                    $status = $transaction['status'];
                                }
                            }
                        } else {
                            $displayMsg = $this->module->l("The HiPay transaction was not canceled because no transaction reference exists. You can see and cancel the transaction directly from HiPay's BackOffice");
                            $displayMsg .= " (https://merchant.hipay-tpp.com/default/auth/login)";

                            $transactionRef = "";
                            $status = "";
                        }
                    } else {
                        $displayMsg = $this->module->l("The HiPay transaction was not canceled because it's status doesn't allow cancellation. You can see and cancel the transaction directly from HiPay's BackOffice");
                        $displayMsg .= " (https://merchant.hipay-tpp.com/default/auth/login)";

                        $transactionRef = "";
                        $status = "";
                    }

                    if (!empty($displayMsg)) {
                        HipayOrderMessage::orderMessage($this->module, $order->id, $order->id_customer,
                            HipayOrderMessage::formatErrorOrderData($this->module, $displayMsg, $transactionRef, $status));
                    }

                    break;
                default:
                    $this->module->getLogs()->logInfos("# Unknown maintenance operation");
            }
            return true;
        } catch (GatewayException $e) {
            $errorMessage = $this->module->l('An error occured during request Maintenance.', 'capture');
            $this->context->cookie->__set('hipay_errors', $errorMessage);
            return false;
        } catch (PrestaShopDatabaseException $e) {
        }
    }

    /**
     * Init params send to the api caller
     *
     * @param $params
     * @param bool $creditCard
     * @param bool $cart
     */
    private function baseParamsInit(&$params, $creditCard = true, $cart = false)
    {
        // no basket sent if PS_ROUND_TYPE is ROUND_TOTAL (prestashop config)
        if (Configuration::get('PS_ROUND_TYPE') == Order::ROUND_TOTAL) {
            $params["basket"] = null;
            $params["delivery_informations"] = null;
        } elseif ($creditCard && $this->configHipay["payment"]["global"]["activate_basket"]) {
            $params["basket"] = $this->getCart($cart);
            $params["delivery_informations"] = $this->getDeliveryInformation($cart);
        } elseif ($this->configHipay["payment"]["global"]["activate_basket"] ||
            (isset($params["method"]) &&
                isset($this->configHipay["payment"]["local_payment"][$params["method"]]["basketRequired"])) &&
            $this->configHipay["payment"]["local_payment"][$params["method"]]["basketRequired"]
        ) {
            $params["basket"] = $this->getCart($cart);
            $params["delivery_informations"] = $this->getDeliveryInformation($cart);
        } else {
            $params["basket"] = null;
            $params["delivery_informations"] = null;
        }
    }

    /**
     * return mapped cart
     * @param bool $cart
     * @return json
     */
    private function getCart($cart = false)
    {
        $cart = new CartFormatter($this->module, $cart);

        return $cart->generate();
    }

    /**
     * return mapped delivery informations
     *
     * @param bool $cart
     * @return \HiPay\Fullservice\Gateway\Request\Info\DeliveryShippingInfoRequest
     */
    private function getDeliveryInformation($cart = false)
    {
        $deliveryInformation = new DeliveryShippingInfoFormatter($this->module, $cart);

        return $deliveryInformation->generate();
    }

    /**
     * call Api to get forwarding URL
     *
     * @param $params
     * @param bool $cart
     * @param bool $moto
     */
    private function handleHostedPayment($params, $cart = false, $moto = false)
    {
        try {
            $params['iframe'] = false;
            Tools::redirect(ApiCaller::getHostedPaymentPage($this->module, $params, $cart, $moto));
        } catch (GatewayException $e) {
            $e->handleException();
        }
    }

    /**
     * Return  iframe URL
     * @param $params
     * @return string
     */
    private function handleIframe($params)
    {
        try {
            $params['iframe'] = true;
            return ApiCaller::getHostedPaymentPage($this->module, $params);
        } catch (GatewayException $e) {
            $e->handleException();
        }
    }

    /**
     * call api and redirect to success or error page
     *
     * @param $params
     * @throws PrestaShopException
     */
    private function handleDirectOrder($params)
    {
        $failUrl = $this->context->link->getModuleLink($this->module->name, 'decline', array(), true);
        $pendingUrl = $this->context->link->getModuleLink($this->module->name, 'pending', array(), true);
        $exceptionUrl = $this->context->link->getModuleLink($this->module->name, 'exception', array(), true);

        try {
            $params["paymentProduct"] = $this->module->hipayConfigTool->getPaymentProduct($params["method"]);

            $params["methodDisplayName"] = HipayHelper::getPaymentProductName(
                $params["paymentProduct"],
                $this->module,
                $this->context->language
            );

            $response = ApiCaller::requestDirectPost($this->module, $params);

            $forwardUrl = $response->getForwardUrl();

            switch ($response->getState()) {
                case TransactionState::COMPLETED:
                    $redirectParams = HipayHelper::validateOrder(
                        $this->module,
                        $this->context,
                        $this->context->cart,
                        $params["methodDisplayName"]
                    );

                    Hook::exec('displayHiPayAccepted', array('cart' => $this->context->cart, "order_id" => $redirectParams['id_order']));
                    $redirectUrl = 'index.php?controller=order-confirmation&' . http_build_query($redirectParams);
                    break;
                case TransactionState::PENDING:
                    HipayHelper::validateOrder(
                        $this->module,
                        $this->context,
                        $this->context->cart,
                        $params["methodDisplayName"]
                    );

                    $redirectUrl = $pendingUrl;
                    break;
                case TransactionState::FORWARDING:
                    $redirectUrl = $forwardUrl;
                    break;
                case TransactionState::DECLINED:
                    $reason = $response->getReason();
                    $this->module->getLogs()->logInfos(
                        'There was an error request new transaction: ' . $reason['message']
                    );
                    $redirectUrl = $failUrl;
                    break;
                case TransactionState::ERROR:
                    $reason = $response->getReason();
                    $this->module->getLogs()->logInfos(
                        'There was an error request new transaction: ' . $reason['message']
                    );
                    $redirectUrl = $exceptionUrl;
                    break;
                default:
                    $redirectUrl = $failUrl;
            }

            Tools::redirect($redirectUrl);
        } catch (GatewayException $e) {
            $e->handleException();
        } catch(Exception $e){
            HipayHelper::redirectToExceptionPage($this->context, $this->module);
            die();
        }
    }

    /**
     * return mapped payment method
     *
     * @param $params
     * @param bool $creditCard
     * @return \HiPay\Fullservice\Gateway\Request\PaymentMethod\CardTokenPaymentMethod|mixed
     */
    private function getPaymentMethod($params, $creditCard = true)
    {
        if ($creditCard) {
            $paymentMethod = new CardTokenFormatter($this->module, $params);
        } else {
            $paymentMethod = new GenericPaymentMethodFormatter($this->module, $params);
        }

        return $paymentMethod->generate();
    }
}
