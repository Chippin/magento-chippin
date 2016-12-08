<?php

use Chippin\SDK\Merchant;
use Chippin\SDK\Chippin;

class Chippin_ChippinPayment_Model_Payment_Method extends Mage_Payment_Model_Method_Abstract {

    protected $_code  = Chippin_ChippinPayment_Model_Config::METHOD_CODE;
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

    protected $_chippin;

    public function __construct($params = array())
    {
        parent::__construct($params);
        $this->_chippin = new Chippin(new Merchant($this->getConfig()->getMerchantId(), $this->getConfig()->getSecret()));
    }
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
        $status = $this->getConfig()->getNewOrderStatus();
        $status = $status? $status : Chippin_ChippinPayment_Model_Order::STATUS_NEW;
        $stateObject->setStatus($status);
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
            'grace_period' => $this->getConfig()->getGracePeriod(),
            'currency_code' => $currencyCode,
            'hmac' => '',
            'products' => array()
        ));

        //Retrieve items from the quote.
        $items = $order->getItemsCollection()->getItems();
        foreach($items as $item) {

            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getTypeId() == 'simple' ) {
                 array_push($fields['products'], array(
                    'label' => $item->getName(),
                    'image' => $product->getImageUrl(),
                    'amount' => intval($item->getRowTotal() * 100)
                ));
            }
        }

        return $fields;
    }

    private function decorateWithHash($data)
    {
        $data['hmac'] = $this->_chippin->generateOrderHash($data);

        return $data;
    }

}
