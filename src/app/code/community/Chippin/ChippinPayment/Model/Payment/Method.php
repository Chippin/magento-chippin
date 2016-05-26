<?php

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
    // protected $_isInitializeNeeded          = false;
    protected $_canManageRecurringProfiles  = false;

    public function validate()
    {
        parent::validate();
        $info = $this->getInfoInstance();

        if (!$info->getFirstName())
        {
            $errorCode = 'invalid_data';
            $errorMsg = $this->_getHelper()->__("First name is a required field.\n");
        }

        if (!$info->getLastName())
        {
            $errorCode = 'invalid_data';
            $errorMsg .= $this->_getHelper()->__('Last name is a required field.');
        }

        if (!$info->getEmail())
        {
            $errorCode = 'invalid_data';
            $errorMsg .= $this->_getHelper()->__('Email is a required field.');
        }

        if ($errorMsg)
        {
            Mage::throwException($errorMsg);
        }

        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('chippin/payment/redirect', array('_secure' => false));
    }
}
