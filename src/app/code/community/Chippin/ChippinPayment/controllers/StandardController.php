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
     * When a customer chooses Chippin on Checkout/Payment page
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
        $this->isHashValid('invited');

        $order = $this->loadOrder();

        $message = sprintf('Invite(s) sent to contributor(s)');
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_INVITED, $message)
            ->save();

        $this->sendJsonResponse();
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
        $this->isContributorHashValid();

        $message = sprintf('Contribution recieved from %s', $this->getEmail());

        $order = $this->loadOrder();
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_CONTRIBUTED, $message)
            ->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($order->getQuoteId());
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(true)->save();

        $this->_redirect('chippin/checkout/contributed', array('_secure' => true));
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
        $this->isHashValid('rejected');

        $orderId = $this->getRequest()->getParam('merchant_order_id');

        $order = $this->loadOrder($orderId);
        $order->hold();
        $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, "Chippin payment has been rejected.", true);
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_REJECTED, 'An unrecoverable error occured');
        $order->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($order->getQuoteId());
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(true)->save();

        $this->_redirect('chippin/checkout/declined', array('_secure' => true));
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
        $this->isHashValid('completed');

        $order = $this->loadOrder();
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_COMPLETED, 'All contributions accepted')
            ->save();

        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($order->getQuoteId());
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
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
        $this->isHashValid('paid');

        $order = $this->loadOrder();

        $payment = $order->getPayment();
        $payment->registerCaptureNotification($order->getGrandTotal(), true);
        $order->save();
        $invoice = $payment->getCreatedInvoice();
        if ($invoice) {
            $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_PAID, $message)
                ->save();

            $order->queueNewOrderEmail()->addStatusHistoryComment(sprintf('Notified customer about invoice #%s.', $invoice->getIncrementId()))
                ->setIsCustomerNotified(true)
                ->save();
        }

        $this->sendJsonResponse();
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
        $this->isHashValid('failed');

        $order = $this->loadOrder();
        if (!$order) {
            throw new Exception('Unable to load the order');
        }
        $message = sprintf('Chippin payment failed.');
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_FAILED, $message)
            ->registerCancellation($message, false)
            ->save();

        $this->sendJsonResponse();
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
        $this->isHashValid('cancelled');

        $session = Mage::getSingleton('checkout/session');
        $order = $this->loadOrder();
        $message = sprintf('Chippin payment cancelled by Instigator');
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_CANCELED, $message)
            ->save();
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
        $this->isHashValid('timed_out');

        $order = $this->loadOrder();
        $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, 'Chippin payment has been rejected.', true);
        $order->addStatusToHistory(Chippin_ChippinPayment_Model_Order::STATUS_TIMEDOUT, 'The Chippin payment timed out', true)
            ->save();

        $order->cancel()->save();

        $this->sendJsonResponse();
    }

    /**
     * Config instance getter
     * @return Chippin_ChippinPayment_Model_Config
     */
    private function getConfig()
    {
        if (null === $this->_config) {
            $this->_config = Mage::getModel('chippinpayment/config');
        }

        return $this->_config;
    }

    private function sendJsonResponse($message = 'Success')
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(sprintf('{"response":"%s"}', $message));
    }

    private function getHmac()
    {
        return $this->getRequest()->getParam('hmac');
    }

    private function getOrderId()
    {
        return $this->getRequest()->getParam('merchant_order_id');
    }

    private function getFirstName()
    {
        return $this->getRequest()->getParam('first_name');
    }

    private function getLastName()
    {
        return $this->getRequest()->getParam('last_name');
    }

    private function getEmail()
    {
        return $this->getRequest()->getParam('email');
    }

    private function loadOrder()
    {
        $orderId = $this->getOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        if (!$order->getId()) {
            throw new Exception(sprintf('Unable to retrieve order with increment_id: %s', $orderId));
        }

        return $order;
    }

    private function isHashValid($callbackKey)
    {
        $orderId = $this->getOrderId();
        $hash = $this->_chippin->generateCallbackHash($callbackKey, $orderId);

        if($hash !== $this->getHmac()) {
            Mage::logException(new Exception(sprintf('HMAC validation failed for order: %s hmac:%s !== hash:%s', $orderId, $hmac, $hash)));
            throw new Exception('HMAC validation failed');
        }
    }

    protected function isContributorHashValid()
    {
        $orderId = $this->getOrderId();
        $first_name = $this->getFirstName();
        $last_name = $this->getLastName();
        $email = $this->getEmail();
        $hmac = $this->getHmac();
        $hash = $this->_chippin->generateContributionHash($orderId, $first_name, $last_name, $email);

        if($hash !== $hmac) {
            Mage::logException(new Exception(sprintf('HMAC validation failed for order: %s hmac:%s !== hash:%s', $orderId, $hmac, $hash)));
            throw new Exception('HMAC validation failed');
        }
    }
}
