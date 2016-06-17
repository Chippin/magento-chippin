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
    protected function getConfig()
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
     * Happens one time at the point first invite(s) to contributors are sent.
     * This is likely point you might wish to reserve stock.
     *
     * Callback Type: Background
     *
     * POST merchant_order_id
     * POST hmac
     *
     * Trigered by: Instigator
     */
    public function  invitedAction()
    {
        $this->isHashValid();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getChippinQuoteId(true));
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(true)->save();

        $order = $this->loadOrder($this->getRequest()->getParam('merchant_order_id'));
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_INVITED, 'Invite(s) sent to contributor(s)');
        $order->save();

        $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }

    /**
    * Occurs after each contributor pages. This is the point to say thank you to the contributor and upsell.
    * Contributor payment has been pre- authed at this point but not collected.
    *
    * Callback Type: Forground redirect
    *
    * POST merchant_order_id
    * POST first_name
    * POST last_name
    * POST email
    * POST hmac
    *
    * Trigered by: Contributor
    */
    public function contributedAction()
    {
        // @TODO apply correct hash validation
        // $this->isHashValid();

        $orderId = $this->getRequest()->getParam('merchant_order_id');
        $contributorEmail = $this->getRequest()->getParam('email');

        $order = $this->loadOrder($orderId);
        $order->addStatusToHistory(
            Chippin_ChippinPayment_Model_Order::STATUS_CONTRIBUTED,
            sprintf('Contribution recieved from %s', $contributorEmail)
        );
        $order->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($order->getQuoteId());
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(true)->save();

        $this->_redirect('checkout/onepage/success', array('_secure' => true));

    }

    /**
    * Occurs if a contributor rejects an invitation to contribute.
    *
    * Callback Type: Forground redirect
    *
    * POST merchant_order_id
    * POST hmac
    *
    * Trigered by: Contributor
    */
    public function rejectedAction()
    {
        $this->isHashValid();

        $orderId = $this->getRequest()->getParam('merchant_order_id');

        $order = $this->loadOrder($orderId);
        $order->hold();
        $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, "Chippin payment has been rejected.", true);
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_REJECTED, 'An unrecoverable error occured');
        $order->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($order->getQuoteId());
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

        $this->_redirect('checkout/onepage/failure', array('_secure' => true));
    }

    /**
    * Occurs when instigator manually completes a chippin. This does not mean the money has been taken yet. Corresponding
    * merchant page should say something along the lines of “Thank you for completing your chippin, we will email
    * you order completion details shortly.”
    *
    * Callback Type: Forground redirect
    *
    * POST merchant_order_id
    * POST hmac
    *
    * Trigered by: Instigator
    */
    public function completedAction()
    {
        $this->isHashValid();

        $orderId = $this->getRequest()->getParam('merchant_order_id');

        $order = $this->loadOrder($orderId);
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_COMPLETED, 'All contributions accepted');
        $order->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($order->getQuoteId());
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

        $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }

    /**
    * All payments have been taken, the order is complete and merchant should email user details of their order.
    *
    * Callback Type: Background
    *
    * POST merchant_order_id
    * POST hmac
    *
    * Trigered by: n/a
    */
    public function paidAction()
    {
        $this->isHashValid();
    }

    /**
    * Occurs if there is an error that can not be recovered. All payments/pre-auths are refunded
    * by Chippin. This should rarely happen, but if it does you should update user about order status
    * and probably provide them an alternative payment method or suggest re- placing the order.
    *
    * Callback Type: Background
    *
    * POST merchant_order_id
    * POST hmac
    *
    * Trigered by: n/a
    */
    public function failedAction()
    {
        $this->isHashValid();
    }

    /**
    * Occurs if the instigator chooses to cancel the ‘chippin’. At this point you should probably indicate order has
    * not been completed and probably provide them an alternative payment method or suggest re- placing the order.
    *
    * Callback Type: Forground redirect
    *
    * POST merchant_order_id
    * POST hmac
    *
    * Trigered by: Instigator
    */
    public function canceledAction()
    {
        $this->isHashValid();

        $session = Mage::getSingleton('checkout/session');
        $order = $this->loadOrder($this->getRequest()->getParam('merchant_order_id'));
        if ($order->getId()) {
            $order->cancel()->setStatus(Chippin_ChippinPayment_Model_Order::STATUS_CANCELED)->save();
        }
        Mage::helper('chippinpayment/checkout')->restoreQuote($order);

        $this->_redirect('checkout/cart');
    }

    /**
    * Occurs if the ‘chippin’ times out (the duration of time allowed to complete has passed). At this point,
    * you should probably release stock and email the customer to inform them to re- place the order.
    *
    * Callback Type: Background
    *
    * POST merchant_order_id
    * POST hmac
    *
    * Trigered by: n/a
    */
    public function timedoutAction()
    {
        $this->isHashValid();

        $order = $this->loadOrder($this->getRequest()->getParam('merchant_order_id'));
        $order->cancel();
        $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, "Chippin payment timed out.", true);
        $order->setStatus(Chippin_ChippinPayment_Model_Order::STATUS_TIMEDOUT);
        $order->save();

        $this->_redirect('checkout/onepage/failure', array('_secure' => true));
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


        if($hash !== $this->_chippin->generateCallbackHash($orderId)) {
            throw new Exception('HMAC validation failed');
        }
    }
}
