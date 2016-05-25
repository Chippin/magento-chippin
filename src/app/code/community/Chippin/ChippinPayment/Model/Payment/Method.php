<?php

class Chippin_ChippinPayment_Model_Payment_Method extends Mage_Payment_Model_Method_Abstract {

    protected $_code  = 'chippinpayment';
    protected $_formBlockType = 'chippinpayment/form_payment';
    protected $_infoBlockType = 'chippinpayment/info_payment';

    /**
     * Payment Method features
     * @var bool
     */
    // protected $_isGateway                   = false;
    protected $_canOrder                    = true;
    protected $_canAuthorize                = true;
    protected $_canCapture                  = true;
    // protected $_canCapturePartial           = false;
    // protected $_canCaptureOnce              = false;
    // protected $_canRefund                   = false;
    // protected $_canRefundInvoicePartial     = false;
    // protected $_canVoid                     = false;
    protected $_canUseInternal              = false;
    protected $_canUseCheckout              = true;
    protected $_canUseForMultishipping      = false;
    // protected $_isInitializeNeeded          = false;
    // protected $_canFetchTransactionInfo     = false;
    // protected $_canReviewPayment            = false;
    // protected $_canCreateBillingAgreement   = false;
    protected $_canManageRecurringProfiles  = false;

    public function validate()
    {
        parent::validate();
        $info = $this->getInfoInstance();

        if (!$info->getCustomFieldOne())
        {
            $errorCode = 'invalid_data';
            $errorMsg = $this->_getHelper()->__("CustomFieldOne is a required field.\n");
        }

        if (!$info->getCustomFieldTwo())
        {
            $errorCode = 'invalid_data';
            $errorMsg .= $this->_getHelper()->__('CustomFieldTwo is a required field.');
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
