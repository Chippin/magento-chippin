<?php

use Chippin\SDK\Merchant;
use Chippin\SDK\Chippin;

class Chippin_ChippinPayment_StandardController extends Mage_Core_Controller_Front_Action {

    protected $_chippin;

    public function _construct()
    {
        parent::_construct();
        $this->_chippin = new Chippin(new Merchant($this->getConfig()->getMerchantId(), $this->getConfig()->getSecret()));
    }

    /**
     * Config instance
     * @var Chippin_ChippinPayment_Model_Config
     */
    protected $_config = null;

    /**
     * Config instance getter
     * @return Chippin_ChippinPayment_Model_Config
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $this->_config = Mage::getModel('chippinpayment/config');
        }

        return $this->_config;
    }

    /**
     * When a customer chooses Paypal on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setChippinQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('chippinpayment/redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
    * Occurs if the instigator chooses to cancel the ‘chippin’. At this point you should probably indicate order has
    * not been completed and probably provide them an alternative payment method or suggest re- placing the order.
    *
    * @param merchant_order_id
    * @param hmac
    */
    public function cancelledAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getChippinQuoteId(true));
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->setStatus('chippin_cancelled')->save();
            }
            Mage::helper('chippinpayment/checkout')->restoreQuote();
        }
        $this->_redirect('checkout/cart');
    }

    public function completedAction()
    {
        if (!$this->isHashValid()) {
            Throw new Exception('HMAC validation failed');
        }
    }

    public function contributedAction()
    {
        // if (!$this->isHashValid()) {
        //     Throw new Exception('HMAC validation failed');
        // }

        $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }

    public function failedAction()
    {
        if (!$this->isHashValid()) {
            Throw new Exception('HMAC validation failed');
        }
    }

    /**
     * when Chippin returns
     *
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get all the contributions.
     */
    public function  invitedAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getChippinQuoteId(true));
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

        $order = $this->loadOrder($this->getRequest()->getParam('merchant_order_id'));
        $order->setStatus('chippin_pending_completion');
        $order->save();

        $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }

    /**
    * Occurs if there is an error that can not be recovered. All payments/pre-auths are refunded by Chippin.
    * This should rarely happen, but if it does you should update user about order status and probably provide
    * them an alternative payment method or suggest re- placing the order.
    *
    * @param merchant_order_id
    * @param hmac
    */
    public function rejectedAction()
    {
        if (!$this->isHashValid()) {
            Throw new Exception('HMAC validation failed');
        }

        $order = $this->loadOrder($this->getRequest()->getParam('merchant_order_id'));
        $order->hold();
        $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, "Chippin payment has been rejected.", true);
        $order->setStatus('chippin_rejected');
        $order->save();
    }

    /**
    * Occurs if the ‘chippin’ times out (the duration of time allowed to complete has passed). At this point,
    * you should probably release stock and email the customer to inform them to re- place the order.
    *
    * @param merchant_order_id
    * @param hmac
    */
    public function timedoutAction()
    {
        if (!$this->isHashValid()) {
            Throw new Exception('HMAC validation failed');
        }

        $order = $this->loadOrder($this->getRequest()->getParam('merchant_order_id'));
        $order->hold();
        $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, "Chippin payment timed out.", true);
        $order->setStatus('chippin_timedout');
        $order->save();
    }

    protected function loadOrder($orderId)
    {
        $order = Mage::getSingleton('sales/order');
        return $order->load($orderId);
    }

    protected function isHashValid()
    {
        $orderId = $this->getRequest()->getParam('merchant_order_id');
        $hash = $this->getRequest()->getParam('hmac');


        if($hash === $this->_chippin->generateCallbackHash($orderId)){
            return true;
        }
        return false;
    }
}
