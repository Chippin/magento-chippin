<?php
class Chippin_ChippinPayment_StandardController extends Mage_Core_Controller_Front_Action {

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

    public function canceledAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getChippinQuoteId(true));
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }
            Mage::helper('chippinpayment/checkout')->restoreQuote();
        }
        $this->_redirect('checkout/cart');
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

        $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }
}
