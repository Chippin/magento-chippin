<?php

use Chippin\SDK\Merchant;
use Chippin\SDK\Chippin;

class Chippin_ChippinPayment_Model_Payment_Method extends Mage_Payment_Model_Method_Abstract {

    protected $_code  = 'chippinpayment';
    protected $_formBlockType = 'chippinpayment/form_payment';
    // protected $_infoBlockType = 'chippinpayment/info_payment';

    /**
     * Payment Method features
     * @var bool
     */
    protected $_canUseInternal              = false;
    protected $_canUseCheckout              = true;
    protected $_canUseForMultishipping      = false;
    protected $_isInitializeNeeded          = true;
    protected $_canManageRecurringProfiles  = false;

    /**
     * Config instance
     * @var Chippin_ChippinPayment_Model_Config
     */
    protected $_config = null;

    /**
     * Interface for chippin creation specific fields
     * @var array
     */
    protected $_chippinFields = array(
        'merchant_id', 'merchant_order_id', 'total_amount', 'first_name', 'last_name', 'email', 'duration', 'currency_code', 'hmac', 'products[]'
    );

    /**
     * Interface for chippin product specific fields
     * @var array
     */
    protected $_chippinProductFields = array(
        'label', 'image', 'amount'
    );

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('chippin/standard/redirect', array('_secure' => true));
    }

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param Varien_Object
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('chippin_pending_payment');
        $stateObject->setIsNotified(false);

        return $this;
    }


    /**
     * Config instance getter
     * @return Chippin_ChippinPayment_Model_Config
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $params = array($this->_code);
            if ($store = $this->getStore()) {
                $params[] = is_object($store) ? $store->getId() : $store;
            }
            $this->_config = Mage::getModel('chippinpayment/config', $params);
        }

        return $this->_config;
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return form field array
     *
     * @return array
     */
    public function getCheckoutFormFields()
    {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        $currencyCode = strtolower($order->getBaseCurrencyCode());
        $isOrderVirtual = $order->getIsVirtual();

        $orderGrandTotal = intval($order->getData('grand_total') * 100);
        $subTotal = intval($order->getSubtotal() * 100);
        $shippingHandling = $grandTotal - $subTotal;

        $fields =  $this->decorateWithHash(array(
            'merchant_id' => $this->getConfig()->getMerchantId(),
            'merchant_order_id' => $orderIncrementId,
            'total_amount' => $orderGrandTotal,
            'first_name' => $order->getCustomerFirstname(),
            'last_name' => $order->getCustomerLastname(),
            'email' => $order->getCustomerEmail(),
            'duration' => $this->getConfig()->getDuration(),
            'currency_code' => $currencyCode,
            'hmac' => '',
            'products' => array()
        ));

        //Retrieve items from the quote.
        $items = $order->getItemsCollection()->getItems();
        foreach($items as $item) {

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            array_push($fields['products'], array(
                'label' => $item->getName(),
                'image' => $product->getImageUrl(),
                'amount' => intval($item->getRowTotal() * 100)
            ));
        }

        //Add Shipping as line item so total matches magento's charge.
        if($shippingHandling > 0) {
            array_push($fields['products'][], array(
                'label' => 'Shipping and Handling',
                'image' => null,
                'amount' => $shippingHandling
            ));
        }

        return $fields;
    }

    private function decorateWithHash($data)
    {
        $data['hmac'] = $this->generateOrderHash($data, $this->getConfig()->getSecret());

        return $data;
    }

    /**
    * merchant_secret + merchant_id + merchant_order_id + total_amount + duration + currency_code
    *
    */
    private function generateOrderHash($data, $secret)
    {
        $hashParts =  array();
        $hashParts[] = $data['merchant_id'];
        $hashParts[] = $data['merchant_order_id'];
        $hashParts[] = $data['total_amount'];
        $hashParts[] = $data['duration'];
        $hashParts[] = $data['currency_code'];

        return $this->generateHash(join($hashParts), $secret);
    }

    private function generateHash($string, $secret)
    {
        return hash_hmac('sha256', $string, $secret);
    }


//     /**
//  *
//  * Extract cart/quote details and send to api.
//  * Respond with token
//  * @throws Mage_Exception
//  * @throws Exception
//  */
// protected  function _customBeginPayment(){
//     //Retrieve cart/quote information.
//     $sessionCheckout = Mage::getSingleton('checkout/session');
//     $quoteId = $sessionCheckout->getQuoteId();
//     $sessionCheckout->setData('chippinQuoteId',$quoteId);
//
//     $quote = Mage::getModel("sales/quote")->load($quoteId);
//     $grandTotal = $quote->getData('grand_total');
//     $subTotal = $quote->getSubtotal();
//     $shippingHandling = ($grandTotal-$subTotal);
//
//     $billingData = $quote->getBillingAddress()->getData();
//
//     $apiEmail = $billingData['email'];
//
//     //Retrieve items from the quote.
//     $items = $quote->getItemsCollection()->getItems();
//     $productsArray = array();
//     foreach($items as $item){
//         $productsArray[] = $item->getName();
//         $productsArray[] = 'http://purelinemedical.com/wp-content/uploads/2015/10/demo-prod-grey_1.png';
//         $productsArray[] = $item->getPrice() * $item->getQty();
//     }
//
//     //Add Shipping as line item so total matches magento's charge.
//     if($shippingHandling > 0){
//         $productsArray[] = 'Shipping and Handling';
//         $productsArray[] = null;
//         $productsArray[] = $shippingHandling;
//     }
//
//     // Build urls back to our modules controller actions as required by the api.
//     // $oUrl = Mage::getModel('core/url');
//     // $apiHrefSuccess = $oUrl->getUrl("mockpay/standard/success");
//     // $apiHrefFailure = $oUrl->getUrl("mockpay/standard/failure");
//     // $apiHrefCancel = $oUrl->getUrl("mockpay/standard/cancel");
//
//
//
//     return $this;
// }

    // public function validate()
    // {
    //     parent::validate();
    //     $info = $this->getInfoInstance();
    //
    //     if (!$info->getFirstName())
    //     {
    //         $errorCode = 'invalid_data';
    //         $errorMsg = $this->_getHelper()->__("First name is a required field.\n");
    //     }
    //
    //     if (!$info->getLastName())
    //     {
    //         $errorCode = 'invalid_data';
    //         $errorMsg .= $this->_getHelper()->__('Last name is a required field.');
    //     }
    //
    //     if (!$info->getEmail())
    //     {
    //         $errorCode = 'invalid_data';
    //         $errorMsg .= $this->_getHelper()->__('Email is a required field.');
    //     }
    //
    //     if ($errorMsg)
    //     {
    //         Mage::throwException($errorMsg);
    //     }
    //
    //     return $this;
    // }
}
