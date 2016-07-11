<?php

class Chippin_ChippinPayment_CheckoutController extends Mage_Core_Controller_Front_Action {

   /**
     * Config instance
     * @var Chippin_ChippinPayment_Model_Config
     */
    protected $_config = null;

    public function contributedAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function declinedAction()
    {
        $this->loadLayout();
        $this->renderLayout();
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
}
